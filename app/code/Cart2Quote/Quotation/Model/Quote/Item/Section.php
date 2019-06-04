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

namespace Cart2Quote\Quotation\Model\Quote\Item;

/**
 * Class Section
 * @package Cart2Quote\Quotation\Model\Quote\Item
 */
class Section extends \Magento\Framework\Model\AbstractExtensibleModel implements \Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface
{
    /**
     * @return int
     */
    public function getSectionId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ID);
    }

    /**
     * @return int
     */
    public function SectionItemId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ITEM_ID);
    }

    /**
     * @return int
     */
    public function getItemId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::ITEM_ID);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SORT_ORDER);
    }

    /**
     * @param int $sectionId
     * @return $this
     */
    public function setSectionId($sectionId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SECTION_ID, $sectionId);
        return $this;
    }

    /**
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::ITEM_ID, $itemId);
        return $this;
    }

    /**
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\Item\SectionInterface::SORT_ORDER, $sortOrder);
        return $this;
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section');
    }
}