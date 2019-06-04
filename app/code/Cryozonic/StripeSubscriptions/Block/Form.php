<?php

namespace Cryozonic\StripeSubscriptions\Block;

use Cryozonic\StripePayments\Helper\Logger;

class Form extends \Magento\Payment\Block\Form\Cc
{
    protected $_template = 'form/switch_subscription.phtml';

    public $isSingleSubscriptionPurchase;
    public $extraClass;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Payment\Model\Config $paymentConfig,
        \Cryozonic\StripePayments\Helper\Generic $helper,
        \Cryozonic\StripeSubscriptions\Helper\Generic $subscriptionsHelper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cryozonic\StripePayments\Model\StripeCustomer $stripeCustomer,
        array $data = []
    ) {
        parent::__construct($context, $paymentConfig, $data);
        $this->helper = $helper;
        $this->subscriptionsHelper = $subscriptionsHelper;
        $this->sessionQuote = $sessionQuote;
        $this->stripeCustomer = $stripeCustomer;

        $this->extraClass = '';
        $this->isSingleSubscriptionPurchase = $this->isSingleSubscriptionPurchase();
        if ($this->isSingleSubscriptionPurchase)
        {
          $this->existingSubscriptions = $this->getCustomerSubscriptions();
          if (count($this->existingSubscriptions) > 0)
            $this->extraClass = 'indent';
          else
            $this->isSingleSubscriptionPurchase = false;
        }
    }

    public function isSingleSubscriptionPurchase()
    {
        $isSubscription = false;
        $quote = $this->sessionQuote->getQuote();
        $itemsCount = $quote->getItemsCount();

        if ($itemsCount != 1) return false;

        foreach ($quote->getAllItems() as $item) {
            $product = $item->getProduct();
            $product = $product->load($product->getId());
            $isSubscription = $product->getCryozonicSubEnabled();
        }

        return $isSubscription;
    }

    public function formatSubscriptionName($sub)
    {
        return $this->subscriptionsHelper->formatSubscriptionName($sub) . " - ends " . $this->formatSubscriptionPeriodEnd($sub);
    }

    public function formatSubscriptionPeriodEnd($sub)
    {
        return date("d/m/Y", $sub->current_period_end);
    }

    public function getCustomerSubscriptions()
    {
        return $this->stripeCustomer->getSubscriptions();
    }
}
