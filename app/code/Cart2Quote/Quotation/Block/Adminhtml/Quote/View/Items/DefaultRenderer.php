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

use Cart2Quote\Quotation\Model\Quote\TierItem;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Quote\Model\Quote\Item;

/**
 * Class DefaultColumn
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items
 */
class DefaultRenderer extends \Magento\Sales\Block\Adminhtml\Items\Column\DefaultColumn implements FooterInterface
{
    /**
     * Quotation Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;

    /**
     * Empty Quote item
     *
     * @var Item
     */
    protected $emptyQuoteItem;

    /**
     * DefaultColumn constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\OptionFactory $optionFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param Item $emptyQuoteItem
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\OptionFactory $optionFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Magento\Quote\Model\Quote\Item $emptyQuoteItem,
        array $data = []
    ) {
        $this->quote = $quote;
        $this->emptyQuoteItem = $emptyQuoteItem;
        parent::__construct($context, $stockRegistry, $stockConfiguration, $registry, $optionFactory, $data);
    }

    /**
     * OVERWRITE Magento getOrder function
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getOrder()
    {
        return $this->getQuote();
    }

    /**
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (!$quote = $this->_coreRegistry->registry('current_quote')) {
            $this->_coreRegistry->register(
                'current_quote',
                $this->quote->load($this->getRequest()->getParam('quote_id'))
            );
        }

        return $quote;
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
     * Get the footer HTML
     *
     * @return string
     */
    public function toFooterHtml()
    {
        return $this->getChildHtml('footer.' . $this->getNameInLayout());
    }

    /**
     * Get the item count
     *
     * @return int
     */
    public function getItemCount()
    {
        return count($this->getItems());
    }

    /**
     * Get the items
     *
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     * @throws \Exception
     */
    public function getItems()
    {
        return $this->getItemsBlock()->getItems();
    }

    /**
     * Get the item block
     *
     * @return \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\GridItems
     * @throws \Exception
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getItemsBlock()
    {
        if (!$itemBlock = $this->getLayout()->getBlock('items')) {
            throw new \Exception('Quote Items render error: "items"' .
                ' needs to be a child of the block "' . $this->getNameInLayout() . '"');
        }

        return $itemBlock;
    }

    /**
     * Get col span if there are more tier items
     *
     * @param string $columnName
     * @return string
     */
    public function getRowSpan($columnName)
    {
        $html = '';

        if ($this->getTotalTierItemCount() && !$this->isTierColumn($columnName)) {
            $html = sprintf('rowspan="%s"', $this->getTotalTierItemCount());
        }

        return $html;
    }

    /**
     * Get the tier item count
     *
     * @return int
     */
    public function getTotalTierItemCount()
    {
        return count($this->getItem()->getTierItems());
    }

    /**
     * Get item
     *
     * @return Item|QuoteItem
     */
    public function getItem()
    {
        $item = $this->_getData('item');
        if ($item instanceof Item || $item instanceof QuoteItem) {
            return $item;
        } else {
            return $this->emptyQuoteItem;
        }
    }

    /**
     * Check if column is tier column
     *
     * @param string $columnName
     * @return bool
     */
    public function isTierColumn($columnName)
    {
        $tierColumns = $this->getTierColumns();

        return isset($tierColumns[$columnName]) && $tierColumns[$columnName];
    }

    /**
     * Get empty column html
     *
     * @param \Magento\Framework\DataObject $item
     * @param string $column
     * @param null $field
     * @return string
     */
    public function getEmptyColumnHtml(\Magento\Framework\DataObject $item, $column, $field = null)
    {
        $html = '';
        $columnRenderer = $this->getColumnRenderer($column, $item->getProductType());
        $emptyColumn = $columnRenderer->getChildBlock('empty.' . $columnRenderer->getNameInLayout());
        if ($emptyColumn) {
            $html = $emptyColumn->setItem($item)->setField($field)->toHtml();
        }

        return $html;
    }

    /**
     * Get the select tier css class
     *
     * @return string
     */
    public function getSelectedTierClass()
    {
        if ($this->getItem()->getIsSelectedTier()) {
            return 'selected-tier-row';
        } else {
            return '';
        }
    }

    /**
     * Get the item id
     *
     * @return int|null
     */
    public function getItemId()
    {
        return $this->getItem()->getId();
    }

    /**
     * Get the tier item set by the parent
     *
     * @return TierItem|null
     */
    public function getTierItem()
    {
        return $this->getItem()->getTierItem();
    }

    /**
     * Get flag for first tier item
     *
     * @return bool|null
     */
    public function getIsFirstTierItem()
    {
        return $this->getItem()->getIsFirstTierItem();
    }

    /**
     * Get the current tier count (starts from 0)
     *
     * @return int|null
     */
    public function getTierItemCount()
    {
        return $this->getItem()->getTierItemCount();
    }

    /**
     * Get flag for selected tier (the tier selected for this quote item)
     *
     * @return bool|null
     */
    public function getIsSelectedTier()
    {
        return $this->getItem()->getIsSelectedTier();
    }
}
