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

use Magento\Quote\Model\Quote\Item;

/**
 * Class Column
 * @package Cart2Quote\Quotation\Block\Quote\Email\Items\Quote
 */
class Column extends DefaultQuote
{
    /**
     * Get the item from the parent block
     *
     * @return \Magento\Quote\Model\Quote\Item
     * @throws \Exception
     */
    public function getItem()
    {
        if ($parentBlock = $this->getParentBlock()) {
            return $parentBlock->getItem();
        } else {
            throw new \Exception('Undefined quote item in block ' . $this->getNameInLayout());
        }
    }

    /**
     * Get the quote from the parent block
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     * @throws \Exception
     */
    public function getQuote()
    {
        if ($parentBlock = $this->getParentBlock()) {
            return $parentBlock->getQuote();
        } else {
            throw new \Exception('Undefined quote in block ' . $this->getNameInLayout());
        }
    }
}
