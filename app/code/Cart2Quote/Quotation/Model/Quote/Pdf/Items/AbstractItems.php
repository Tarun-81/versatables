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

namespace Cart2Quote\Quotation\Model\Quote\Pdf\Items;

/**
 * Quote Pdf Items renderer Abstract
 */
abstract class AbstractItems extends \Magento\Sales\Model\Order\Pdf\Items\AbstractItems
{
    /**
     * Core string
     *
     * @var \Magento\Framework\Stdlib\StringUtils
     */
    protected $string;

    /**
     * Set Quote model
     *
     * @param  \Cart2Quote\Quotation\Model\Quote
     * @return $this
     */
    public function setQuote(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        $this->_quote = $quote;

        return $this;
    }

    /**
     * Retrieve quote object
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            throw new \Magento\Framework\Exception\LocalizedException(__('The quote object is not specified.'));
        }

        return $this->_quote;
    }
}
