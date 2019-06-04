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

namespace Cart2Quote\Quotation\CustomerData;

use Magento\Framework\Stdlib\DateTime;

/**
 * Quote source
 */
class Quote extends \Magento\Checkout\CustomerData\Cart
{
    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quotationSession;

    /**
     * Core store config
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var Quote|null
     */
    protected $_quote = null;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @param DateTime $dateTime
     * @param \Cart2Quote\Quotation\Model\Session $checkoutSession
     * @param \Magento\Catalog\Model\ResourceModel\Url $catalogUrl
     * @param \Cart2Quote\Quotation\Model\QuotationCart $checkoutCart
     * @param \Magento\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param array $data
     */
    public function __construct(
        DateTime $dateTime,
        \Cart2Quote\Quotation\Model\Session $checkoutSession,
        \Magento\Catalog\Model\ResourceModel\Url $catalogUrl,
        \Cart2Quote\Quotation\Model\QuotationCart $checkoutCart,
        \Magento\Checkout\Helper\Data $checkoutHelper,
        \Magento\Checkout\CustomerData\ItemPoolInterface $itemPoolInterface,
        \Magento\Framework\View\LayoutInterface $layout,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        array $data = []
    ) {
        parent::__construct(
            $checkoutSession,
            $catalogUrl,
            $checkoutCart,
            $checkoutHelper,
            $itemPoolInterface,
            $layout,
            $data
        );
        $this->_dateTime = $dateTime;
        $this->_quotationSession = $checkoutSession;
        $this->_scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getSectionData()
    {
        $totals = $this->getQuote()->getTotals();

        return [
            'summary_count' => $this->getSummaryCount(),
            'subtotal' => isset($totals['subtotal'])
                ? $this->checkoutHelper->formatPrice($totals['subtotal']->getValue())
                : 0,
            'possible_onepage_checkout' => false,
            'quote_id' => $this->getQuote()->getId(),
            'last_update' => $this->_dateTime->gmDate('Y-m-d H:i:s', (new \DateTime())->getTimestamp()),
            'items' => $this->getRecentItems(),
            'extra_actions' => $this->layout->createBlock('Magento\Catalog\Block\ShortcutButtons')->toHtml(),
            'isGuestCheckoutAllowed' => $this->isGuestCheckoutAllowed(),
        ];
    }

    /**
     * Get active quote
     * @return Quote
     */
    public function getQuote()
    {
        if (null === $this->_quote) {
            $this->_quote = $this->_quotationSession->getQuote();
        }

        return $this->_quote;
    }

    /**
     * Get quote items qty
     * @return int|float
     */
    protected function getSummaryCount()
    {
        $useQty = $this->_scopeConfig->getValue(
            'checkout/cart_link/use_qty',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if ($useQty) {
            $return = $this->getQuote()->getItemsQty() * 1;
        } else {
            $return = $this->getQuote()->getItemsCount() * 1;
        }

        return $return;
    }
}
