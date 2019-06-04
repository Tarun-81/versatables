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

namespace Cart2Quote\Quotation\Model\Quote\Relation;

use Magento\Framework\Model\ResourceModel\Db\VersionControl\RelationInterface;

class TierItem implements RelationInterface
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\TierItemFactory
     */
    protected $tierItemFactory;

    /**
     * AddProduct constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
    ) {
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->tierItemFactory = $tierItemFactory;
    }


    /**
     * Process object relations
     *
     * @param \Magento\Framework\Model\AbstractModel|\Magento\Quote\Model\Quote $object
     * @return void
     */
    public function processRelation(\Magento\Framework\Model\AbstractModel $object)
    {
        if (!$this->isQuotation($object)) {
            return;
        }

        /** @var \Magento\Quote\Model\Quote\Item $item */
        foreach ($object->getAllItems() as &$item) {
            if (!$this->hasCurrentTierItemId($item)) {
                $tierItemCollectionFactory = $this->tierItemCollectionFactory->create();
                if (!$tierItemCollectionFactory->tierExists($item->getId(), $item->getQty())) {
                    $existingTierItemCollection = $item->getTierItems();
                    if ($existingTierItemCollection
                        instanceof \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection) {
                        $this->processExistingUpdatedQuoteItem($existingTierItemCollection, $item);
                    } else {
                        $this->processNewTierItems($item);
                    }
                }
            }
        }
    }

    /**
     * Check if the quote is a quotation quote
     *
     * @param $object
     * @return bool
     */
    protected function isQuotation($object)
    {
        return $object->getId() && $object instanceof \Cart2Quote\Quotation\Model\Quote;
    }

    /**
     * Checks if the item has current tier item id
     *
     * @param $item
     * @return bool
     */
    protected function hasCurrentTierItemId($item)
    {
        return $item->getId() && !$item->isDeleted() && $item->getCurrentTierItemId();
    }

    /**
     * Process existing quote item that has been updated (different configuration)
     *
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\Collection $existingTierItemCollection
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    protected function processExistingUpdatedQuoteItem(&$existingTierItemCollection, &$item)
    {
        $existingTierItemCollection->walk("setItemId", [$item->getId()]);
        $existingTierItemCollection->walk("unsetData", ['id']);
        $existingTierItemCollection->walk("unsetData", ['entity_id']);
        $existingTierItemCollection->save();
        $item->unsetData('tier_items');
        $item->unsetData('current_tier_item');
    }

    /**
     * Process new tiers
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     */
    protected function processNewTierItems(&$item)
    {
        $tierItem = $this->tierItemFactory->createFromItem($item);
        if ($tierItem) {
            $tierItem->save();
        }
        $this->tierItemCollectionFactory->create()->setItemTiers($item);
    }
}
