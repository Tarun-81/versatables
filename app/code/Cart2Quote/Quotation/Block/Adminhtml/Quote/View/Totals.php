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
 * Adminhtml quotation quote create totals block
 */
class Totals extends \Magento\Sales\Block\Adminhtml\Order\Create\Totals
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
     * Currency helper
     *
     * @var \Cart2Quote\Quotation\Helper\Currency
     */
    protected $currencyHelper;

    /**
     * Default renderer
     * @var string
     */
    protected $_defaultRenderer = 'Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Totals\DefaultTotals';

    /**
     * Totals constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Sales\Model\Config $salesConfig
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
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Sales\Model\Config $salesConfig,
        \Magento\Framework\Registry $registry,
        \Cart2Quote\Quotation\Api\QuoteRepositoryInterface $quoteRepository,
        \Cart2Quote\Quotation\Helper\Currency $currencyHelper,
        array $data
    ) {
        $this->registry = $registry;
        $this->quoteRepository = $quoteRepository;
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $salesData, $salesConfig, $data);
    }

    /**
     * Get header text
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Quote Totals');
    }

    /**
     * Check allow to send new quote confirmation email
     * @return bool
     */
    public function canSendNewQuoteConfirmationEmail()
    {
        return false;
    }

    /**
     * Retrieve formated price
     *
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->currencyHelper->formatPrice($this->getQuote(), $value);
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
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotation_quote_view_totals');
    }

}
