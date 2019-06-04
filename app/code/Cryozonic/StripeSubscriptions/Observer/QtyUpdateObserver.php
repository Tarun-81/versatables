<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;
use Cryozonic\StripePayments\Exception\WebhookException;

class QtyUpdateObserver implements ObserverInterface
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\InitialFee $helper,
        \Cryozonic\StripePayments\Helper\Generic $paymentsHelper,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $dbTransaction,
        \Cryozonic\StripePayments\Model\StripeCustomer $stripeCustomer,
        \Magento\Framework\Event\ManagerInterface $eventManager
    )
    {
        $this->helper = $helper;
        $this->paymentsHelper = $paymentsHelper;
        $this->_stripeCustomer = $stripeCustomer;
        $this->_eventManager = $eventManager;
        $this->invoiceService = $invoiceService;
        $this->dbTransaction = $dbTransaction;
    }

    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $items = $observer->getCart()->getQuote()->getItems();
        foreach ($items as $item)
        {
            if (!empty($item->getQtyOptions()))
                $additionalOptions = $this->helper->getAdditionalOptionsForChildrenOf($item);
            else
                $additionalOptions = $this->helper->getAdditionalOptionsForProductId($item->getProductId(), $item->getQty());

            $item->addOption(array(
                'product_id' => $item->getProductId(),
                'code' => 'additional_options',
                'value' => $this->helper->serialize($additionalOptions)
            ));
        }
    }
}
