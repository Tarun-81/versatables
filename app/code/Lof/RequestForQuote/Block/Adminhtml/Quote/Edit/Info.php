<?php
/**
 * Landofcoder
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Block\Adminhtml\Quote\Edit;

use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Info extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var null
     */
    protected $_rates = null;
    /**
     * Customer service
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $metadata;

    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $_metadataElementFactory;

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Lof\RequestForQuote\Helper\Data
     */
    protected $moduleHelper;

    /**
     * @var \Magento\Quote\Model\Quote\Address
     */
    protected $_quoteAddress;

    /**
     * @var null
     */
    protected $_quote_field_data = null;

    /**
     * @var null
     */
    protected $_quote_extra_field_data = null;

    /**
     * @var null
     */
    protected $_quote_address = null;

    /**
     * @var null
     */
    protected $_quote_billing_address = null;

    /**
     * @var null
     */
    protected $_quote_questions = null;

    /**
     * @var
     */
    protected $currenciesModel;

    /**
     * @var \Magento\Shipping\Model\Config
     */
    protected $shipconfig;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress
     */
    protected $quoteToOrderAddressConverter;

    /**
     * Info constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteToOrderAddressConverter
     * @param \Lof\RequestForQuote\Helper\Data $moduleHelper
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteToOrderAddressConverter,
        \Lof\RequestForQuote\Helper\Data $moduleHelper,
        \Magento\Shipping\Model\Config $shippingConfig,
        array $data = []
    )
    {
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        $this->quoteToOrderAddressConverter = $quoteToOrderAddressConverter;
        $this->moduleHelper = $moduleHelper;
        $this->shipconfig = $shippingConfig;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Retrieve required options from parent
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    protected function _beforeToHtml()
    {
        if (!$this->getParentBlock()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Please correct the parent block for this block.')
            );
        }
        $this->setOrder($this->getParentBlock()->getQuote());

        foreach ($this->getParentBlock()->getOrderInfoData() as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }


        parent::_beforeToHtml();
    }

    /**
     * @return mixed
     */
    public function getQuote()
    {
        return $this->getParentBlock()->getQuote();
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getMageQuote()
    {
        return $this->_coreRegistry->registry('mage_quote');
    }

    /**
     * @param null $store
     * @return mixed
     * @throws NoSuchEntityException
     */
    public function getCurrencySymbol($store = null)
    {
        $currencySymbol = "";
        if (!$store) {
            $storeId = $this->getMageQuote()->getStoreId();
            if ($storeId !== null) {
                $store = $this->_storeManager->getStore($storeId);
            }
        }
        if ($store) {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $currency = $_objectManager->create('Magento\Directory\Model\CurrencyFactory')->create()->load($store->getCurrentCurrencyCode());
            $currencySymbol = $currency->getCurrencySymbol();
        } else {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $rfqHelper = $_objectManager->create('Lof\RequestForQuote\Helper\Data')->create();
            $currencySymbol = $rfqHelper->getCurrentCurrencySymbol();
        }


        return $currencySymbol;
    }


    /**
     * @return null|string
     * @throws NoSuchEntityException
     */
    public function getOrderStoreName()
    {
        if ($this->getMageQuote()) {
            $storeId = $this->getMageQuote()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getMageQuote()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerGroupName()
    {
        if ($this->getMageQuote()) {
            $customerGroupId = $this->getMageQuote()->getCustomerGroupId();
            try {
                if ($customerGroupId !== null) {
                    return $this->groupRepository->getById($customerGroupId)->getCode();
                }
            } catch (NoSuchEntityException $e) {
                return '';
            }
        }

        return '';
    }

    /**
     * @return \Lof\RequestForQuote\Helper\Data
     */
    public function getModuleHelper()
    {
        return $this->moduleHelper;
    }

    /**
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if ($this->getMageQuote()->getCustomerIsGuest() || !$this->getMageQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $this->getMageQuote()->getCustomerId()]);
    }

    /**
     * Get order view URL.
     *
     * @param int $orderId
     * @return string
     */
    public function getViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', ['quote_id' => $orderId]);
    }

    /**
     * Find sort order for account data
     * Sort Order used as array key
     *
     * @param array $data
     * @param int $sortOrder
     * @return int
     */
    protected function _prepareAccountDataSortOrder(array $data, $sortOrder)
    {
        if (isset($data[$sortOrder])) {
            return $this->_prepareAccountDataSortOrder($data, $sortOrder + 1);
        }

        return $sortOrder;
    }


    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getCustomerAccountData()
    {
        $accountData = [];
        $entityType = 'customer';

        /* @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($this->metadata->getAllAttributesMetadata($entityType) as $attribute) {
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $orderKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $orderValue = $this->getMageQuote()->getData($orderKey);
            if ($orderValue != '') {
                $metadataElement = $this->_metadataElementFactory->create($attribute, $orderValue, $entityType);
                $value = $metadataElement->outputValue(AttributeDataFactory::OUTPUT_FORMAT_HTML);
                $sortOrder = $attribute->getSortOrder() + $attribute->isUserDefined() ? 200 : 0;
                $sortOrder = $this->_prepareAccountDataSortOrder($accountData, $sortOrder);
                $accountData[$sortOrder] = [
                    'label' => $attribute->getFrontendLabel(),
                    'value' => $this->escapeHtml($value, ['br']),
                ];
            }
        }
        ksort($accountData, SORT_NUMERIC);

        return $accountData;
    }

    /**
     * @return array
     */
    public function getShippingMethods()
    {

        $activeCarriers = $this->shipconfig->getActiveCarriers();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            $options = array();
            $default_price = 0;
            $carrierTitle = '';
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $options[] = array('value' => $code, 'label' => $method);

                }
                $carrierTitle = $this->_scopeConfig->getValue('carriers/' . $carrierCode . '/title');
                $carrierName = $this->_scopeConfig->getValue('carriers/' . $carrierCode . '/name');
                $default_price = $this->_scopeConfig->getValue('carriers/' . $carrierCode . '/price');

                $carrierTitle = $carrierTitle ? $carrierTitle : $carrierName;
            }
            $methods[] = array('value' => $options, 'label' => $carrierTitle, 'price' => '0', 'default_price' => (int)$default_price);
        }
        return $methods;

    }

    /**
     * Get link to edit order address page
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @param string $label
     * @return string
     */
    public function getAddressEditLink($address, $label = '')
    {
        if ($this->_authorization->isAllowed('Lof_RequestForQuote::quote_edit')) {
            if (empty($label)) {
                $label = __('Edit');
            }
            $url = $this->getUrl('sales/order/address', ['address_id' => $address->getId()]);
            return '<a href="' . $url . '">' . $label . '</a>';
        }

        return '';
    }

    /**
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getMageQuote()->getStoreId()
        );
    }

    /**
     * Check if is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Get object created at date affected with object store timezone
     *
     * @param mixed $store
     * @param string $createdAt
     * @return \DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get timezone for store
     *
     * @param mixed $store
     * @return string
     */
    public function getTimezoneForStore($store)
    {
        return $this->_localeDate->getConfigTimezone(
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store->getCode()
        );
    }

    /**
     * Get object created at date
     *
     * @param string $createdAt
     * @return \DateTime
     */
    public function getOrderAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }


    /**
     * @param $address
     * @return null|string
     * @throws \Exception
     */
    public function getFormattedAddress($address) {
        if ($address instanceof \Magento\Quote\Model\Quote\Address) {
            $address = $this->quoteToOrderAddressConverter->convert($address);
        }

        if (!$address instanceof \Magento\Sales\Model\Order\Address) {
            throw new \Exception(__('Expected instance of \Magento\Sales\Model\Order\Address, got ' . get_class($address)));
        }

        return $this->addressRenderer->format($address, 'html');
    }


    /**
     * @param $storeId
     * @return \Magento\Store\Api\Data\StoreInterface
     * @throws NoSuchEntityException
     */
    public function getStore($storeId)
    {
        return $this->_storeManager->getStore($storeId);
    }

    /**
     * @return string
     */
    public function getDateFormat()
    {
        return "yy-mm-dd";//$this->_localeDate->getDateFormat(\IntlDateFormatter::SHORT);
    }

    /**
     * @return array|null
     */
    public function getQuoteFieldData()
    {
        if (!$this->_quote_field_data) {
            $quote = $this->getQuote();
            $mage_quote = $this->getMageQuote();
            $addresses = $this->getMageQuoteAddress();
            $this->_quote_field_data = [];
            $this->_quote_field_data['first_name'] = $quote->getFirstName();
            $this->_quote_field_data['last_name'] = $quote->getLastName();
            $this->_quote_field_data['company'] = $quote->getCompany();
            $this->_quote_field_data['telephone'] = $quote->getTelephone();
            $this->_quote_field_data['address'] = $quote->getAddress();
            $this->_quote_field_data['email'] = $quote->getEmail();
            $this->_quote_field_data['tax_id'] = $quote->getTaxId();

            if (!$this->_quote_field_data['first_name']) {
                $this->_quote_field_data['first_name'] = $mage_quote->getData('customer_firstname');
            }
            if (!$this->_quote_field_data['last_name']) {
                $this->_quote_field_data['last_name'] = $mage_quote->getData('customer_lastname');
            }
            if (!$this->_quote_field_data['email']) {
                $this->_quote_field_data['email'] = $mage_quote->getData('customer_email');
            }
            if (!$this->_quote_field_data['telephone']) {
                $this->_quote_field_data['telephone'] = $addresses->getData('telephone');
            }
            if (!$this->_quote_field_data['company']) {
                $this->_quote_field_data['company'] = $addresses->getData('company');
            }

            $street = $addresses->getData("street");
            $region = $addresses->getData("region");
            $postcode = $addresses->getData("postcode");
            $country_id = $addresses->getData("country_id");

            if ($q_street = $mage_quote->getData('street')) {
                $street = $q_street;
            }
            if ($q_region = $mage_quote->getData('region')) {
                $region = $q_region;
            }
            if ($q_postcode = $mage_quote->getData('postcode')) {
                $postcode = $q_postcode;
            }
            if ($q_country_id = $mage_quote->getData('country_id')) {
                $country_id = $q_country_id;
            }

            if (!$this->_quote_field_data['address']) {

                $country_name = $this->moduleHelper->getCountryname($country_id);
                if (!$street) {
                    $street = $quote->getStreet();
                }
                if (!$region) {
                    $region = $quote->getRegion();
                }
                if (!$postcode) {
                    $postcode = $quote->getPostcode();
                }
                $this->_quote_field_data['street'] = $street;
                $this->_quote_field_data['region'] = $region;
                $this->_quote_field_data['postcode'] = $postcode;
                $this->_quote_field_data['country_id'] = $country_id;

                if ($street || $region || $postcode || $country_id) {
                    $quote_address = $street . ", " . $region . " " . $postcode . ", " . $country_name;
                    $this->_quote_field_data['address'] = $quote_address;
                }
            }
        }

        return $this->_quote_field_data;
    }

    /**
     * @return mixed|null
     */
    public function getMageQuoteAddress()
    {
        if (!$this->_quote_address) {
            $mage_quote = $this->getMageQuote();
            $addresses = $mage_quote->getAddressesCollection();
            foreach ($addresses as $address) {
                $address_type = $address->getAddressType();
                if ($address_type == "shipping") {
                    $this->_quote_address = $address;
                    break;
                }
            }
        }

        return $this->_quote_address;
    }

    /**
     * @return mixed|null
     */
    public function getMageQuoteBillingAddress()
    {
        if (!$this->_quote_billing_address) {
            $mage_quote = $this->getMageQuote();
            $addresses = $mage_quote->getAddressesCollection();
            foreach ($addresses as $address) {
                $address_type = $address->getAddressType();
                if ($address_type == "billing") {
                    $this->_quote_billing_address = $address;
                    break;
                }
            }
        }

        return $this->_quote_billing_address;
    }

    /**
     * @return array
     */
    public function getExtraFieldData()
    {
        $data = [];
        return $data;
    }

    /**
     * @param int $customer_id
     * @return string
     */
    public function getImportAddressUrl($customer_id = 0)
    {
        return $this->getUrl('quotation/quote/getCustomerAddress', ['customer_id' => (int)$customer_id]);
    }

    /**
     * @return array
     * @throws NoSuchEntityException
     */
    public function getAllCurrencies()
    {
        $codes = $this->_storeManager->getStore()->getAvailableCurrencyCodes();
        $currencies = [];
        if (is_array($codes) && count($codes) >= 1) {

            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            foreach ($codes as $code) {
                $allCurrencies = $_objectManager->create('Magento\Framework\Locale\Bundle\CurrencyBundle')->get(
                    $_objectManager->create('Magento\Framework\Locale\ResolverInterface')->getLocale()
                )['Currencies'];
                $currencies[$code] = [];
                $currencies[$code]['title'] = $allCurrencies[$code][1] ?: $code;
                $currencies[$code]['symbol'] = $_objectManager->create('Magento\Framework\Locale\CurrencyInterface')->getCurrency($code)->getSymbol();

            }
        }
        return $currencies;
    }

    /**
     * @param $date
     * @param $format
     * @return false|string
     */
    public function formatTheDate($date, $format)
    {
        $date_time = strtotime($date);
        return date($format, $date_time);
    }

    /**
     * @return array|mixed|null
     */
    public function getQuestions()
    {
        if (!$this->_quote_questions) {
            $quote = $this->getQuote();
            $question = $quote->getQuestion();
            if ($question) {
                $this->_quote_questions = unserialize($question);
            } else {
                $this->_quote_questions = [];
            }
        }
        return $this->_quote_questions;
    }

    /**
     * @param $html
     * @return null|string|string[]
     */
    public function stripScriptTags($html)
    {
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    }

}
