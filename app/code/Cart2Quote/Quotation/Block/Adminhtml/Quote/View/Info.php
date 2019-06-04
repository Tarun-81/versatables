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

use Magento\Eav\Model\AttributeDataFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order\Address;

/**
 * Quote info block
 * Class Info
 */
class Info extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\AbstractQuote
{
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
     * @var Address\Renderer
     */
    protected $addressRenderer;

    /**
     * @var \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     */
    protected $_quoteAddressToOrderAddress;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     */
    protected $_orderCollectionFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param Address\Renderer $addressRenderer
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     * * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
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
        \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory,
        array $data = []
    ) {
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        $this->_quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->_orderCollectionFactory = $_orderCollectionFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get quote store name
     *
     * @return null|string
     */
    public function getQuoteStoreName()
    {
        if ($this->getQuote()) {
            $storeId = $this->getQuote()->getStoreId();
            if ($storeId === null) {
                $deleted = __(' [deleted]');
                return nl2br($this->getQuote()->getStoreName()) . $deleted;
            }
            $store = $this->_storeManager->getStore($storeId);
            $name = [$store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName()];
            return implode('<br/>', $name);
        }

        return null;
    }

    /**
     * Return name of the customer group.
     *
     * @return string
     */
    public function getCustomerGroupName()
    {
        if ($this->getQuote()) {
            $customerGroupId = $this->getQuote()->getCustomerGroupId();
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
     * Get URL to edit the customer.
     *
     * @return string
     */
    public function getCustomerViewUrl()
    {
        if ($this->getQuote()->getCustomerIsGuest() || !$this->getQuote()->getCustomerId()) {
            return '';
        }

        return $this->getUrl('customer/index/edit', ['id' => $this->getQuote()->getCustomerId()]);
    }

    /**
     * Get quote view URL.
     *
     * @param  int $quoteId
     * @return string
     */
    public function getViewUrl($quoteId)
    {
        return $this->getUrl('quotation/quote/view', ['quote_id' => $quoteId]);
    }

    /**
     * Return array of additional account data
     * Value is option style array
     *
     * @return array
     */
    public function getCustomerAccountData()
    {
        $accountData = [];
        $entityType = 'customer';

        /** @var \Magento\Customer\Api\Data\AttributeMetadataInterface $attribute */
        foreach ($this->metadata->getAllAttributesMetadata() as $attribute) {
            if (!$attribute->isVisible() || $attribute->isSystem()) {
                continue;
            }
            $quoteKey = sprintf('customer_%s', $attribute->getAttributeCode());
            $quoteValue = $this->getQuote()->getData($quoteKey);
            if ($quoteValue != '') {
                $metadataElement = $this->_metadataElementFactory->create($attribute, $quoteValue, $entityType);
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
     * Find sort quote for account data
     * Sort Order used as array key
     *
     * @param  array $data
     * @param  int $sortOrder
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
     * Whether Customer IP address should be displayed on sales documents
     *
     * @return bool
     */
    public function shouldDisplayCustomerIp()
    {
        return !$this->_scopeConfig->isSetFlag(
            'sales/general/hide_customer_ip',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getQuote()->getStoreId()
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
     * @param  mixed $store
     * @param  string $createdAt
     * @return \DateTime
     */
    public function getCreatedAtStoreDate($store, $createdAt)
    {
        return $this->_localeDate->scopeDate($store, $createdAt, true);
    }

    /**
     * Get object created at date
     *
     * @param  string $createdAt
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * Returns string with formatted address
     *
     * @param  \Magento\Quote\Model\Quote\Address $address
     * @return null|string
     */
    public function getFormattedAddress(\Magento\Quote\Model\Quote\Address $address)
    {
        //convert to Magento\Sales\Model\Order\Address so we can use a nice renderer
        $salesAddress = $this->_quoteAddressToOrderAddress->convert($address);
        return $this->addressRenderer->format($salesAddress, 'html');
    }

    /**
     * Get URL to set the default billing address
     * @return string
     */
    public function getSetDefaultBillingAddressHtml()
    {
        $defaultBillingAddressId = $this->getQuote()->getCustomer()->getDefaultBilling();
        $billingAddressId = $this->getQuote()->getBillingAddress()->getCustomerAddressId();
        if (($defaultBillingAddressId != null) && $defaultBillingAddressId != $billingAddressId) {
            $url = $this->getUrl('quotation/quote/changeAddress', [
                'quote_id' => $this->getQuote()->getId(),
                'address_type' => 'billing'
            ]);

            $link = '<a href="' . $url . '">' . __('Change to default billing address') . '</a>';
            return $link;
        }

        return '';
    }

    /**
     * Get URL to set the default shipping address
     * @return string
     */
    public function getSetDefaultShippingAddressHtml()
    {
        $defaultShippingAddressId = $this->getQuote()->getCustomer()->getDefaultShipping();
        $shippingAddressId = $this->getQuote()->getShippingAddress()->getCustomerAddressId();
        if (($defaultShippingAddressId != null) && $defaultShippingAddressId != $shippingAddressId) {
            $url = $this->getUrl('quotation/quote/changeAddress', [
                'quote_id' => $this->getQuote()->getId(),
                'address_type' => 'shipping'
            ]);

            $link = '<a href="' . $url . '">' . __('Change to default shipping address') . '</a>';
            return $link;
        }

        return '';
    }

    /**
     * Returns Order view url is based on quotation id
     *
     * @param  int $quoteId
     * @return array
     */
    public function getOrderViewUrlByQuotationId($quoteId)
    {
        //get order view url based on quotation id
        $orderIds = $this->_orderCollectionFactory->create()->addFieldToSelect(
            [
                'increment_id',
                'entity_id'
            ]
        )->addFieldToFilter(
            'quote_id',
            $quoteId
        )->setOrder(
            'increment_id',
            'asc'
        );

        $orderUrls = [];
        foreach ($orderIds as $val) {
            $orderUrls[$val->getIncrementId()] = $this->getUrl(
                'sales/order/view',
                [
                    'order_id' => $val->getEntityId()
                ]
            );
        }

        return $orderUrls;
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
        $this->setQuote($this->getParentBlock()->getQuote());

        foreach ($this->getParentBlock()->getQuoteInfoData() as $key => $value) {
            $this->setDataUsingMethod($key, $value);
        }

        parent::_beforeToHtml();
    }
}
