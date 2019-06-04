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
 * Class Discount
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class Discount extends DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $_taxConfig;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quote;

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
     * @return array
     */
    public static function getUnderscoreCache()
    {
        return self::$_underscoreCache;
    }

    /**
     * @return \Magento\Tax\Model\Config
     */
    public function getTaxConfig()
    {
        return $this->_taxConfig;
    }

    /**
     * @return \Magento\Tax\Model\Calculation
     */
    public function getTaxCalculation()
    {
        return $this->_taxCalculation;
    }

    /**
     * @return \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory
     */
    public function getTaxOrdersFactory()
    {
        return $this->_taxOrdersFactory;
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * @return \Magento\Tax\Helper\Data
     */
    public function getTaxHelper()
    {
        return $this->_taxHelper;
    }

    /**
     * Get discounts for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $totals = [];
        $amount = 0;
        $quote = $this->getSource();
        $couponCode = $quote->getCouponCode();

        $title = __($this->getTitle());
        $label = null;
        if ($couponCode != '') {
            $label = $title . ' (' . $couponCode . '):';
            $amount = $this->getAmount();
        }
        $totals = array_merge($totals, parent::getTotalsForDisplay());

        //being a discount we want the outcome to be negative
        if ($amount > 0) {
            $amount = $amount - ($amount * 2);
        }

        if ($label != null) {
            $totals[0]['amount'] = $quote->formatPriceTxt($amount);
            $totals[0]['label'] = $label;
        }
        return $totals;
    }

    /**
     * @return float
     */
    public function getAmount()
    {
        return $this->getSource()->getBaseSubtotalWithDiscount() - $this->getSource()->getBaseSubtotal();
    }
}
