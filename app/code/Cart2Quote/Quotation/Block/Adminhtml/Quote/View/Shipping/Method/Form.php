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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Shipping\Method;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml quotation quote view shipping method form block
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\Create\Shipping\Method\Form
{
    /**
     * Currency Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Currency
     */
    protected $currencyHelper;

    /**
     * Registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * Quote Repository
     *
     * @var \Cart2Quote\Quotation\Api\QuoteRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * Form constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Tax\Helper\Data $taxData
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository
     * @param \Cart2Quote\Quotation\Helper\Currency $currencyHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository,
        \Cart2Quote\Quotation\Helper\Currency $currencyHelper,
        array $data
    ) {
        $this->registry = $registry;
        $this->quoteRepository = $quoteRepository;
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $taxData, $data);
    }

    /**
     * Get shipping price
     * @param float $price
     * @param bool $flag
     * @return float|string
     */
    public function getShippingPrice($price, $flag)
    {
        if ($this->getQuote()->isCurrencyDifferent()) {
            return $this->currencyHelper->formatPrice(
                $this->getQuote(),
                $this->getPrice($price, $flag)
            );
        } else {
            return parent::getShippingPrice($price, $flag);
        }
    }

    /**
     * Retrieve quote model object
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        if (!$quote = $this->registry->registry('current_quote')) {
            $this->registry->register(
                'current_quote',
                $this->quoteRepository->get($this->getRequest()->getParam('quote_id'))
            );
        }

        return $quote;
    }

    /**
     * Get the shipping price
     *
     * @param float $price
     * @param bool $flag
     * @return float
     */
    public function getPrice($price, $flag)
    {
        if ($this->getAddress()->getQuote()) {
            $quote = $this->getAddress()->getQuote();
        } else {
            $quote = $this->getQuote();
        }

        return $this->_taxData->getShippingPrice(
            $price,
            $flag,
            $this->getAddress(),
            null,
            $quote->getStore()
        );
    }

    /**
     * Is quotation shipping
     *
     * @param string $code
     * @return bool
     */
    public function isQuotationShipping($code)
    {
        return $code == \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE;
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotation_quote_view_shipping_method_form');
    }
}
