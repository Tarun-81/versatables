<?php

namespace Cryozonic\StripePayments\Helper;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\CouldNotSaveException;
use Cryozonic\StripePayments\Model;
use Cryozonic\StripePayments\Model\PaymentMethod;
use Cryozonic\StripePayments\Model\Config;
use Psr\Log\LoggerInterface;
use Magento\Framework\Validator\Exception;
use Cryozonic\StripePayments\Helper\Logger;

class Api
{
    protected $_trialAmount;

    public function __construct(
        \Cryozonic\StripePayments\Model\Config $config,
        LoggerInterface $logger,
        Generic $helper,
        \Cryozonic\StripePayments\Model\StripeCustomer $customer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Cryozonic\StripePayments\Model\Rollback $rollback
    ) {
        $this->logger = $logger;
        $this->helper = $helper;
        $this->config = $config;
        $this->_stripeCustomer = $customer;
        $this->_eventManager = $eventManager;
        $this->rollback = $rollback;
    }

    public function retrieveCharge($token)
    {
        if (strpos($token, 'pi_') === 0)
        {
            $pi = \Stripe\PaymentIntent::retrieve($token);
            return $pi->charges->data[0];
        }

        return \Stripe\Charge::retrieve($token);
    }

    public function createToken($params)
    {
        // If the card is already a token, such as from Stripe.js, then don't create a new token
        if (is_string($params['card']) && strpos($params['card'], 'tok_') === 0) return $params['card'];

        try
        {
            $params['card'] = $this->helper->getAvsFields($params['card']);

            $this->validateParams($params);

            $token = \Stripe\Token::create($params);

            if (empty($token['id']) || strpos($token['id'],'tok_') !== 0)
                throw new Exception(__('Sorry, this payment method can not be used at the moment. Try again later.'));

            $this->_stripeCustomer->setCustomerCard($token['card']);

            return $token['id'];
        }
        catch (\Stripe\Error\Card $e)
        {
            $this->helper->dieWithError($e->getMessage(), $e);
        }
        catch (\Stripe\Error $e)
        {
            $this->helper->dieWithError($e->getMessage(), $e);
        }
        catch (\Exception $e)
        {
            if ($this->helper->isAdmin())
                $this->helper->dieWithError($e->getMessage(), $e);
            else if ($this->helper->maskError($e->getMessage()))
                $this->helper->dieWithError(__($this->helper->maskError($e->getMessage())), $e);
            else
                $this->helper->dieWithError(__("Sorry, we could not complete the checkout process. Please contact us for more help."), $e);
        }
    }

    public function validateParams($params)
    {
        if (is_array($params) && isset($params['card']) && is_array($params['card']) && empty($params['card']['number']))
            throw new \Exception("Unable to use Stripe.js, please see https://store.cryozonic.com/documentation/magento-2-stripe-payments#stripejs");
    }

    public function getStripeParamsFrom($order)
    {
        if ($this->config->useStoreCurrency())
        {
            $amount = $order->getGrandTotal();
            $currency = $order->getOrderCurrencyCode();
        }
        else
        {
            $amount = $order->getBaseGrandTotal();
            $currency = $order->getBaseCurrencyCode();
        }

        $cents = 100;
        if ($this->helper->isZeroDecimal($currency))
            $cents = 1;

        $metadata = [
            "Module" => Config::module(),
            "Order #" => $order->getIncrementId()
        ];
        if ($order->getCustomerIsGuest())
        {
            $customer = $this->helper->getGuestCustomer($order);
            $customerName = $customer->getFirstname() . ' ' . $customer->getLastname();
            $metadata["Guest"] = "Yes";
        }
        else
            $customerName = $order->getCustomerName();

        $params = array(
          "amount" => round($amount * $cents),
          "currency" => $currency,
          "description" => "Order #".$order->getRealOrderId().' by '.$customerName,
          "metadata" => $metadata
        );

        if ($this->config->isReceiptEmailEnabled() && $this->helper->getCustomerEmail())
            $params["receipt_email"] = $this->helper->getCustomerEmail();

        return $params;
    }

    public function createCharge($payment, $amount, $capture, $useSavedCard = false)
    {
        try
        {
            $order = $payment->getOrder();

            $switchSubscription = $payment->getAdditionalInformation('switch_subscription');

            if ($switchSubscription)
            {
                $this->_eventManager->dispatch('cryozonic_switch_subscription', array(
                    'payment' => $payment,
                    'order' => $order,
                    'switchSubscription' => $switchSubscription
                ));
                return;
            }
            else if ($useSavedCard) // We are coming here from the admin, capturing an expired authorization
            {
                $customer = $this->_stripeCustomer->loadFromPayment($payment);
                $token = $this->_stripeCustomer->getDefaultSavedCardFrom($payment);
                $this->customerStripeId = $this->_stripeCustomer->getStripeId();

                if (!$token || !$this->customerStripeId)
                    throw new \Exception('The authorization has expired and the customer has no saved cards to re-create the order.');
            }
            else
            {
                $token = $this->getToken($payment);

                if ($this->helper->hasSubscriptions())
                {
                    // Ensure that a customer exists in Stripe (may be the case with Guest checkouts)
                    if (!$this->_stripeCustomer->getStripeId())
                    {
                        try
                        {
                            $this->_stripeCustomer->createStripeCustomer($order);

                            // We need a saved card for subscriptions
                            if (strpos($token, 'card_') !== 0)
                            {
                                $card = $this->_stripeCustomer->addSavedCard($token);

                                if ($card)
                                    $token = $card->id;
                            }
                        }
                        catch (\Cryozonic\StripePayments\Exception\SilentException $e)
                        {
                            return;
                        }
                    }
                }
            }

            $params = $this->getStripeParamsFrom($order);

            $params["source"] = $token;
            $params["capture"] = $capture;

            // If this is a 3D Secure charge, pass the customer id
            if ($payment->getAdditionalInformation('customer_stripe_id'))
            {
                $params["customer"] = $payment->getAdditionalInformation('customer_stripe_id');
            }
            else if ($this->_stripeCustomer->getStripeId())
            {
                $params["customer"] = $this->_stripeCustomer->getStripeId();
                $payment->setAdditionalInformation('customer_stripe_id', $this->_stripeCustomer->getStripeId());
            }

            $this->validateParams($params);

            if ($this->config->getSecurityMethod() < 1)
                unset($params['customer']);

            $amount = $params['amount'];
            $currency = $params['currency'];
            $cents = 100;
            if ($this->helper->isZeroDecimal($currency))
                $cents = 1;

            $returnData = new \Magento\Framework\DataObject();
            $returnData->setAmount($amount);
            $returnData->setParams($params);
            $returnData->setCents($cents);
            $returnData->setIsDryRun(false);

            $this->_eventManager->dispatch('cryozonic_create_subscriptions', array(
                'order' => $order,
                'returnData' => $returnData
            ));

            $params = $returnData->getParams();

            $fraud = false;

            $statementDescriptor = $this->config->getStatementDescriptor();
            if (!empty($statementDescriptor))
                $params["statement_descriptor"] = $statementDescriptor;

            if ($params["amount"] > 0)
            {
                if ($this->helper->isMultiShipping() && $this->config->shouldUse3DSecure($params['source'], $this->_stripeCustomer))
                {
                    $message = "This card cannot be used for multi-shipping orders because it requires 3D Secure authentication. " .
                        "Please place your order on its own for this shipping address, or use a different card.";
                    throw new \Stripe\Error\Card($message, null, 400, 400, null, null);
                }

                $charge = \Stripe\Charge::create($params);
                $this->rollback->addCharge($charge);

                if ($this->config->isStripeRadarEnabled() &&
                    isset($charge->outcome->type) &&
                    $charge->outcome->type == 'manual_review')
                {
                    $payment->setAdditionalInformation("stripe_outcome_type", $charge->outcome->type);
                }

                if (!$charge->captured && $this->config->isAutomaticInvoicingEnabled())
                {
                    $payment->setIsTransactionPending(true);
                    $invoice = $order->prepareInvoice();
                    $invoice->register();
                    $order->addRelatedObject($invoice);
                }

                $payment->setTransactionId($charge->id);
                $payment->setLastTransId($charge->id);
            }

            $payment->setIsTransactionClosed(0);
            $payment->setIsFraudDetected($fraud);
        }
        catch (\Stripe\Error\Card $e)
        {
            $this->rollback->run($e->getMessage(), $e);
        }
        catch (\Stripe\Error $e)
        {
            $this->rollback->run($e->getMessage(), $e);
        }
        catch (\Exception $e)
        {
            if ($this->helper->isAdmin())
                $this->rollback->run($e->getMessage(), $e);
            else
                $this->rollback->run(null, $e);
        }
    }

    public function getToken($info)
    {
        $token = $info->getAdditionalInformation('token');

        // Is this a saved card?
        if (strpos($token,'card_') === 0)
            return $token;

        // Are we coming from the back office?
        if (strstr($token,'tok_') === false &&
            strstr($token,'tdsrc_') === false &&
            strstr($token,'src_') === false
            )
        {
            $params = $this->getInfoInstanceCard($info);
            $token = $this->createToken($params);
        }

        return $token;
    }

    public function getInfoInstanceCard($info)
    {
        return array(
            "card" => array(
                "name" => $info->getCcOwner(),
                "number" => $info->getCcNumber(),
                "cvc" => $info->getCcCid(),
                "exp_month" => $info->getCcExpMonth(),
                "exp_year" => $info->getCcExpYear()
            )
        );
    }
}
