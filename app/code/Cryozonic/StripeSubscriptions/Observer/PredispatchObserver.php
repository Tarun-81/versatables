<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;
use Cryozonic\StripePayments\Exception\WebhookException;

class PredispatchObserver implements ObserverInterface
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\Generic $helper,
        \Cryozonic\StripePayments\Helper\Generic $paymentsHelper,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->helper = $helper;
        $this->paymentsHelper = $paymentsHelper;
        $this->_eventManager = $eventManager;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (stripos($_SERVER['REQUEST_URI'],"directory/currency/switch") !== false)
            $this->_eventManager->dispatch('cryozonic_stripesubscriptions_currency_switch');
    }
}
