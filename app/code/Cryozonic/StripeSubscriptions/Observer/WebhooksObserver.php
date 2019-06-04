<?php

namespace Cryozonic\StripeSubscriptions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Cryozonic\StripePayments\Helper\Logger;
use Cryozonic\StripePayments\Exception\WebhookException;

class WebhooksObserver implements ObserverInterface
{
    public function __construct(
        \Cryozonic\StripeSubscriptions\Helper\Generic $helper,
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
        $eventName = $observer->getEvent()->getName();
        $arrEvent = $observer->getData('arrEvent');
        $stdEvent = $observer->getData('stdEvent');
        $object = $observer->getData('object');

        switch ($eventName) {
            case 'cryozonic_stripe_webhook_invoice_payment_succeeded':
                $this->paymentSucceeded($stdEvent);
                break;
            case 'cryozonic_stripe_webhook_invoice_payment_failed':
                //$this->paymentFailed($event);
                break;
            default:
                # code...
                break;
        }
    }

    public function paymentSucceeded($event)
    {
        if (!empty($event->data->object->lines->data[0]))
            $subscription = $event->data->object->lines->data[0];

        if (!isset($subscription))
            throw new WebhookException(__("Received {$event->type} webhook but could not read the subscription object."));

        $metadata = $subscription->metadata;

        if (!empty($metadata->{'Order #'}))
            $orderId = $metadata->{'Order #'};
        else
            throw new WebhookException(__("The webhook request has no Order ID in its metadata - ignoring."));

        if (!empty($metadata->{'Product ID'}))
            $productId = $metadata->{'Product ID'};
        else
            throw new WebhookException(__("The webhook request has no product ID in its metadata - ignoring."));

        $transactionId = $event->data->object->charge;
        $currency = $event->data->object->currency;
        $orderItemId = false;
        $markAsPaid = true;

        if (isset($event->data->object->total))
            $amountPaid = $event->data->object->total;
        else
            $amountPaid = $subscription->amount;

        if ($amountPaid <= 0)
            return;

        $cents = 100;
        $decimals = 2;
        if ($this->paymentsHelper->isZeroDecimal($currency))
        {
            $cents = 1;
            $decimals = 0;
        }

        $amountPaid = round($amountPaid / $cents, $decimals);

        $order = $this->paymentsHelper->loadOrderByIncrementId($orderId);

        $items = $order->getAllItems();

        foreach ($items as $item)
        {
            if ($item->getProductId() == $productId)
            {
                // Is this a configurable product?
                if ($item->getRowTotalInclTax() == 0 && $item->getParentItem())
                    $item = $item->getParentItem();

                $item->setQtyInvoiced(0);
                $item->setQtyCanceled(0);

                $orderItemId = $item->getId();
                $orderItemQty = $item->getQtyOrdered();
                $taxAmount = $item->getTaxAmount();
                $baseTaxAmount = $item->getBaseTaxAmount();
                $grandTotal = $item->getRowTotalInclTax();
                $baseGrandTotal = $item->getBaseRowTotalInclTax();
                $subTotal = $item->getRowTotal();
                $baseSubtotal = $item->getBaseRowTotal();

                break;
            }
        }

        // Scenario where the merchant switched the customer to another subscription plan
        // In theory here's how to handle this scenario, but in practice, the invoice must be created first
        // We'd probably be better off if we created a new order with this product
        if (!$orderItemId)
        {
            // $item = $this->objectManager->create('Magento\Sales\Model\Order\Invoice\Item');
            // $orderItemQty = $subscription->quantity;
            // $taxAmount = 0;
            // $baseTaxAmount = 0;
            // $grandTotal = $amountPaid;
            // $baseGrandTotal = 0;
            // $subTotal = $amountPaid;
            // $baseSubtotal = 0;

            // $planAmount = round($subscription->plan->amount / $cents, $decimals);

            // $item->setName($subscription->plan->name);
            // $item->setQtyOrdered($orderItemQty);
            // $item->setPrice($planAmount);
            // $item->save();
            // $orderItemId = $item->getId();
            throw new WebhookException(__("Could not match the product ID $productId with an item on order #$orderId - ignoring."));
        }

        $discount = $grandTotal - $amountPaid;
        $baseDiscount = $discount * ($baseGrandTotal / $grandTotal);

        $itemsArray = array($orderItemId => $orderItemQty);

        $invoice = $this->invoiceService->prepareInvoice($order, $itemsArray);

        // There is only one order item per invoice
        foreach ($invoice->getAllItems() as $invoiceItem)
        {
            $invoiceItem->setRowTotal($subTotal);
            $invoiceItem->setBaseRowTotal($baseSubtotal);
            $invoiceItem->setSubtotal($grandTotal);
            $invoiceItem->setBaseSubtotal($baseGrandTotal);
            $invoiceItem->setTaxAmount($taxAmount);
            $invoiceItem->setBaseTaxAmount($baseTaxAmount);
            $invoiceItem->setDiscountAmount($discount);
            $invoiceItem->setBaseDiscountAmount($baseDiscount);
        }
        $invoice->setTaxAmount($taxAmount);
        $invoice->setBaseTaxAmount($baseTaxAmount);
        // $invoice->setDiscountTaxCompensationAmount()
        // $invoice->setBaseDiscountTaxCompensationAmount()
        // $invoice->setShippingTaxAmount();
        // $invoice->setBaseShippingTaxAmount();
        $invoice->setShippingAmount(0); // Shipping should be included in the subscription price
        $invoice->setBaseShippingAmount(0);
        $invoice->setDiscountAmount($discount);
        $invoice->setBaseDiscountAmount($baseDiscount);
        // $invoice->setBaseCost();
        $invoice->setSubtotal($subTotal);
        $invoice->setBaseSubtotal($baseSubtotal);
        $invoice->setGrandTotal($grandTotal - $discount);
        $invoice->setBaseGrandTotal($baseGrandTotal - $baseDiscount);

        $invoice->setTransactionId($transactionId);
        if ($markAsPaid)
        {
            $invoice->setState($invoice::STATE_PAID);
        }
        else
        {
            $invoice->setState($invoice::STATE_OPEN);
            $invoice->setRequestedCaptureCase($invoice::CAPTURE_OFFLINE);
        }
        // Invoice notification to the customer
        $notifyCustomerByEmail = true;
        $visibleOnFront = true;

        $invoice->register();

        $transactionSave = $this->dbTransaction;
        $transactionSave->addObject($invoice);
        $transactionSave->addObject($invoice->getOrder());
        $transactionSave->save();

        $comment = $this->paymentsHelper->createInvoiceComment("Created invoice for order #$orderId based on subscription", $notifyCustomerByEmail, $visibleOnFront);
        $comment->setInvoice($invoice);
        $comment->setParentId($invoice->getId());
        $comment->save();

        $invoice->addComment($comment, $notifyCustomerByEmail, $visibleOnFront);

        $order->addStatusHistoryComment(__('Notified customer about invoice #%1.', $invoice->getId()))
            ->setIsCustomerNotified(true)
            ->save();

        $this->_eventManager->dispatch('cryozonic_stripe_webhook_invoice_payment_succeeded_complete', array(
            'stdEvent' => $event,
            'productId' => $productId,
            'order' => $order
        ));
    }
}
