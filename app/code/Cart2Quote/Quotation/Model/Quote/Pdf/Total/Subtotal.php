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

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Total;

/**
 * Class Subtotal
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Subtotal extends DefaultTotal
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Subtotal constructor.
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get subtotal for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $store = $this->getSource()->getStore();
        $helper = $this->_taxHelper;

        //get amount excluding tax
        $amount = $this->getSource()->formatPriceTxt($this->getAmount());

        //get amount including tax
        if ($this->getSource()->getSubtotalInclTax()) {
            $amountInclTax = $this->getSource()->getSubtotalInclTax();
        } else {
            $amountInclTax = $this->getAmount()
                + $this->getSource()->getShippingAddress()->getTaxAmount()
                - $this->getSource()->getShippingAddress()->getShippingTaxAmount();
        }

        $amountInclTax = $this->getSource()->formatPriceTxt($amountInclTax);
        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;
        $prefix = '';
        $showAdjustment = $this->scopeConfig->getValue(\Cart2Quote\Quotation\Block\Quote\Totals::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT);
        if (intval($showAdjustment) == 1 || (intval($showAdjustment) == 2 && ($this->getQuote()->getSubtotal() != $this->getQuote()->getOriginalSubTotal()))) {
            $origAmount = $this->getSource()->getOriginalSubTotal();
            $origAmountInclTax = $this->getQuote()->getOriginalSubTotal() +
                $this->getQuote()->getTaxAmount() -
                $this->getQuote()->getShippingTaxAmount();
            $origAmountInclTax = $this->getSource()->formatPriceTxt($origAmountInclTax);
            $origAmount = $this->getSource()->formatPriceTxt($origAmount);
            $prefix = 'Quoted ';
            if ($helper->displaySalesSubtotalBoth($store)) {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmount,
                        'label' => __('Original Subtotal (Excl. Tax)') . ':',
                        'font_size' => $fontSize,
                    ],
                    [
                        'amount' => $this->getAmountPrefix() . $origAmountInclTax,
                        'label' => __('Original Subtotal (Incl. Tax)') . ':',
                        'font_size' => $fontSize
                    ],
                ];
            } elseif ($helper->displaySalesSubtotalInclTax($store)) {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmountInclTax,
                        'label' => __('Original ') . __($this->getTitle()) . ':',
                        'font_size' => $fontSize,
                    ],
                ];
            } else {
                $totals = [
                    [
                        'amount' => $this->getAmountPrefix() . $origAmount,
                        'label' => __('Original ') . __($this->getTitle()) . ':',
                        'font_size' => $fontSize,
                    ],
                ];
            }
        }

        if ($helper->displaySalesSubtotalBoth($store)) {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amount,
                    'label' => __($prefix) . __('Subtotal (Excl. Tax)') . ':',
                    'font_size' => $fontSize
                ];
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amountInclTax,
                    'label' => __($prefix) . __('Subtotal (Incl. Tax)') . ':',
                    'font_size' => $fontSize
                ];

        } elseif ($helper->displaySalesSubtotalInclTax($store)) {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amountInclTax,
                    'label' => __($prefix) . __($this->getTitle()) . ':',
                    'font_size' => $fontSize
                ];
        } else {
            $totals[] =
                [
                    'amount' => $this->getAmountPrefix() . $amount,
                    'label' => __($prefix) . __($this->getTitle()) . ':',
                    'font_size' => $fontSize
                ];
        }

        return $totals;
    }
}
