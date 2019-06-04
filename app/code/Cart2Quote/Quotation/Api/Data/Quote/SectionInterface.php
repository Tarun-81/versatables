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

namespace Cart2Quote\Quotation\Api\Data\Quote;

/**
 * Interface SectionInterface
 * @package Cart2Quote\Quotation\Api\Data\Quote
 */
interface SectionInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     *
     */
    const SECTION_ID = "section_id";
    /**
     *
     */
    const QUOTE_ID = "quote_id";
    /**
     *
     */
    const LABEL = "label";
    /**
     *
     */
    const SORT_ORDER = "sort_order";

    /**
     * @return int
     */
    public function getSectionId();

    /**
     * @return int
     */
    public function getQuoteId();

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return int
     */
    public function getSortOrder();

    /**
     * @param int $sectionId
     * @return self
     */
    public function setSectionId($sectionId);

    /**
     * @param int $quoteId
     * @return self
     */
    public function setQuoteId($quoteId);

    /**
     * @param string $label
     * @return self
     */
    public function setLabel($label);

    /**
     * @param string $sortOrder
     * @return self
     */
    public function setSortOrder($sortOrder);
}