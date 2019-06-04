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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Details\Account;

/**
 * Class Details
 * @package Cart2Quote\Quotation\Block\Quote\Details\Account
 */
class Details extends \Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Info
{

    /**
     * Customer Model
     *
     * @var \Magento\Customer\Model\Customer
     */
    private $customer;

    /**
     * Details constructor.
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param \Magento\Quote\Model\Quote\Address\ToOrderAddress $quoteAddressToOrderAddress
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $_orderCollectionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\Customer $customer,
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
        $this->customer = $customer;
        $this->groupRepository = $groupRepository;
        $this->metadata = $metadata;
        $this->_metadataElementFactory = $elementFactory;
        $this->addressRenderer = $addressRenderer;
        $this->_quoteAddressToOrderAddress = $quoteAddressToOrderAddress;
        $this->_orderCollectionFactory = $_orderCollectionFactory;
        parent::__construct(
            $context,
            $registry,
            $adminHelper,
            $groupRepository,
            $metadata,
            $elementFactory,
            $addressRenderer,
            $quoteAddressToOrderAddress,
            $_orderCollectionFactory,
            $data);
    }

    /**
     * Get customer name
     *
     * @return string
     */
    public function getCustomerName()
    {
        $customer = $this->getQuote()->getCustomer();

        if ($customer->getId()) {
            $customerName = $this->customer->updateData($customer)->getName();
        } else {
            $customerName = implode(' ', [
                $this->getQuote()->getCustomerFirstname(),
                $this->getQuote()->getCustomerMiddlename(),
                $this->getQuote()->getCustomerLastname(),
            ]);
        }

        return $this->escapeHtml($customerName);
    }
}