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

namespace Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions;

use Cart2Quote\Quotation\Helper\QuotationCart;
use Magento\Checkout\Helper\Cart;
use Magento\Framework\View\Element\Template;

/**
 * Class Remove
 * @package Cart2Quote\Quotation\Block\Checkout\Cart\Item\Renderer\Actions
 */
class Remove extends \Magento\Checkout\Block\Cart\Item\Renderer\Actions\Generic
{
    /**
     * @var Cart
     */
    protected $cartHelper;

    /**
     * @param Template\Context $context
     * @param QuotationCart $cartHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        QuotationCart $cartHelper,
        array $data = []
    ) {
        $this->cartHelper = $cartHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get delete item POST JSON
     * @return string
     */
    public function getDeletePostJson()
    {
        return $this->cartHelper->getDeletePostJson($this->getItem());
    }
}
