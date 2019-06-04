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


/**
 * Quotation Email quote items
 */

namespace Cart2Quote\Quotation\Block\Quote\Email;

/**
 * Class Items
 * @package Cart2Quote\Quotation\Block\Quote\Email
 */
class Items extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $cart2QuoteHelper;

    /**
     * Items constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper
     * @param array $data \
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $cart2QuoteHelper,
        array $data = []
    ) {
        $this->cart2QuoteHelper = $cart2QuoteHelper;
        parent::__construct($context, $data);
    }

    /**
     * Check disabled product remark field
     * @return boolean
     */
    public function isProductRemarkDisabled()
    {
        return $this->cart2QuoteHelper->isProductRemarkDisabled();
    }

    /**
     * @return array
     */
    public function getSections()
    {
        return $this->getQuote()->getSections(true);
    }
}
