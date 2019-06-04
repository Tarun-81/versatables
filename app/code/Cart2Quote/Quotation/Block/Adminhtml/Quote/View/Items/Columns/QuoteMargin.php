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

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Quote\Model\Quote\Item;


/**
 * Class Margin
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class QuoteMargin extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    const TOTAL = 'total';
    const INDIVIDUAL = 'individual';
    /**
     * @var \Cart2Quote\Quotation\Margin\Calculation
     */
    private $gpMarginCalculation;

    /**
     * @param \Cart2Quote\Quotation\Margin\Calculation $gpMarginCalculation
     * @param \Magento\Backend\Block\Template\Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Margin\Calculation $gpMarginCalculation,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Item $emptyQuoteItem,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $stockRegistry,
            $stockConfiguration,
            $registry,
            $optionFactory,
            $quote,
            $emptyQuoteItem,
            $data
        );

        $this->gpMarginCalculation = $gpMarginCalculation;
    }


    /**
     * @return string
     */
    public function getTotalMargin()
    {
        /**
         * Add up known margins
         * Count rows with known margins
         */
        //do not make default value of $averagemargin zero
        $averageMargin = null;
        $itemWithMarginCount = 0;
        $items = $this->getQuote()->getAllVisibleItems();

        foreach ($items as $item) {
            $itemMargin = $this->getMargin($item, self::TOTAL);

            if ($itemMargin != null) {
                $totalItemMargin = $itemMargin * $item->getQty();
                $averageMargin = $averageMargin + $totalItemMargin;
                $itemWithMarginCount += $item->getQty();
            }
        }

        /**
         * divide the totalMargin by the amount of rows with Margin to get Margin over the total.
         * "Average Margin"
         */
        if (!is_null($averageMargin)) {
            $averageMargin /= $itemWithMarginCount;
            $averageMargin = round($averageMargin);
            return $averageMargin;
        }

        return null;
    }

    /**
     * Calculate the profit Margin based on
     * the item's cost price and the quoted price.
     * @param Item $item
     * @param string $marginBlock | null
     * @return string
     *
     */
    public function getMargin(\Magento\Quote\Model\Quote\Item $item, $marginBlock)
    {
        $margin = $this->gpMarginCalculation->itemMargin($item, $marginBlock);
        if ($margin) {
            return $margin;
        }

        return null;
    }
}
