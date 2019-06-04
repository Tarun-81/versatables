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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns;

/**
 * Class BundleDiscount
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\Columns
 */
class BundleDiscount extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Items\DefaultRenderer
{
    /**
     * Get total discount for a bundle
     *
     * @param bool $strong
     * @param string $separator
     * @return float
     */
    public function getTotalDiscount($strong = false, $separator = '<br />')
    {
        $totalDiscount = 0;
        $baseTotalDiscount = 0;
        foreach ($this->getItem()->getChildren() as $item) {
            $totalDiscount += $item->getDiscountAmount();
            $baseTotalDiscount += $item->getBaseDiscountAmount();
        }

        return $this->displayPrices(
            $baseTotalDiscount,
            $totalDiscount,
            $strong,
            $separator
        );
    }
}