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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\CatalogInventory\Api\StockStateInterface;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Adminhtml quotation quote view items grid block
 */
class Grid extends \Magento\Sales\Block\Adminhtml\Order\Create\Items\Grid
{
    const ALTERNATIVE_COST_FIELD = 'price';

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Quote create model
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quoteCreate;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry = null;

    /**
     * @var \Magento\Catalog\Helper\Product\Configuration
     */
    protected $productConfig;

    /**
     * @var DefaultColumn
     */
    protected $column;

    /**
     * Grid constructor.
     * @param \Magento\Catalog\Helper\Product\Configuration $productConfig
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Wishlist\Model\WishlistFactory $wishlistFactory
     * @param \Magento\GiftMessage\Model\Save $giftMessageSave
     * @param \Magento\Tax\Model\Config $taxConfig
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\GiftMessage\Helper\Message $messageHelper
     * @param StockRegistryInterface $stockRegistry
     * @param StockStateInterface $stockState
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Helper\Product\Configuration $productConfig,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Wishlist\Model\WishlistFactory $wishlistFactory,
        \Magento\GiftMessage\Model\Save $giftMessageSave,
        \Magento\Tax\Model\Config $taxConfig,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\GiftMessage\Helper\Message $messageHelper,
        StockRegistryInterface $stockRegistry,
        StockStateInterface $stockState,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        array $data = []
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->coreRegistry = $registry;
        $this->quoteCreate = $quoteCreate;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $wishlistFactory,
            $giftMessageSave,
            $taxConfig,
            $taxData,
            $messageHelper,
            $stockRegistry,
            $stockState,
            $data
        );

        $this->productConfig = $productConfig;
    }

    /**
     * Retrieve create quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getCreateOrderModel()
    {
        return $this->quoteCreate;
    }

    /**
     * Get order item extra info block
     *
     * @param Item $item
     * @return \Magento\Framework\View\Element\AbstractBlock
     */
    public function getItemExtraInfo($item)
    {
        return $this->getLayout()->getBlock('quote_item_extra_info')->setItem($item);
    }

    /**
     * Accept option value and return its formatted view
     *
     * @param string|array $optionValue
     * @return array
     */
    public function getFormatedOptionValue($optionValue)
    {
        $params = [
            'max_length' => 55,
            'cut_replacer' => ' <a href="#" class="dots tooltip toggle" onclick="return false">...</a>'
        ];

        return $this->productConfig->getFormattedOptionValue($optionValue, $params);
    }

    /**
     * Get original subtotal
     *
     * @return float
     */
    public function getOriginalSubtotal()
    {
        return $this->getQuote()->getOriginalSubtotal();
    }

    /**
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (!$quote = $this->coreRegistry->registry('current_quote')) {
            $this->coreRegistry->register(
                'current_quote',
                $this->quoteCreate->load($this->getRequest()->getParam('quote_id'))
            );
        }

        return $quote;
    }

    /**
     * Get proposal total
     *
     * @deprecated Please use the total collected on the quote
     * @return float
     */
    public function getProposalTotal()
    {
        $proposalTotal = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $itemCustomPrice = $item->getCustomPrice();
            $itemCustomPrice = isset($itemCustomPrice) ? $itemCustomPrice : $item->getPrice();
            $proposalTotal += $itemCustomPrice * $item->getQty();
        }

        return $proposalTotal;
    }

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
        $itemCost = $item->getCost();
        if (!$itemCost) {
            $itemCost = $item->getBaseCost();
        } elseif (!$itemCost && $useAlternativeCostField) {
            $itemCost = $item->getData(self::ALTERNATIVE_COST_FIELD);
        }

        return $itemCost;
    }

    /**
     * Get total item qty
     *
     * @return int
     */
    public function getTotalItemQty()
    {
        $itemsQty = 0;
        foreach ($this->getQuote()->getAllVisibleItems() as $item) {
            $itemQty = $item->getQty();
            $itemsQty += $itemQty;
        }

        return $itemsQty;
    }

    /**
     * Check disabled product remark field
     *
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->quotationHelper->isProductRemarkDisabled();
    }

    /**
     * Check action active status
     *
     * @return boolean
     */
    public function isActiveAction()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ACCEPTED,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CLOSED
        ];

        $quote = $this->getQuote();
        if (!in_array($quote->getStatus(), $availableStatus)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Check make optional active status
     *
     * @return boolean
     */
    public function isActiveMakeOptional()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN
        ];

        return in_array($this->getQuote()->getStatus(), $availableStatus);
    }

    /**
     * Format price with custom return
     *
     * @param int $value
     * @param string|int $zero
     * @return string
     */
    public function formatPriceZero($value, $zero)
    {
        if (isset($value) && $value > 0) {
            return $this->formatPrice($value);
        }

        return $zero;
    }

    /**
     * Retrieve price attribute html content
     *
     * @param string $code
     * @param bool $strong
     * @param string $separator
     * @return string
     */
    public function displayPriceAttribute($code, $strong = false, $separator = '<br />')
    {
        return $this->column->displayPriceAttribute($code, $strong, $separator);
    }

    /**
     * Set the active item
     *
     * @param $item
     */
    public function setActiveItem($item)
    {
        $this->column->setItem($item);
        $this->column->setPriceDataObject($item->getCurrentTierItem());
    }

    /**
     * Return the active item
     *
     * @return Mixed
     */
    public function getActiveItem()
    {
        return $this->column->getPriceDataObject();
    }

    /**
     * Get max qty from the tier items
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $tierItems
     * @return int
     */
    public function getMaxTierItemQty($tierItems)
    {
        $qty = 1;
        foreach ($tierItems as $tierItem) {
            if ($tierItem instanceof \Cart2Quote\Quotation\Model\Quote\TierItem) {
                $qty = max($tierItem->getQty(), $qty);
            }
        }

        return $qty * 1;
    }

    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotation_quote_view_search_grid');
    }

    /**
     * Force disable cache
     *
     * @return bool
     */
    protected function getCacheLifetime()
    {
        return false;
    }
}
