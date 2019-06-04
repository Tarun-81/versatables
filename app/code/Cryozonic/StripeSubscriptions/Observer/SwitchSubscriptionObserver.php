<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;

class SwitchSubscriptionObserver implements ObserverInterface
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\Generic $helper,
        \Cryozonic\StripePayments\Model\StripeCustomer $stripeCustomer
    )
    {
        $this->helper = $helper;
        $this->_stripeCustomer = $stripeCustomer;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $switchSubscription = $observer->getData('switchSubscription');
        $order = $observer->getData('order');
        $payment = $observer->getData('payment');
        $trialUntil = $switchSubscription['trial_until'][$switchSubscription['from']];

        $date = \DateTime::createFromFormat("d/m/Y", $trialUntil);
        $trialEnd = $date->getTimestamp();
        if (!is_numeric($trialEnd) || $trialEnd <= 0)
            throw new \Exception("Invalid date specified for subscription: ". $trialUntil);

        // Shipping must be included in the subscription price. See the documentation for an explanation.
        $shippingAmount = $order->getShippingAmount();
        $baseShippingAmount = $order->getBaseShippingAmount();
        $order->setShippingAmount(0);
        $order->setBaseShippingAmount(0);
        $order->setGrandTotal($order->getGrandTotal() - $shippingAmount);
        $order->setBaseGrandTotal($order->getBaseGrandTotal() - $baseShippingAmount);

        $this->helper->createSubscriptions($order, false, $trialEnd);
        $payment->setIsTransactionClosed(1);
        $payment->setAdditionalInformation('captured', true);
        $sub = $this->_stripeCustomer->getSubscription($switchSubscription['from']);
        $sub->cancel();
    }
}
