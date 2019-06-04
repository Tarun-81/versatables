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
 * Class Section
 * @package Cart2Quote\Quotation\Model\Quote
 */
class Section extends \Magento\Framework\Model\AbstractExtensibleModel implements \Cart2Quote\Quotation\Api\Data\Quote\SectionInterface
{
    /**
     * @return int
     */
    public function getSectionId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SECTION_ID);
    }

    /**
     * @return int
     */
    public function getQuoteId()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID);
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::LABEL);
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->getData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER);
    }

    /**
     * @param int $sectionId
     * @return $this
     */
    public function setSectionId($sectionId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SECTION_ID, $sectionId);
        return $this;
    }

    /**
     * @param int $quoteId
     * @return $this
     */
    public function setQuoteId($quoteId)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::QUOTE_ID, $quoteId);
        return $this;
    }

    /**
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::LABEL, $label);
        return $this;
    }

    /**
     * @param string $sortOrder
     * @return $this
     */
    public function setSortOrder($sortOrder)
    {
        $this->setData(\Cart2Quote\Quotation\Api\Data\Quote\SectionInterface::SORT_ORDER, $sortOrder);
        return $this;
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote\Section');
    }
}