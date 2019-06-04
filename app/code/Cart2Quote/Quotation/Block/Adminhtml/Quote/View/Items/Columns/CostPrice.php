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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

/**
 * Class CostPrice
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class CostPrice extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{

    /**
     * if no cost price is found, the price is used for calculations
     */
    const ALTERNATIVE_COST_FIELD = 'price';

    /**
     * Get cost total
     *
     * @param bool|true $useAlternativeCostField
     * @return float
     */
    public function getCostTotal($useAlternativeCostField = true)
    {
        $totalCost = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $itemCost = $this->getItemCost($item, $useAlternativeCostField);
            $totalCost += $itemCost * $item->getQty();
        }

        return $totalCost;
    }

    /**
     * Get item cost
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param bool|true $useAlternativeCostField
     * @return float
     */
    public function getItemCost(\Magento\Quote\Model\Quote\Item $item, $useAlternativeCostField = true)
    {
        $calculateProduct = $item;
        if ($item->getProduct()) {
            $calculateProduct = $item->getProduct();
        }

        $itemCost = $calculateProduct->getCost();
        if (!$itemCost) {
            $itemCost = $item->getBaseCost();
        } elseif (!$itemCost && $useAlternativeCostField) {
            $itemCost = $item->getData(self::ALTERNATIVE_COST_FIELD);
        }

        $quote = $this->getQuote();
        $quoteCurrency = $quote->getQuoteCurrency();
        if ($quoteCurrency != $quote->getBaseCurrency()) {
            try {
                $itemCost = $this->_storeManager->getStore()->getBaseCurrency()->convert($itemCost, $quoteCurrency);
            } catch (\Exception $e) {
                $logMessage = sprintf("No conversion rate set: %s", $e);
                $this->_logger->notice($logMessage);

                return null;
            }
        }

        return $itemCost;
    }
}
