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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items;

/**
 * Class FooterRenderer
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items
 */
class FooterRenderer extends DefaultRenderer
{
    /**
     * Retrieve rendered column html content
     *
     * @param string $column the column key
     * @return string
     */
    public function getColumnFooterHtml($column)
    {
        $block = $this->getColumnRenderer($column, self::DEFAULT_TYPE);

        if ($block instanceof FooterInterface) {
            return $block->toFooterHtml();
        }
        return '&nbsp;';
    }
}