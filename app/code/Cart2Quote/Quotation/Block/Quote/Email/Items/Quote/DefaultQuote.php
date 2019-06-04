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

namespace Cart2Quote\Quotation\Block\Quote\Email\Items\Quote;

use Magento\Quote\Model\Quote\Item as QuoteItem;

/**
 * Quotation Quote Email items default renderer
 */
class DefaultQuote extends \Cart2Quote\Quotation\Block\Quote\Email\Items\DefaultItems
{
    /**
     * @return array
     */
    public function getItemOptions()
    {
        $result = [];
        $item = $this->getItem();
        $product = $item->getProduct();
        $options = $product->getTypeInstance()->getOrderOptions($product);
        if ($options) {
            if (isset($options['options'])) {
                $result = array_merge($result, $options['options']);
            }
            if (isset($options['additional_options'])) {
                $result = array_merge($result, $options['additional_options']);
            }
            if (isset($options['attributes_info'])) {
                $result = array_merge($result, $options['attributes_info']);
            }
        }

        return $result;
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    public function getSku($item)
    {
        if ($item->getProductOptionByCode('simple_sku')) {
            return $item->getProductOptionByCode('simple_sku');
        } else {
            return $item->getSku();
        }
    }

    /**
     * Get the html for item price
     * @param QuoteItem $item
     * @return string
     */
    public function getItemPrice($item)
    {
        $block = $this->getLayout()->getBlock('item_row_total');
        $block->setItem($item);
        return $block->toHtml();
    }
}
