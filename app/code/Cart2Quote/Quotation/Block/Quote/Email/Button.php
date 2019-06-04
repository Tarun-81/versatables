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
 * Quotation Email quote Button
 */

namespace Cart2Quote\Quotation\Block\Quote\Email;

use Cart2Quote\Quotation\Model\Quote;

/**
 * Class Button
 * @package Cart2Quote\Quotation\Block\Quote\Email
 */
class Button extends \Magento\Framework\View\Element\Template
{
    /**
     * Data Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * URL builder
     *
     * @var \Magento\Framework\Url
     */
    protected $url;

    /**
     * Button constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Magento\Framework\Url $url
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Magento\Framework\Url $url,
        array $data = []
    ) {
        $this->url = $url;
        $this->quotationHelper = $quotationHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Url for checkout button
     *
     * @return string
     */
    public function getUrlForCheckoutButton()
    {
        /** @var Quote $quote */
        $quote = $this->getQuote();
        $route = $this->getRoute($quote);
        $params = ['quote_id' => $quote->getQuoteId()];

        if ($this->isAutoLoginEnabled()) {
            $params['hash'] = $quote->getUrlHash();
        }

        return $this->url->getUrl($route, $params);
    }

    /**
     * Returns the url for the more options controller
     *
     * @return string
     */
    public function getMoreOptionsUrl()
    {
        /** @var Quote $quote */
        $quote = $this->getQuote();
        $route = 'quotation/quote/checkout_moreoptions';
        $params = ['quote_id' => $quote->getQuoteId()];
        if ($this->isAutoLoginEnabled()) {
            $params['hash'] = $quote->getUrlHash();
        }

        return $this->url->getUrl($route, $params);
    }

    /**
     * Get route depending on the checkout being enabled and the customer being a guest.
     *
     * @param Quote $quote
     * @return string
     */
    private function getRoute(Quote $quote)
    {
        $route = 'quotation/quote/checkout_customer';
        if ($this->isCheckoutDisabled()) {
            $route = 'quotation/quote_checkout/acceptwithoutcheckout';
        } elseif ($quote->getCustomerIsGuest()) {
            $route = 'quotation/quote/checkout_guest';
        }

        return $route;
    }

    /**
     * Get the Label for the button on the proposal email
     *
     * @return string
     */
    public function getFrontendButtonLabel()
    {
        $buttonLabel = __('Proceed to checkout');
        if ($this->isCheckoutDisabled()) {
            $buttonLabel = __('Accept Quotation');
        }

        return $buttonLabel;
    }

    /**
     * Check enabled auto login
     *
     * @return boolean
     */
    public function isAutoLoginEnabled()
    {
        return $this->quotationHelper->isAutoLoginEnabled();
    }

    /**
     * Check disabled checkout
     *
     * @return bool
     */
    public function isCheckoutDisabled()
    {
        return $this->quotationHelper->isCheckoutDisabled();
    }
}
