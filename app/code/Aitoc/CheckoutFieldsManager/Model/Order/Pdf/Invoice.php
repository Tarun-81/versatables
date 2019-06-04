<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Order\Pdf;

use Aitoc\CheckoutFieldsManager\Traits\CustomFields;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Pdf\Invoice as MagentoInvoice;
use Magento\Sales\Model\Order\Shipment;

class Invoice extends MagentoInvoice
{
    use CustomFields;

    /**
     * @param \Zend_Pdf_Page $page
     * @param Order $obj
     * @param bool $putOrderId
     *
     * @throws \Zend_Pdf_Exception
     */
    protected function insertOrder(&$page, $obj, $putOrderId = true)
    {
        parent::insertOrder($page, $obj, $putOrderId);

        switch (true) {
            case ($obj instanceof Order):
                $shipment = null;
                $order = $obj;
                break;
            case ($obj instanceof Shipment):
                $shipment = $obj;
                $order = $shipment->getOrder();
                break;
            default:
                return;
        }

        $this->prepareCheckoutFieldsData($order->getId());
        $count = count($this->checkoutFieldsData);
        if ($count) {
            $top = $this->y;
            $top += 10;
            $yStepText = 15;
            $aitocCheckoutFieldsBlockSize = 30 * $count;// A, B, C, D blocks for output in PDF report
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.75));
            $page->setFillColor(new \Zend_Pdf_Color_Rgb(1, 1, 1));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $top, 570, $top - $aitocCheckoutFieldsBlockSize);

            $page->setFillColor(new \Zend_Pdf_Color_Rgb(0.93, 0.92, 0.92));
            $page->setLineColor(new \Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            $page->drawRectangle(25, $top, 570, $top - 25);
            $page->setFillColor(new \Zend_Pdf_Color_GrayScale(0));
            $this->_setFontBold($page, 12);
            $checkoutFieldsDataLeft = self::setShowValue($this->checkoutFieldsData);
            $page->drawText(__('Custom Fields:'), 35, $top - 15, 'UTF-8');
            $top -= 40;
            foreach ($checkoutFieldsDataLeft as $oneField) {
                $page->drawText(strip_tags(ltrim($oneField['field_name'])), 35, $top, 'UTF-8');
                $page->drawText(strip_tags(ltrim($oneField['value'])), 285, $top, 'UTF-8');
                $top -= $yStepText;
            }
            $this->y = $top;
        }
    }
}
