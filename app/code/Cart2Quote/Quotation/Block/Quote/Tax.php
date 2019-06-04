<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Block\Quote;

use Cart2Quote\Quotation\Model\Quote;

/**
 * Class Tax
 * @package Cart2Quote\Quotation\Block\Quote
 */
class Tax extends \Magento\Tax\Block\Sales\Order\Tax
{
    /**
     * @var Quote
     */
    protected $_quote;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Tax\Model\Config $taxConfig,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->_config = $taxConfig;
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct(
            $context,
            $taxConfig,
            $data
        );
    }

    /**
     * Check if we nedd display full tax total info
     * @return bool
     */
    public function displayFullSummary()
    {
        return $this->_config->displaySalesFullSummary($this->getQuote()->getStore());
    }

    /**
     * @return Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Initialize all order totals relates with tax
     * @return \Cart2Quote\Quotation\Block\Quote\Tax
     */
    public function initTotals()
    {
        $parent = $this->getParentBlock();
        $this->_quote = $parent->getQuote();
        $this->_source = $parent->getSource();

        $store = $this->getStore();
        $allowTax = $this->_source->getTaxAmount() > 0 || $this->_config->displaySalesZeroTax($store);
        $grandTotal = (double)$this->_source->getGrandTotal();
        if (!$grandTotal || $allowTax && !$this->_config->displaySalesTaxWithGrandTotal($store)) {
            $this->_addTax();
        }

        $this->_initSubtotal();
        $this->_initShipping();
        $this->_initDiscount();
        $this->_initGrandTotal();
        return $this;
    }

    /**
     * Get order store object
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_quote->getStore();
    }

    /**
     * @return $this
     */
    protected function _initSubtotal()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $subtotal = $parent->getTotal('subtotal');
        if (!$subtotal) {
            return $this;
        }
        if ($this->_config->displaySalesSubtotalBoth($store)) {
            $subtotal = (double)$this->_source->getSubtotal();
            $baseSubtotal = (double)$this->_source->getBaseSubtotal();
            $subtotalIncl = (double)$this->_source->getSubtotalInclTax();
            $baseSubtotalIncl = (double)$this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl || !$baseSubtotalIncl) {
                // Calculate the subtotal if it is not set
                $subtotalIncl = $subtotal
                    + $this->_source->getTaxAmount()
                    - $this->_source->getShippingTaxAmount();
                $baseSubtotalIncl = $baseSubtotal
                    + $this->_source->getBaseTaxAmount()
                    - $this->_source->getBaseShippingTaxAmount();

                if ($this->_source instanceof Quote) {
                    // Adjust for the discount tax compensation
                    foreach ($this->_source->getAllItems() as $item) {
                        $subtotalIncl += $item->getDiscountTaxCompensationAmount();
                        $baseSubtotalIncl += $item->getBaseDiscountTaxCompensationAmount();
                    }
                }
            }

            $subtotalIncl = max(0, $subtotalIncl);
            $baseSubtotalIncl = max(0, $baseSubtotalIncl);
            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'subtotal_excl',
                    'value' => $subtotal,
                    'base_value' => $baseSubtotal,
                    'label' => __('Subtotal (Excl.Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'subtotal_incl',
                    'value' => $subtotalIncl,
                    'base_value' => $baseSubtotalIncl,
                    'label' => __('Subtotal (Incl.Tax)'),
                ]
            );
            $parent->addTotal($totalExcl, 'subtotal');
            $parent->addTotal($totalIncl, 'subtotal_excl');
            $parent->removeTotal('subtotal');
        } elseif ($this->_config->displaySalesSubtotalInclTax($store)) {
            $subtotalIncl = (double)$this->_source->getSubtotalInclTax();
            $baseSubtotalIncl = (double)$this->_source->getBaseSubtotalInclTax();

            if (!$subtotalIncl) {
                $subtotalIncl = $this->_source->getSubtotal() +
                    $this->_source->getTaxAmount() -
                    $this->_source->getShippingTaxAmount();
            }
            if (!$baseSubtotalIncl) {
                $baseSubtotalIncl = $this->_source->getBaseSubtotal() +
                    $this->_source->getBaseTaxAmount() -
                    $this->_source->getBaseShippingTaxAmount();
            }

            $total = $parent->getTotal('subtotal');
            if ($total) {
                $total->setValue(max(0, $subtotalIncl));
                $total->setBaseValue(max(0, $baseSubtotalIncl));
            }
        }
        return $this;
    }

    /**
     * @return $this
     */
    protected function _initShipping()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $shipping = $parent->getTotal('shipping');
        if (!$shipping) {
            return $this;
        }

        if ($this->_config->displaySalesShippingBoth($store)) {
            $shipping = (double)$this->_source->getShippingAmount();
            $baseShipping = (double)$this->_source->getBaseShippingAmount();
            $shippingIncl = (double)$this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl = $shipping + (double)$this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl = (double)$this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $baseShipping + (double)$this->_source->getBaseShippingTaxAmount();
            }

            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping',
                    'value' => $shipping,
                    'base_value' => $baseShipping,
                    'label' => __('Shipping & Handling (Excl.Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'shipping_incl',
                    'value' => $shippingIncl,
                    'base_value' => $baseShippingIncl,
                    'label' => __('Shipping & Handling (Incl.Tax)'),
                ]
            );
            $parent->addTotal($totalExcl, 'shipping');
            $parent->addTotal($totalIncl, 'shipping');
        } elseif ($this->_config->displaySalesShippingInclTax($store)) {
            $shippingIncl = $this->_source->getShippingInclTax();
            if (!$shippingIncl) {
                $shippingIncl = $this->_source->getShippingAmount() + $this->_source->getShippingTaxAmount();
            }
            $baseShippingIncl = $this->_source->getBaseShippingInclTax();
            if (!$baseShippingIncl) {
                $baseShippingIncl = $this->_source->getBaseShippingAmount() +
                    $this->_source->getBaseShippingTaxAmount();
            }
            $total = $parent->getTotal('shipping');
            if ($total) {
                $total->setValue($shippingIncl);
                $total->setBaseValue($baseShippingIncl);
            }
        }
        return $this;
    }

    /**
     * @return void
     */
    protected function _initDiscount()
    {
        //nothing
    }

    /**
     * @return $this
     */
    protected function _initGrandTotal()
    {
        $store = $this->getStore();
        $parent = $this->getParentBlock();
        $grandototal = $parent->getTotal('grand_total');
        if (!$grandototal || !(double)$this->_source->getGrandTotal()) {
            return $this;
        }

        if ($this->_config->displaySalesTaxWithGrandTotal($store)) {
            $grandtotal = $this->_source->getGrandTotal();
            $baseGrandtotal = $this->_source->getBaseGrandTotal();
            $grandtotalExcl = $grandtotal - $this->_source->getTaxAmount();
            $baseGrandtotalExcl = $baseGrandtotal - $this->_source->getBaseTaxAmount();
            $grandtotalExcl = max($grandtotalExcl, 0);
            $baseGrandtotalExcl = max($baseGrandtotalExcl, 0);
            $totalExcl = new \Magento\Framework\DataObject(
                [
                    'code' => 'grand_total',
                    'strong' => true,
                    'value' => $grandtotalExcl,
                    'base_value' => $baseGrandtotalExcl,
                    'label' => __('Grand Total (Excl.Tax)'),
                ]
            );
            $totalIncl = new \Magento\Framework\DataObject(
                [
                    'code' => 'grand_total_incl',
                    'strong' => true,
                    'value' => $grandtotal,
                    'base_value' => $baseGrandtotal,
                    'label' => __('Grand Total (Incl.Tax)'),
                ]
            );
            $parent->addTotal($totalExcl, 'grand_total');
            $this->_addTax('grand_total');
            $parent->addTotal($totalIncl, 'tax');
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getLabelProperties()
    {
        return $this->getParentBlock()->getLabelProperties();
    }

    /**
     * @return array
     */
    public function getValueProperties()
    {
        return $this->getParentBlock()->getValueProperties();
    }

    /**
     * Check disabled product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->cart2QuoteHelper->isProductRemarkDisabled();
    }

    /**
     * Has Optional Products
     *
     * @return bool
     */
    public function hasOptionalProducts()
    {
        return $this->getParentBlock() &&
            $this->getParentBlock()->hasOptionalProducts();
    }
}
