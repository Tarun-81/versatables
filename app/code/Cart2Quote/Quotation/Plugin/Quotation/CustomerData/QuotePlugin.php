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

namespace Cart2Quote\Quotation\Plugin\Quotation\CustomerData;

/**
 * Class QuotePlugin
 * @package Cart2Quote\Quotation\Plugin\Quotation\CustomerData
 */
class QuotePlugin extends \Magento\Tax\Plugin\Checkout\CustomerData\Cart
{
    /**
     * @var QuotePlugin|null
     */
    protected $quote = null;

    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * QuotePlugin constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Tax\Block\Item\Price\Renderer $itemPriceRenderer,
        \Cart2Quote\Quotation\Model\Session $quotationSession
    ) {
        $this->quotationSession = $quotationSession;
        parent::__construct($checkoutSession, $checkoutHelper, $itemPriceRenderer);
    }

    /**
     * Get active quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        if (null === $this->quote) {
            $this->quote = $this->quotationSession->getQuote();
        }

        return $this->quote;
    }
}
