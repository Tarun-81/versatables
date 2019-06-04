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
 * Class QuoteReduction
 * @package Cart2Quote\Quotation\Model\Quote\Pdf\Total
 */
class QuoteAdjustment extends \Cart2Quote\Quotation\Model\Quote\Pdf\Total\DefaultTotal
{
    /**
     * @var \Magento\Tax\Model\Config
     */
    protected $taxConfig;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @param \Magento\Tax\Helper\Data $taxHelper
     * @param \Magento\Tax\Model\Calculation $taxCalculation
     * @param \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Tax\Helper\Data $taxHelper,
        \Magento\Tax\Model\Calculation $taxCalculation,
        \Magento\Tax\Model\ResourceModel\Sales\Order\Tax\CollectionFactory $ordersFactory,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->taxConfig = $taxConfig;
        parent::__construct($taxHelper, $taxCalculation, $ordersFactory, $data);
    }

    /**
     * Get Quote Reduction for display on PDF
     * @return array
     */
    public function getTotalsForDisplay()
    {
        $totals = parent::getTotalsForDisplay();
        $showAdjustment = $this->scopeConfig->getValue(\Cart2Quote\Quotation\Block\Quote\Totals::XML_PATH_CART2QUOTE_QUOTATION_GLOBAL_SHOW_QUOTE_ADJUSTMENT);
        if (intval($showAdjustment) == 0 || (intval($showAdjustment) == 2 && $this->getAmount() == 0)) {
            unset($totals[0]);
        }

        return $totals;
    }

    /**
     * Function to return the amount that should be included in QuoteReduction block
     * @return mixed
     */
    public function getAmount()
    {
        $amount = $this->getSource()->getSubtotal() - $this->getSource()->getOriginalSubTotal();

        return $amount;
    }
}
