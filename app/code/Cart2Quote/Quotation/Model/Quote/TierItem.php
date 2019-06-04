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

namespace Cart2Quote\Quotation\Model\Quote;

/**
 * Class TierItem
 * @package Cart2Quote\Quotation\Model\Quote
 */
class TierItem extends \Magento\Sales\Model\AbstractModel
    implements \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_item';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'item';

    /**
     * Quote item collection factory
     *
     * @var \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory
     */
    protected $quoteItemCollectionFactory;

    /**
     * Tier item collection factory
     *
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * TierItem constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem $resource
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $resourceCollection
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem $resource = null,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $resourceCollection = null,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->quoteItemCollectionFactory = $quoteItemCollectionFactory;
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
    }

    /**
     * Get item id
     *
     * @return int $itemId
     */
    public function getItemId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID);
    }

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * Set entity id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID);
    }

    /**
     * Set base original price
     *
     * @return float
     */
    public function getBaseOriginalPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ORIGINAL_PRICE);
    }

    /**
     * Get Base Custom Price
     *
     * @return float
     */
    public function getBaseCustomPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_CUSTOM_PRICE);
    }

    /**
     * Set cost price
     *
     * @param float $costPrice
     * @return $this
     */
    public function setCostPrice($costPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::COST_PRICE, $costPrice);

        return $this;
    }

    /**
     * Set base cost price
     *
     * @param float $baseCostPrice
     * @return $this
     */
    public function setBaseCostPrice($baseCostPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_COST_PRICE, $baseCostPrice);

        return $this;
    }

    /**
     * Get cost price
     *
     * @return float
     */
    public function getRowTotal()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ROW_TOTAL);
    }

    /**
     * Set cost price
     *
     * @param float $costPrice
     * @return $this
     */
    public function setRowTotal($costPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ROW_TOTAL, $costPrice);

        return $this;
    }

    /**
     * Get base cost price
     *
     * @return float
     */
    public function getBaseRowTotal()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ROW_TOTAL);
    }

    /**
     * Set base cost price
     *
     * @param float $baseCostPrice
     * @return $this
     */
    public function setBaseRowTotal($baseCostPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ROW_TOTAL, $baseCostPrice);

        return $this;
    }

    /**
     * Get discount amount
     *
     * @return float
     */
    public function getDiscountAmount()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::DISCOUNT_AMOUNT);
    }

    /**
     * Set discount amount
     *
     * @param float $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::DISCOUNT_AMOUNT, $discountAmount);

        return $this;
    }

    /**
     * Get base discount amount
     *
     * @return float
     */
    public function getBaseDiscountAmount()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_DISCOUNT_AMOUNT);
    }

    /**
     * Set base discount amount
     *
     * @param float $baseDiscountAmount
     * @return $this
     */
    public function setBaseDiscountAmount($baseDiscountAmount)
    {
        $this->setData(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_DISCOUNT_AMOUNT,
            $baseDiscountAmount
        );

        return $this;
    }

    /**
     * Make optional
     *
     * @return boolean
     */
    public function getMakeOptional()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::MAKE_OPTIONAL);
    }

    /**
     * Set make optional
     *
     * @param boolean $makeOptional
     * @return $this
     */
    public function setMakeOptional($makeOptional)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::MAKE_OPTIONAL, $makeOptional);

        return $this;
    }

    /**
     * Set selected
     *
     * @return \Magento\Quote\Model\Quote\Item $item
     */
    public function setSelected()
    {
        $quoteItem = $this->getItem();
        $quoteItem = $this->resetQuoteItem($quoteItem);
        $quoteItem->setCurrentTierItem($this);
        $quoteItem->setQty($this->getQty());
        $quoteItem->setCustomPrice($this->getCustomPrice());
        $quoteItem->setCalculationPrice($this->getCustomPrice());
        $quoteItem->setOriginalPrice($this->getOriginalPrice());
        $quoteItem->setBaseCost($this->getBaseCostPrice());
        $quoteItem->setCost($this->getCostPrice());

        if ($quoteItem->getProductType() == \Magento\Catalog\Model\Product\Type::TYPE_BUNDLE) {
            $quoteItem->getProduct()->setPriceType(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED);
            $quoteItem->setNoDiscount(1);
            $this->setSelectedChild($quoteItem);
        }

        return $quoteItem;
    }

    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getItem()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QUOTE_ITEM);
    }

    /**
     * Reset the quote item prices
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return \Magento\Quote\Model\Quote\Item
     */
    protected function resetQuoteItem($quoteItem)
    {
        return $quoteItem
            ->setPrice(0)
            ->setBasePrice(0)
            ->setCustomPrice(0)
            ->setBaseCustomPrice(0)
            ->setOriginalCustomPrice(0)
            ->setBaseCalculationPrice(0)
            ->setCalculationPrice(0)
            ->setDiscountAmount(0)
            ->setBaseDiscountAmount(0)
            ->setDiscountPercent(0)
            ->setBaseRowTotal(0)
            ->setBaseRowTotalInclTax(0)
            ->setBaseRowTotalWithDiscount(0)
            ->setRowTotal(0)
            ->setRowTotalInclTax(0)
            ->setRowTotalWithDiscount(0)
            ->setBaseCost(0)
            ->setPriceInclTax(0)
            ->setBasePriceInclTax(0)
            ->setCost(0)
            ->setRowTotalWithDiscount(0)
            ->setTaxAmount(0)
            ->setBaseTaxAmount(0)
            ->setTaxPercent(0)
            ->setBaseTaxCalculationPrice(0)
            ->setTaxCalculationPrice(0);
    }

    /**
     * Get qty
     *
     * @return float $qty
     */
    public function getQty()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY);
    }

    /**
     * Get original price
     *
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_PRICE);
    }

    /**
     * Get base cost price
     *
     * @return float
     */
    public function getBaseCostPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_COST_PRICE);
    }

    /**
     * Get cost price
     *
     * @return float
     */
    public function getCostPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::COST_PRICE);
    }

    /**
     * Set selected for child
     * The child tier prices are calculated based on the parent tier item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    protected function setSelectedChild($quoteItem)
    {
        $totalChildPrice = $this->calculateChildTotalPrice($quoteItem->getChildren());
        $totalCalculatedChildPrice = 0;

        /** @var \Magento\Quote\Model\Quote\Item $child */
        foreach ($quoteItem->getChildren() as &$child) {
            $childPrice = $this->calculateChildPrice($child, $totalChildPrice);
            $totalCalculatedChildPrice += ((float)$childPrice * (float)$child->getQty());

            /** @var TierItem $tier */
            if ($tier = $child->getCurrentTierItem()) {
                $tier->setItem($child);
                $tier->setCustomPrice($childPrice);
                $child = $tier->setSelected(); // recursive
                $child->setNoDiscount(1);
            }
        }

        $this->checkBundleRoundingIssue($totalCalculatedChildPrice);

        return $this;
    }

    /**
     * Get total child price
     *
     * @param $children
     * @return int
     */
    protected function calculateChildTotalPrice($children)
    {
        $totalPrice = 0;
        foreach ($children as $child) {
            $dataPriceObject = $child->getCurrentTierItem();
            if ($dataPriceObject == null) {
                $dataPriceObject = $child;
            }

            if ($dataPriceObject->getRowTotal()) {
                $totalPrice += (float)$dataPriceObject->getRowTotal();
            } else {
                $totalPrice += ((float)$dataPriceObject->getPrice() * (float)$dataPriceObject->getQty());
            }
        }

        return $totalPrice;
    }

    /**
     * Calculate the child price
     *
     * @param \Magento\Quote\Model\Quote\Item $child
     * @param $totalChildPrice
     * @return float
     */
    public function calculateChildPrice(\Magento\Quote\Model\Quote\Item $child, $totalChildPrice)
    {
        $dataPriceObject = $child->getCurrentTierItem();

        if ($dataPriceObject == null) {
            $dataPriceObject = $child;
        }

        $percentage = (double)$this->calculatePercentage(
            $totalChildPrice,
            $dataPriceObject->getRowTotal()
        );
        $percentage = round($percentage, 4, PHP_ROUND_HALF_DOWN);

        $customPrice = (double)$this->calculatePrice($this->getCustomPrice(), $percentage);
        $childPrice = (double)($customPrice / $dataPriceObject->getQty());
        $childPrice = round($childPrice, 4, PHP_ROUND_HALF_DOWN);

        return $childPrice;
    }

    /**
     * Calculate percentage
     *
     * @param float $total
     * @param $subject
     * @return float
     */
    public function calculatePercentage($total, $subject)
    {
        if ($total <= 0) {
            return $total;
        }

        return $subject / (0.01 * $total);
    }

    /**
     * Calculate price
     *
     * @param float $total
     * @param float $percentage
     * @return float
     */
    public function calculatePrice($total, $percentage)
    {
        return ($total * 0.01) * $percentage;
    }

    /**
     * Set item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
        $this->setItemId($item->getId());
        $item->setCurrentTierItem($this);
        $this->loadPriceOnItem($item);

        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QUOTE_ITEM, $item);

        return $this;
    }

    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID, $itemId);

        return $this;
    }

    /**
     * Load the tier price on the quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @return $this
     */
    public function loadPriceOnItem(&$quoteItem)
    {
        $quoteItem->setCalculationPrice($this->getCustomPrice());
        $quoteItem->setCustomPrice($this->getCustomPrice());
        $quoteItem->setOriginalCustomPrice($this->getCustomPrice());

        return $this;
    }

    /**
     * Get custom price
     *
     * @return float
     */
    public function getCustomPrice()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::CUSTOM_PRICE);
    }

    /**
     * Set custom price
     *
     * @param float $customPrice
     * @return $this
     */
    public function setCustomPrice($customPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::CUSTOM_PRICE, $customPrice);

        return $this;
    }

    /**
     * Adds the rounding differences to the tier item (won't be saved in the DB)
     * You can use this to detect rounding issues for bundles
     *
     * @param $totalCalculatedChildPrice
     */
    protected function checkBundleRoundingIssue($totalCalculatedChildPrice)
    {
        if ($this->getCustomPrice() > $totalCalculatedChildPrice || $this->getCustomPrice() < $totalCalculatedChildPrice) {
            $this->setRoundingOffset($this->getCustomPrice() - $totalCalculatedChildPrice);
        }
    }

    /**
     * Set the tier item data base the quote item values
     *
     * @param $item
     * @param null $tierItemId
     * @param null $qty
     * @return $this
     */
    public function setDataByItem($item, $tierItemId = null, $qty = null)
    {
        $this->setData($item->getData())
            ->setId($tierItemId)
            ->setQty($qty ? $qty : $item->getQty())
            ->setItemId($item->getId());

        if ($tierItemId == null) {
            $this->setNewPrice($item);
        }

        return $this;
    }

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY, $qty);

        return $this;
    }

    /**
     * Set a custom price
     *
     * @param $item
     * @return $this
     */
    protected function setNewPrice($item)
    {
        $fallbackPrice = $item->getPrice();

        if ($item->getBasePrice() > 0) {
            $this->setBaseCustomPrice($item->getBasePrice());
        } else {
            $this->setBaseCustomPrice($fallbackPrice);
        }

        if ($item->getPrice() > 0) {
            $this->setCustomPrice($item->getPrice());
        } else {
            $this->setCustomPrice($fallbackPrice);
        }

        if ($item->getProduct()->getPrice()) {
            $this->setOriginalPrice($item->getProduct()->getPrice());
        } else {
            $this->setOriginalPrice($fallbackPrice);
        }
        
        return $this;
    }

    /**
     * Set base original price
     *
     * @param float $baseOriginalPrice
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_ORIGINAL_PRICE, $baseOriginalPrice);

        return $this;
    }

    /**
     * Set Base Custom Price
     *
     * @param float $baseCustomPrice
     * @return $this
     */
    public function setBaseCustomPrice($baseCustomPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::BASE_CUSTOM_PRICE, $baseCustomPrice);

        return $this;
    }

    /**
     * Set original price
     *
     * @param float $originalPrice
     * @return $this
     */
    public function setOriginalPrice($originalPrice)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ORIGINAL_PRICE, $originalPrice);

        return $this;
    }

    /**
     * Load the tier price on the product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function loadPriceOnProduct(&$product)
    {
        $product->setData('final_price', $this->getCustomPrice());
        $product->setPriceType(\Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED);

        return $this;
    }

    /**
     * Is tier selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return ($this->getItem()->getQty() * 1) == ($this->getQty() * 1);
    }

    /**
     * Init resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem');
    }
}
