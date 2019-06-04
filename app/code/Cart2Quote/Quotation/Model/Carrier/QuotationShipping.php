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

namespace Cart2Quote\Quotation\Model\Carrier;

/**
 * Quotation shipping model
 */
class QuotationShipping extends \Magento\Shipping\Model\Carrier\AbstractCarrier
    implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    const CODE = 'quotation';

    /**
     * Path to method name in system.xml
     */
    const XML_PATH_CARRIER_QUOTATION_METHOD = 'carriers/quotation/name';

    /**
     * Path to title in system.xml
     */
    const XML_PATH_CARRIER_QUOTATION_TITLE = 'carriers/quotation/title';

    /**
     * Code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Is Fixed
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Rate \Magento\Shipping\Model\Rate\Result Factory
     *
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * Rate Method Factory
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * Quotation Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;
    /**
     * Magento App State
     *
     * @var \Magento\Framework\App\State
     */
    protected $appState;
    /**
     * Quotation Quote Model
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    private $quote;

    /**
     * QuotationShipping constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Framework\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Framework\App\State $appState,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->quote = $quote;
        $this->quoteSession = $quoteSession;
        $this->appState = $appState;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect the shipping rates
     * Only when using a quotation quote the shipping will be collected
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $quoteId = $this->getQuoteId($request);
        $this->quote->load($quoteId);
        $sessionConfigData = $this->getSessionQuoteConfigData($quoteId);

        if (!($this->hasFixedShipping($sessionConfigData) ||
            $this->isBackend() ||
            $this->quote->getIsQuotationQuote() ||
            $this->existsInSession($quoteId))) {
            return false;
        }

        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($sessionConfigData);

        if ($shippingPrice >= 0) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * Get the quote ID
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return int
     */
    protected function getQuoteId(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        $quoteId = 0;
        $allItems = $request->getAllItems();

        if ($allItems && count($allItems)) {
            $quoteItem = reset($allItems);
            $quoteId = $quoteItem->getQuote()->getId();
        }

        return $quoteId;
    }

    /**
     * Get quote data from the session
     *
     * @param int $quoteId
     * @return array
     */
    protected function getSessionQuoteConfigData($quoteId)
    {
        $data = [];
        $configData = $this->quoteSession->getData(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA);
        if (isset($configData[$quoteId])) {
            $data = $configData[$quoteId];
        }

        return $data;
    }

    /**
     * Has fixed shipping price
     *
     * @param array $configData
     * @return bool
     */
    protected function hasFixedShipping($configData)
    {
        return isset($configData['fixed_shipping_price']);
    }

    /**
     * Check if the request is done in the backend
     *
     * @return bool
     */
    protected function isBackend()
    {
        return $this->appState->getAreaCode() == \Magento\Framework\App\Area::AREA_ADMINHTML;
    }

    /**
     * Check if the quote id in the session is the same as the given quote id
     *
     * @param int $quoteId
     * @return bool
     */
    protected function existsInSession($quoteId)
    {
        return $this->quoteSession->getQuoteId() == $quoteId;
    }

    /**
     * Get the shipping price
     *
     * @param array $configData
     * @return float
     */
    protected function getShippingPrice($configData)
    {
        $price = 0;
        if ($this->quote->getFixedShippingPrice()) {
            $price = $this->quote->getFixedShippingPrice();
        } elseif ($this->hasFixedShipping($configData)) {
            $price = $this->getFixedShippingPrice($configData);
        }

        return $price;
    }

    /**
     * Get fixed shipping price
     *
     * @param array $configData
     * @return string
     */
    protected function getFixedShippingPrice($configData)
    {
        return $configData['fixed_shipping_price'];
    }

    /**
     * Create the result method
     *
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    protected function createResultMethod($shippingPrice)
    {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier(self::CODE);
        $method->setCarrierTitle(__($this->getShippingTitle()));

        $method->setMethod(self::CODE);
        $method->setMethodTitle(__($this->getShippingMethod()));
        $method->setMethodDescription(__('Custom Price'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        return $method;
    }

    /**
     * Get allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [self::CODE => __('Quote Shipping')];
    }

    /**
     * Force active
     *
     * @return bool
     */
    public function isActive()
    {
        return true;
    }

    /**
     * Get quotation shipping title
     *
     * @return string
     */
    public function getShippingTitle()
    {
        $title = $this->_scopeConfig->getValue(self::XML_PATH_CARRIER_QUOTATION_TITLE);

        return isset($title) ? $title : 'Quote Shipping';
    }

    /**
     * Get quotation shipping method name
     *
     * @return string
     */
    public function getShippingMethod()
    {
        $method = $this->_scopeConfig->getValue(self::XML_PATH_CARRIER_QUOTATION_METHOD);

        return isset($method) ? $method : 'Custom Price';
    }
}
