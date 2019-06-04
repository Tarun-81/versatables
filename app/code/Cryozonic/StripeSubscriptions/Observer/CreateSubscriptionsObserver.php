<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;

class CreateSubscriptionsObserver implements ObserverInterface
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\Generic $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getData('order');
        $returnData = $observer->getData('returnData');
        $params = $returnData->getData('params');
        $cents = $returnData->getData('cents');
        $amount = $returnData->getData('amount');
        $isDryRun = $returnData->getData('is_dry_run');

        $amounts = $this->helper->createSubscriptions($order, $isDryRun);
        $trialAmount = $amounts['trialAmount'];
        $initialFee = $amounts['initialFee'];

        // If any subscriptions are configured as trials, do not charge for those right now
        $params["amount"] = round((($amount/$cents) - $trialAmount) * $cents);

        $returnData->setParams($params);
    }
}
