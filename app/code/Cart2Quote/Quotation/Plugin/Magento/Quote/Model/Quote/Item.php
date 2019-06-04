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

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\Quote;

/**
 * Class Address
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model
 */
class Item
{
    /**
     * Quotation Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Address constructor.
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession
    ) {
        $this->quoteSession = $quoteSession;
    }

    /**
     * Apply tier product price
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterGetProduct($quoteItem, $product)
    {
        $tierItem = $quoteItem->getCurrentTierItem();
        if ($tierItem instanceof \Cart2Quote\Quotation\Model\Quote\TierItem) {
            if ($tierItem->getCustomPrice()) {
                $tierItem->loadPriceOnItem($quoteItem);
                $tierItem->loadPriceOnProduct($product);
            }
        }

        return $product;
    }
}
