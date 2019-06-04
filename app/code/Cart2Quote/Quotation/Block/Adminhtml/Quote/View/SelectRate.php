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
 * Class SelectRate
 * @package Cart2Quote\Quotation\Block\Adminhtml\Quote\View
 */
class SelectRate extends \Magento\Sales\Block\Adminhtml\Order\Create\Data
{
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
     * Totals constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Framework\Registry $registry
     * @param \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository,
        array $data
    ) {
        $this->registry = $registry;
        $this->quoteRepository = $quoteRepository;
        parent::__construct(
            $context,
            $sessionQuote,
            $orderCreate,
            $priceCurrency,
            $currencyFactory,
            $localeCurrency,
            $data
        );
    }

    /**
     * Retrieve store model object
     *
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->getQuote()->getStore();
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

}
