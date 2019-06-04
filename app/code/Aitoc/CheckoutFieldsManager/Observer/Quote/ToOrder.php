<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Observer\Quote;

use Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData\ConvertToOrder;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Event name: sales_model_service_quote_submit_success
 * Observer name: aitoc_convert_quote_to_order
 */
class ToOrder implements ObserverInterface
{
    /**
     * @var ConvertToOrder
     */
    private $converter;

    /**
     * @param ConvertToOrder $converter
     */
    public function __construct(
        ConvertToOrder $converter
    ) {
        $this->converter = $converter;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Observer $observer)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $observer->getEvent()->getQuote();
        /** @var \Magento\Sales\Model\Order $order */
        $order = $observer->getEvent()->getOrder();
        $this->converter->convert($quote->getId(), $order->getId());
    }
}
