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

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class LoadTierItem
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class JoinTierItem implements ObserverInterface
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
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $collection = $observer->getCollection();

        if ($collection instanceof \Magento\Quote\Model\ResourceModel\Quote\Item\Collection) {
            $tierItemTable = $collection->getTable('quotation_quote_tier_item');
            $collection->getSelect()->joinLeft(
                $tierItemTable,
                sprintf(
                    '%s.item_id = main_table.item_id AND %s.qty = main_table.qty',
                    $tierItemTable,
                    $tierItemTable
                ),
                sprintf('%s.entity_id AS current_tier_item_id', $tierItemTable)
            );
        }
    }
}
