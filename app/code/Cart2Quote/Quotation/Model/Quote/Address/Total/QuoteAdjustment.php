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

namespace Cart2Quote\Quotation\Model\Quote\Address\Total;

/**
 * Class QuoteAdjustment
 * @package Cart2Quote\Quotation\Model\Quote\Address\Total
 */
class QuoteAdjustment extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Assign quote adjustment amount and label to address object
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        if (!$quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            return [];
        }

        $value = (double)$total->getSubtotal() - (double)$quote->getOriginalSubtotal();

        return [
            'code' => $this->getCode(),
            'title' => __('Quote Adjustment'),
            'value' => $value
        ];
    }
}
