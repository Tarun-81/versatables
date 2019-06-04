<?php
namespace Cryozonic\StripeSubscriptions\Model\Invoice\Total;

use Magento\Quote\Model\Quote;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total;
use Cryozonic\StripePayments\Helper\Logger;

class InitialFee extends \Magento\Sales\Model\Order\Total\AbstractTotal
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\InitialFee $helper
    )
    {
        $this->helper = $helper;
    }

    /**
     * @param Quote $quote
     * @param ShippingAssignmentInterface $shippingAssignment
     * @param Total $total
     * @return $this
     */
    public function collect(
        \Magento\Sales\Model\Order\Invoice $invoice
    ) {
        $amount = $this->helper->getTotalInitialFeeForInvoice($invoice);
        if (is_numeric($invoice->getBaseToQuoteRate()))
            $baseAmount = $amount / $invoice->getBaseToOrderRate();
        else
            $baseAmount = $amount;

        $invoice->setGrandTotal($invoice->getGrandTotal() + $amount);
        $invoice->setBaseGrandTotal($invoice->getBaseGrandTotal() + $baseAmount);

        return $this;
    }
}
