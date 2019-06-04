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
 * Class Tax
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Tax extends DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @var \Magento\Tax\Helper\Data
     */
    protected $_taxHelper;

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
        $this->_taxHelper = $taxHelper;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get tax amount for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $quote = $this->getQuote();
        $totals = [];
        $store = $this->getSource()->getStore();
        if ($this->_taxConfig->displaySalesTaxWithGrandTotal($store)) {
            return [];
        }
        $tax = 0;
        foreach ($quote->getItemsCollection() as $item) {
            $tax = $tax + $item->getTaxAmount();
        }

        //add shipping tax
        if ($quote->getShippingAddress()->getShippingTaxAmount()) {
            $tax = $tax + $quote->getShippingAddress()->getShippingTaxAmount();
        }

        if ($this->_taxConfig->displaySalesFullSummary($store)) {
            $totals = $this->getFullTaxInfo();
        }

        $tax = $quote->formatPriceTxt($tax);
        $totals = array_merge($totals, parent::getTotalsForDisplay());
        $totals[0]['amount'] = $tax;

        return $totals;
    }
}
