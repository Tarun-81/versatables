<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;

class DataAssignObserver implements ObserverInterface
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
        $method = $observer->getData('method');
        $data = $observer->getData('data');
        $info = $observer->getData('info');

        if ($this->helper->isAdminSubscriptionSwitch($data))
        {
            $info->setAdditionalInformation('switch_subscription', $data['subscription']);
            return $this;
        }
        else if ($info->getAdditionalInformation('switch_subscription'))
        {
            $info->setAdditionalInformation('switch_subscription', null);
        }
    }
}
