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

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem;

/**
 * Class Collection
 * @package Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem
 */
class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * @var \Magento\Quote\Model\Quote\Item $_item
     */
    protected $_item;

    /**
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item $item)
    {
        $this->_item = $item;
        $itemId = $item->getId();
        if ($itemId) {
            $this->addFieldToFilter('item_id', $item->getId());
        } else {
            $this->_totalRecords = 0;
            $this->_setIsLoaded(true);
        }
        return $this;
    }

    /**
     * @param $itemId
     * @param $qty
     * @return bool
     */
    public function tierExists($itemId, $qty)
    {
        return $this->addFieldToFilter(
                \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
                $itemId
            )->addFieldToFilter(
                \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY,
                $qty
            )->getSize() > 0;
    }

    /**
     * @param $itemId
     * @return bool
     */
    public function tierExistsForItem($itemId)
    {
        return $this->addFieldToFilter(
                \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
                $itemId)
                ->getSize() > 0;
    }

    /**
     * @param $itemId
     * @param $qty
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTier($itemId, $qty)
    {
        return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID,
            $itemId
        )->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::QTY,
            $qty
        )->getFirstItem();
    }

    /**
     * @param $tierItemId
     * @return \Cart2Quote\Quotation\Model\Quote\TierItem
     */
    public function getTierById($tierItemId)
    {
        return $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID,
            $tierItemId)->getFirstItem();
    }

    /**
     * @param $tierItemIds
     * @return \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection
     */
    public function getTiersByIds($tierItemIds)
    {
        $this->addFieldToFilter(
            \Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ENTITY_ID,
            ['in' => $tierItemIds]
        );

        return $this;
    }

    /**
     * Get an array with the qty as key and record ID as value
     *
     * @param bool $format
     * @return array ['qty' => 'ID']
     */
    public function getQtys($format = true)
    {
        $qtys = [];

        /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $item */
        foreach ($this->getItems() as $item) {
            $qty = $item->getQty();
            if ($format) {
                $qty = $qty * 1; // remove the zeros in the decimal
            }

            $qtys[$qty] = $item->getId();
        }

        return $qtys;
    }

    /**
     * Set tier items to a Quote item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param int $currentTierItemId
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function setItemTiers(\Magento\Quote\Model\Quote\Item $item, $currentTierItemId = 0)
    {
        $this->getTierItemsByItemId($item->getId());

        if ($currentTierItemId > 0) {
            /**
             * @var \Cart2Quote\Quotation\Model\Quote\TierItem $currentTierItem
             */
            $currentTierItem = $this->getItemById($currentTierItemId);
        }

        if (!isset($currentTierItem)) {
            foreach ($this as $tier) {
                if ($tier->getQty() == $item->getQty()) {
                    $currentTierItem = $tier;
                    break;
                }
            }
        }

        if (isset($currentTierItem) && $currentTierItem instanceof \Cart2Quote\Quotation\Model\Quote\TierItem) {
            $currentTierItem->setItem($item);
            $item = $currentTierItem->getItem();
        }

        foreach ($this as $id => $tier) {
            if ($tier->isDeleted()) {
                $this->removeItemByKey($id);
            }
        }

        return $item->setTierItems($this);
    }

    /**
     * Get the tier items by id
     *
     * @param $itemId
     * @param bool $orderByQty
     * @return $this
     */
    public function getTierItemsByItemId($itemId, $orderByQty = true)
    {
        $this->addFieldToFilter(\Cart2Quote\Quotation\Api\Data\QuoteTierItemInterface::ITEM_ID, $itemId);

        if ($orderByQty) {
            $this->addOrder('qty', self::SORT_ORDER_ASC);
        }

        return $this;
    }

    /**
     * Model initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Cart2Quote\Quotation\Model\Quote\TierItem',
            'Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem'
        );
    }

    /**
     * @return $this
     */
    protected function _afterLoad()
    {
        parent::_afterLoad();
        /**
         * @var \Cart2Quote\Quotation\Model\Quote\TierItem $item
         * */
        foreach ($this as $item) {
            if ($this->_item) {
                $item->setItem($this->_item);
            }
        }
        $this->resetItemsDataChanged();
        return $this;
    }
}
