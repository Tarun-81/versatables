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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Tab;

/**
 * Quote information tab
 */
class Info extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\AbstractQuote implements
    \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Customer form factory
     * @var \Magento\Customer\Model\Metadata\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * Json encoder
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * Address service
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Framework\Locale\CurrencyInterface
     */
    protected $_localeCurrency;

    /**
     * @var \Magento\Customer\Model\Address\Mapper
     */
    protected $addressMapper;

    /**
     * Constructor
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Cart2Quote\Quotation\Model\Quote $quoteCreate
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Locale\CurrencyInterface $localeCurrency
     * @param \Magento\Customer\Model\Address\Mapper $addressMapper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Cart2Quote\Quotation\Model\Quote $quoteCreate,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Customer\Model\Metadata\FormFactory $customerFormFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_customerFormFactory = $customerFormFactory;
        $this->customerRepository = $customerRepository;
        $this->_localeCurrency = $localeCurrency;
        $this->addressMapper = $addressMapper;
        $this->priceCurrency = $priceCurrency;
        $this->_sessionQuote = $sessionQuote;
        $this->_quoteCreate = $quoteCreate;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Retrieve source model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getSource()
    {
        return $this->getQuote();
    }

    /**
     * Retrieve quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Retrieve quote totals block settings
     * @return array
     */
    public function getQuoteTotalData()
    {
        return [
            'can_display_total_due' => true,
            'can_display_total_paid' => true,
            'can_display_total_refunded' => true
        ];
    }

    /**
     * Get quote info data
     * @return array
     */
    public function getQuoteInfoData()
    {
        return ['no_use_quote_link' => true];
    }

    /**
     * Get tracking html
     * @return string
     */
    public function getTrackingHtml()
    {
        return $this->getChildHtml('quote_tracking');
    }

    /**
     * Get items html
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('quote_items');
    }

    /**
     * Get payment html
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('quote_payment');
    }

    /**
     * View URL getter
     * @param int $quoteId
     * @return string
     */
    public function getViewUrl($quoteId)
    {
        return $this->getUrl('quotation/*/*', ['quote_id' => $quoteId]);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Information');
    }

    /**
     * ######################## TAB settings #################################
     */

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Quote Information');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrieve url for loading blocks
     * @return string
     */
    public function getLoadBlockUrl($quoteId)
    {
        return $this->getUrl('quotation/quote_view/loadBlock', ['quote_id' => $quoteId]);
    }

    /**
     * Retrieve url for form submiting
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('quotation/quote/save');
    }

    /**
     * Retrieve url for form sending
     * @return string
     */
    public function getSendUrl()
    {
        return $this->getUrl('quotation/quote/send');
    }

    /**
     * Get quote data jason
     * @return string
     */
    public function getQuoteDataJson()
    {
        $data = [];
        if ($this->getCustomerId()) {
            $data['customer_id'] = $this->getCustomerId();
            $data['addresses'] = [];

            $addresses = $this->customerRepository->getById($this->getCustomerId())->getAddresses();

            foreach ($addresses as $address) {
                $addressForm = $this->_customerFormFactory->create(
                    'customer_address',
                    'adminhtml_customer_address',
                    $this->addressMapper->toFlatArray($address)
                );
                $data['addresses'][$address->getId()] = $addressForm->outputData(
                    \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_JSON
                );
            }
        }
        if ($this->getStoreId() !== null) {
            $data['store_id'] = $this->getStoreId();
            $currency = $this->_localeCurrency->getCurrency($this->getStore()->getCurrentCurrencyCode());
            $symbol = $currency->getSymbol() ? $currency->getSymbol() : $currency->getShortName();
            $data['currency_symbol'] = $symbol;
            $data['shipping_method_reseted'] = !(bool)$this->getQuote()->getShippingAddress()->getShippingMethod();
            $data['payment_method'] = $this->getQuote()->getPayment()->getMethod();
        }

        return $this->_jsonEncoder->encode($data);
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
     * Retrieve store model object
     * @return \Magento\Store\Model\Store
     */
    public function getStore()
    {
        return $this->_getSession()->getStore();
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
     * Retrieve formated price
     * @param float $value
     * @return string
     */
    public function formatPrice($value)
    {
        return $this->priceCurrency->format(
            $value,
            true,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            $this->getStore()
        );
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
                \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
                $this->getStore()
            )
            : $this->priceCurrency->convert($value, $this->getStore());
    }

    /**
     * Constructor
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('quotation_quote_view_form');
    }
}
