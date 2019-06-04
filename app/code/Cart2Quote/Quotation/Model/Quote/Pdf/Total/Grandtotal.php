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
 * Class Grandtotal
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Grandtotal extends DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magento\Tax\Model\Config $taxConfig,
        array $data = []
    ) {
        $this->_taxConfig = $taxConfig;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get grand total for display on PDF
     * @return mixed
     */
    public function getTotalsForDisplay()
    {
        $emptyRowAmount = 2;
        $store = $this->getSource()->getStore();
        if (!$this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return $this->appendEmptyRows(parent::getTotalsForDisplay(), $emptyRowAmount);
        }

        $tax = 0;
        foreach ($this->getSource()->getItemsCollection() as $item) {
            $tax = $tax + $item->getTaxAmount();
        }

        $amount = $this->getSource()->formatPriceTxt($this->getAmount());
        $amountExclTax = $this->getAmount() - $tax;
        $amountExclTax = $amountExclTax > 0 ? $amountExclTax : 0;
        $amountExclTax = $this->getSource()->formatPriceTxt($amountExclTax);

        $fontSize = $this->getFontSize() ? $this->getFontSize() : 7;

        $totals = [
            [
                'amount' => $this->getAmountPrefix() . $amountExclTax,
                'label' => __('Grand Total (Excl. Tax) ') . ':',
                'font_size' => $fontSize,
            ],
        ];

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
            $totals = array_merge($totals, $this->getFullTaxInfo());
        }

        $totals[] = [
            'amount' => $this->getAmountPrefix() . $this->getSource()->formatPriceTxt($tax),
            'label' => __('Tax') . ':',
            'font_size' => $fontSize,
        ];
        $totals[] = [
            'amount' => $this->getAmountPrefix() . $amount,
            'label' => __('Grand Total (Incl. Tax)') . ':',
            'font_size' => $fontSize,
        ];

        return $this->appendEmptyRows($totals, $emptyRowAmount);
    }
}
