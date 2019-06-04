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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View;

/**
 * View quote expiry date calendar
 */
class ExpiryDate extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\AbstractView
{
    /**
     * ExpiryDate constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        array $data
    ) {
        parent::__construct(
            $context,
            $sessionQuote,
            $quoteCreate,
            $orderCreate,
            $priceCurrency,
            $registry,
            $data
        );
    }

    /**
     * Get header css class
     * @return string
     */
    public function getHeaderCssClass()
    {
        return 'head-comment';
    }

    /**
     * Get header text
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Expiry Date');
    }

    /**
     * Check expiry date active status
     * @return string
     */
    public function isActiveExpiryDate()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW,
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN
        ];

        $quote = $this->getQuote();
        if (in_array($quote->getStatus(), $availableStatus)) {
            return '';
        } else {
            return 'disabled';
        }
    }
}
