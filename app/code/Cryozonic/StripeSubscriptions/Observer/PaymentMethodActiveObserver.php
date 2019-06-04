<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\Observer;
use Magento\Payment\Observer\AbstractDataAssignObserver;
use Cryozonic\StripePayments\Helper\Logger;

class PaymentMethodActiveObserver extends AbstractDataAssignObserver
{
    public function __construct(
        \Cryozonic\StripePayments\Helper\Generic $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $result = $observer->getEvent()->getResult();
        $methodInstance = $observer->getEvent()->getMethodInstance();
        $quote = $observer->getEvent()->getQuote();
        $code = $methodInstance->getCode();
        $isAvailable = $result->getData('is_available');

        // No need to check if its already false
        if (!$isAvailable)
            return;

        // Don't disable the Stripe payment method
        if ($code == 'cryozonic_stripe')
            return;

        // Can't check without a quote
        if (!$quote)
            return;

        // Check if the quote contains subscriptions
        $items = $quote->getAllItems();
        if (empty($items))
            return;

        foreach ($items as $item)
        {
            $product = $this->helper->loadProductById($item->getProduct()->getEntityId());
            if ($product->getCryozonicSubEnabled())
            {
                // Disable for all payment methods except the module
                $result->setData('is_available', false);
                return;
            }
        }
    }
}
