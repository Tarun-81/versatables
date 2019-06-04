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

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml quotation quote view abstract tab block
 */
abstract class AbstractTab extends \Magento\Backend\Block\Widget\Tab
{
    /**
     * Session quote
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * Quote create
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_quoteCreate;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->_sessionQuote = $sessionQuote;
        $this->_quoteCreate = $quoteCreate;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve create quote model object
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getCreateQuoteModel()
    {
        return $this->_quoteCreate;
    }

    /**
     * Retrieve quote model object
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Retrieve customer identifier
     * @return int
     */
    public function getCustomerId()
    {
        return $this->_getSession()->getCustomerId();
    }

    /**
     * Retrieve quote session object
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_sessionQuote;
    }

    /**
     * Retrieve store identifier
     * @return int
     */
    public function getStoreId()
    {
        return $this->_getSession()->getStoreId();
    }

    /**
     * Retrieve formated price
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getStore()
        );
    }

    /**
     * Retrieve store model object
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
    }

    /**
     * Convert price
     * @param float $value
     * @param bool $format
     * @return float
     */
    public function convertPrice($value, $format = true)
    {
        return $format
            ? $this->priceCurrency->convertAndFormat(
                $value,
                true,
                PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->getStore()
            )
            : $this->priceCurrency->convert($value, $this->getStore());
    }
}
