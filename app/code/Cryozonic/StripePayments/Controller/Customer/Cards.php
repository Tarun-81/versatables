<?php

namespace Cryozonic\StripePayments\Controller\Customer;

use Cryozonic\StripePayments\Helper\Logger;

class Cards extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $session,
        \Cryozonic\StripePayments\Model\Config $config,
        \Cryozonic\StripePayments\Helper\Generic $helper,
        \Cryozonic\StripePayments\Model\StripeCustomer $stripeCustomer
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->config = $config;
        $this->helper = $helper;
        $this->stripeCustomer = $stripeCustomer;

        if (!$session->isLoggedIn())
            $this->_redirect('customer/account/login');
    }

    public function execute()
    {
        $params = $this->getRequest()->getParams();

        if (isset($params['save']))
            return $this->saveCard($params);
        else if (isset($params['delete']))
            return $this->deleteCard($params['delete']);

        return $this->resultPageFactory->create();
    }

    public function saveCard($params)
    {
        try
        {
            if ($this->config->getSecurityMethod() > 0)
            {
                if (empty($params['payment']) || empty($params['payment']['cc_stripejs_token']))
                    throw new \Exception("Sorry, the card could not be saved. Unable to use Stripe.js.");

                $parts = explode(":", $params['payment']['cc_stripejs_token']);

                if (strpos($parts[0], "tok_") === false && strpos($parts[0], "src_") === false)
                    throw new \Exception("Sorry, the card could not be saved. Unable to use Stripe.js.");

                try
                {
                    $this->stripeCustomer->addCard($parts[0]);
                    $this->helper->addSuccess("Card **** " . $parts[2] . " was added successfully.");
                }
                catch (\Exception $e)
                {
                    $this->helper->addError("Could not add card.");
                }
            }
            else
            {
                $address = $this->helper->getStripeFormattedDefaultBillingAddress();

                if (empty($address['address_line1']))
                    throw new \Exception("You must first add a billing address before you can add saved cards.");

                $cardDetails = array_merge([
                        'name' => $params['payment']['cc_owner'],
                        'number' => $params['payment']['cc_number'],
                        'exp_month' => $params['payment']['cc_exp_month'],
                        'exp_year' => $params['payment']['cc_exp_year'],
                        'cvc' => $params['payment']['cc_cid']
                    ], $address, ['object' => 'card']);

                try
                {
                    $card = $this->stripeCustomer->addCard($cardDetails);
                    $this->helper->addSuccess("Card **** " . $card->last4 . " was added successfully.");
                }
                catch (\Exception $e)
                {
                    $this->helper->addError("Could not add card.");
                }
            }
        }
        catch (\Stripe\Error\Card $e)
        {
            $this->helper->addError($e->getMessage());
        }
        catch (\Stripe\Error $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError($e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }
        catch (\Exception $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError($e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }

        $this->_redirect('cryozonic-stripe/customer/cards');
    }

    public function deleteCard($token)
    {
        try
        {
            $card = $this->stripeCustomer->deleteCard($token);

            // In case we deleted a source
            if (isset($card->card))
                $card = $card->card;

            $this->helper->addSuccess("Card **** " . $card->last4 . " has been deleted.");
        }
        catch (\Stripe\Error\Card $e)
        {
            $this->helper->addError($e->getMessage());
        }
        catch (\Stripe\Error $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError($e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }
        catch (\Exception $e)
        {
            $this->helper->addError($e->getMessage());
            $this->helper->logError($e->getMessage());
            $this->helper->logError($e->getTraceAsString());
        }

        $this->_redirect('cryozonic-stripe/customer/cards');
    }
}
