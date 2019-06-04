<?php

namespace Cryozonic\StripeSubscriptions\Helper;

use Cryozonic\StripePayments\Helper\Logger;
use Magento\Framework\Pricing\PriceCurrencyInterface;

class Generic
{
    public function __construct(
        \Cryozonic\StripePayments\Model\Rollback $rollback,
        \Cryozonic\StripePayments\Helper\Generic $paymentsHelper,
        \Cryozonic\StripePayments\Model\Config $config,
        \Cryozonic\StripePayments\Model\StripeCustomer $stripeCustomer,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->rollback = $rollback;
        $this->paymentsHelper = $paymentsHelper;
        $this->config = $config;
        $this->_stripeCustomer = $stripeCustomer;
        $this->priceCurrency = $priceCurrency;
        $this->eventManager = $eventManager;
    }

    public function createSubscriptions($order, $isDryRun = false, $trialEnd = null)
    {
        $this->_trialAmount = 0;
        $this->_initialFee = 0;
        $this->_isDryRun = $isDryRun;

        // Get all the products on the order
        $items = $order->getAllItems();
        foreach ($items as $item)
        {
            $product = $this->paymentsHelper->loadProductById($item->getProductId());
            if ($product->getCryozonicSubEnabled())
            {
                try
                {
                    $this->createSubscriptionForProduct($product, $order, $item, $trialEnd);
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
                    // We get a \Stripe\Error\InvalidRequest if the customer is purchasing a subscription with a currency
                    // that is different from the currency they used for previous subscription purposes
                    $message = $e->getMessage();
                    if (preg_match('/with currency (\w+)$/', $message, $matches))
                    {
                        $currency = strtoupper($matches[1]);
                        $this->rollback->run("Your account has been configured to use a different currency. Please complete the purchase in the currency: $currency", $e);
                    }
                    else
                        $this->rollback->run("Sorry, we could not create the subscription for " . $product->getName() . ". Please contact us for more help.", $e);
                }
            }
        }

        return array("trialAmount" => $this->_trialAmount, "initialFee" => $this->_initialFee);
    }

    public function createSubscriptionForProduct($product, $order, $item, $trialEnd = null)
    {
        // Get billing interval and billing period
        $interval = $product->getCryozonicSubInterval();
        $intervalCount = $product->getCryozonicSubIntervalCount();

        if (!$interval)
            throw new \Exception(__("An interval period has not been specified for the subscription"));

        if (!$intervalCount)
            $intervalCount = 1;

        // If it is a configurable product, switch to the parent item
        if ($item->getPrice() == 0 && $item->getParentItem() &&
            $item->getParentItem()->getProductType() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE)
        {
            $item = $item->getParentItem();
        }

        // Get the subscription currency and amount
        if ($this->config->useStoreCurrency())
        {
            $amount = $item->getPriceInclTax();
            $currency = $order->getOrderCurrencyCode();
            if (empty($currency))
                $currency = $order->getQuoteCurrencyCode(); // Dry run scenario
        }
        else
        {
            $amount = $item->getBasePriceInclTax();
            $currency = $order->getBaseCurrencyCode();
        }

        $trialDays = 0;
        if (!$trialEnd)
        {
            $trialDays = $product->getCryozonicSubTrial();
            if (!empty($trialDays) && is_numeric($trialDays) && $trialDays > 0)
            {
                $this->_trialAmount += $amount;
                $trialEnd = strtotime("+$trialDays days");
            }
        }

        $initialFee = $product->getCryozonicSubInitialFee();
        if (!empty($initialFee) && is_numeric($initialFee) && $initialFee > 0)
        {
            $this->_initialFee += $initialFee;
        }

        if ($this->_isDryRun)
            return;

        $cents = 100;
        if ($this->paymentsHelper->isZeroDecimal($currency))
            $cents = 1;

        $amount = round($amount * $cents);

        // Generate the plan if it doesn't exist
        $params = [
            'amount' => $amount . $currency,
            'frequency' => $intervalCount . strtoupper($interval) . ($intervalCount > 1 ? 'S' : ''),
            'product' => $product->getId(),
        ];
        $planId = implode('-', $params);
        $plan = $this->getSubscriptionPlan($planId);
        if (!$plan)
        {
            $plan = \Stripe\Plan::create(array(
              "amount" => $amount,
              "interval" => $interval,
              "interval_count" => $intervalCount,
              "name" => $product->getName(),
              "currency" => $currency,
              "id" => $planId
            ));
        }

        // Build the metadata for this subscription - the customer will be able to edit these in the future
        $metadata = [
            "Product ID" => $product->getId(),
            "Customer ID" => $this->_stripeCustomer->getCustomerId(),
            "Order #" => $order->getIncrementId(),
            "Module" => \Cryozonic\StripePayments\Model\Config::$moduleName . " v" . \Cryozonic\StripePayments\Model\Config::$moduleVersion
        ];
        $shipping = $this->paymentsHelper->getAddressFrom($order);
        if ($shipping)
        {
            $metadata["Shipping First Name"] = $shipping["firstname"];
            $metadata["Shipping Last Name"] = $shipping["lastname"];
            $metadata["Shipping Company"] = $shipping["company"];
            $metadata["Shipping Street"] = $shipping["street"];
            $metadata["Shipping City"] = $shipping["city"];
            $metadata["Shipping Region"] = $shipping["region"];
            $metadata["Shipping Postcode"] = $shipping["postcode"];
            $metadata["Shipping Country"] = $shipping["country_id"];
            $metadata["Shipping Telephone"] = $shipping["telephone"];
        }

        if ($trialDays > 0)
            $metadata["Trial"] = "$trialDays days";

        // Event to collect additional metadata, use this in your own local module
        $returnObject = new \Magento\Framework\DataObject();
        $this->eventManager->dispatch('cryozonic_stripesubscriptions_metadata', array(
            'product' => $product,
            'order' => $order,
            'item' => $item,
            'metadata' => $metadata,
            'returnObject' => $returnObject
        ));

        foreach ((array) $returnObject->getMetadata() as $key => $value)
            $metadata[$key] = $value;

        // Subscribe the customer to the plan
        $params = [
          "customer" => $this->_stripeCustomer->getStripeId(),
          "plan" => $planId,
          "trial_end" => ($trialEnd ? $trialEnd : strtotime("+$intervalCount $interval")),
          "quantity" => round($item->getQtyOrdered()),
          "metadata" => $metadata
        ];
        $subscription = \Stripe\Subscription::create($params);
        $this->rollback->addSubscription($subscription);

        $order->addStatusHistoryComment("Subscribed to {$this->formatSubscriptionName($subscription)} ({$subscription->id})");
    }

    public function getSubscriptionPlan($planId)
    {
        try
        {
            return \Stripe\Plan::retrieve($planId);
        }
        catch (\Exception $e) {}

        return null;
    }

    public function isAdminSubscriptionSwitch($data)
    {
        return (is_array($data['subscription']) &&
            isset($data['subscription']['switch']) &&
            $data['subscription']['switch'] == 'switch');
    }

    public function formatSubscriptionName($sub)
    {
        if (empty($sub)) return "Unknown subscription";

        $name = $sub->plan->name;

        $currency = $sub->plan->currency;
        $precision = PriceCurrencyInterface::DEFAULT_PRECISION;
        $cents = 100;
        $qty = '';

        if ($this->paymentsHelper->isZeroDecimal($currency))
        {
            $cents = 1;
            $precision = 0;
        }

        $amount = $sub->plan->amount / $cents;

        if ($sub->quantity > 1)
        {
            $qty = " x " . $sub->quantity;
        }

        $this->priceCurrency->getCurrency()->setCurrencyCode($currency);
        $cost = $this->priceCurrency->format($amount, false, $precision);

        return "$name ($cost$qty)";
    }

}
