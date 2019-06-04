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

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Interface QuoteTierItemInterface
 * @package Cart2Quote\Quotation\Api\Data
 */
interface QuoteTierItemInterface extends \Magento\Framework\Api\ExtensibleDataInterface
{
    /**
     * Entity ID
     */
    const ENTITY_ID = 'entity_id';

    /**
     * Item ID.
     */
    const ITEM_ID = 'item_id';

    /**
     * Tier Qty
     */
    const QTY = 'qty';

    /**
     * Custom price
     */
    const CUSTOM_PRICE = 'custom_price';

    /**
     * Base custom Price
     */
    const BASE_CUSTOM_PRICE = 'base_custom_price';

    /**
     * Original Price
     */
    const ORIGINAL_PRICE = 'original_price';

    /**
     * Base original Price
     */
    const BASE_ORIGINAL_PRICE = 'base_original_price';

    /**
     * Cost Price
     */
    const COST_PRICE = 'cost_price';

    /**
     * Base cost Price
     */
    const BASE_COST_PRICE = 'base_cost_price';

    /**
     * Row Total
     */
    const ROW_TOTAL = 'row_total';

    /**
     * Base Row Total
     */
    const BASE_ROW_TOTAL = 'base_row_total';

    /**
     * Row Total
     */
    const DISCOUNT_AMOUNT = 'discount_amount';

    /**
     * Base Row Total
     */
    const BASE_DISCOUNT_AMOUNT = 'base_discount_amount';

    /**
     * Comment per line item
     */
    const COMMENT = 'comment';

    /**
     * Item has comment
     */
    const ITEM_HAS_COMMENT = 'item_has_comment';

    /**
     * Make Optional
     */
    const MAKE_OPTIONAL = 'make_optional';

    /**
     * Quote item
     */
    const QUOTE_ITEM = 'item';

    /**
     * Set entity id
     *
     * @return int
     */
    public function getEntityId();

    /**
     * Set item id
     *
     * @return int
     */
    public function getItemId();

    /**
     * Set qty
     *
     * @return float
     */
    public function getQty();

    /**
     * Get custom price
     *
     * @return float
     */
    public function getCustomPrice();

    /**
     * Set entity id
     *
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId);

    /**
     * Set item id
     *
     * @param int $itemId
     * @return $this
     */
    public function setItemId($itemId);

    /**
     * Set qty
     *
     * @param float $qty
     * @return $this
     */
    public function setQty($qty);

    /**
     * Set custom price
     *
     * @param float $customPrice
     * @return $this
     */
    public function setCustomPrice($customPrice);

    /**
     * Set base original price
     *
     * @return float
     */
    public function getBaseOriginalPrice();

    /**
     * Set base original price
     *
     * @param float $baseOriginalPrice
     * @return $this
     */
    public function setBaseOriginalPrice($baseOriginalPrice);

    /**
     * Get Base Custom Price
     *
     * @return float
     */
    public function getBaseCustomPrice();

    /**
     * Set Base Custom Price
     *
     * @param float $baseCustomPrice
     * @return $this
     */
    public function setBaseCustomPrice($baseCustomPrice);

    /**
     * Get original price
     *
     * @return float
     */
    public function getOriginalPrice();

    /**
     * Set original price
     *
     * @param float $originalPrice
     * @return $this
     */
    public function setOriginalPrice($originalPrice);

    /**
     * Get cost price
     *
     * @return float
     */
    public function getCostPrice();

    /**
     * Set code price
     *
     * @param float $costPrice
     * @return $this
     */
    public function setCostPrice($costPrice);

    /**
     * Get base cost price
     *
     * @return float
     */
    public function getBaseCostPrice();

    /**
     * Set base code price
     *
     * @param float $baseCostPrice
     * @return $this
     */
    public function setBaseCostPrice($baseCostPrice);

    /**
     * Get row total
     *
     * @return float
     */
    public function getRowTotal();

    /**
     * Set row total
     *
     * @param float $rowTotal
     * @return $this
     */
    public function setRowTotal($rowTotal);

    /**
     * Get row total
     *
     * @return float
     */
    public function getBaseRowTotal();

    /**
     * Set base row total
     *
     * @param float $baseRowTotal
     * @return $this
     */
    public function setBaseRowTotal($baseRowTotal);

    /**
     * Get discount amount
     *
     * @return float
     */
    public function getDiscountAmount();

    /**
     * Set discount amount
     *
     * @param float $discountAmount
     * @return $this
     */
    public function setDiscountAmount($discountAmount);

    /**
     * Get base discount amount
     *
     * @return float
     */
    public function getBaseDiscountAmount();

    /**
     * Set base discount amount
     *
     * @param float $baseDiscountAmount
     * @return $this
     */
    public function setBaseDiscountAmount($baseDiscountAmount);

    /**
     * Make optional
     *
     * @return boolean
     */
    public function getMakeOptional();

    /**
     * Set make optional
     *
     * @param boolean $makeOptional
     * @return $this
     */
    public function setMakeOptional($makeOptional);

    /**
     * Get item
     *
     * @return \Magento\Quote\Model\Quote\Item
     */
    public function getItem();

    /**
     * Set item
     *
     * @param \Magento\Quote\Model\Quote\Item $item
     * @return $this
     */
    public function setItem(\Magento\Quote\Model\Quote\Item $item);
}
