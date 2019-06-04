<?php
declare(strict_types = 1);
namespace Cryozonic\StripeSubscriptions\Plugin\Order;

use Magento\Framework\DataObject;
use Magento\Quote\Api\Data\TotalsInterface;
use Magento\Sales\Block\Order\Totals;
use Magento\Sales\Model\Order;
use Cryozonic\StripePayments\Helper\Logger;

class AddInitialFeeToTotalsBlock
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\InitialFee $helper,
        \Magento\Quote\Model\QuoteFactory $quoteFactory
    )
    {
        $this->helper = $helper;
        $this->quoteFactory = $quoteFactory;
    }

    public function afterGetOrder(Totals $subject, Order $order): Order
    {
        if (empty($subject->getTotals()))
            return $order;

        if ($subject->getTotal('initial_fee') !== false)
            return $order;

        if ($this->isRecurringInvoice($subject, $order))
            return $order;

        $quote = $this->quoteFactory->create()->load($order->getQuoteId());

        $fee = $this->helper->getTotalInitialFeeForQuote($quote);
        if ($fee > 0)
        {
            $subject->addTotalBefore(new DataObject([
                'code' => 'initial_fee',
                'value' => $fee,
                'label' => __('Initial Fee')
            ]), TotalsInterface::KEY_GRAND_TOTAL);
        }

        return $order;
    }

    public function isRecurringInvoice($subject, $order)
    {
        if (stripos(get_class($subject), 'Order\Invoice\Totals\Interceptor') === false)
            return false;

        $currentInvoiceID = $subject->getInvoice()->getId();

        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice)
        {
            if ($invoice->getId() == $currentInvoiceID)
                return false;
            else
                return true;
        }

        return false;
    }
}
