<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * https://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (https://www.landofcoder.com/)
 * @license    https://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuote\Helper;

use Magento\Customer\Api\Data\CustomerInterfaceFactory;
use Magento\Customer\Api\AccountManagementInterface;

class Customer extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_currency;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;
    /**
     * @var Rate
     */
    protected $rate;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    protected $_priceCurrency;

    protected $_enable_quote = null;

    protected $customerSession = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        CustomerInterfaceFactory $customerFactory,
        AccountManagementInterface $accountManagement
        ) {
        parent::__construct($context);
        $this->customerSession        = $customerSession;
        $this->_storeManager          = $storeManager;
        $this->_localeDate            = $localeDate;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->date                   = $date;
        $this->customerRepository   = $customerRepository;
        $this->_objectManager  = $objectManager;
        $this->customerFactory   = $customerFactory;
        $this->accountManagement = $accountManagement;
    }

    public function createPassword($quote_id)
    {
        $password = base64_encode($quote_id.rand().time());
        return $password;
    }

    /**
     * @param string $email
     * @return bool|\Magento\Customer\Model\Customer
     */
    public function getCustomerByEmail($email, $websiteId = null)
    {
        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $this->_objectManager->create(
            'Magento\Customer\Model\Customer'
        );
        if (!$websiteId) {
            $customer->setWebsiteId($this->_storeManager->getWebsite()->getId());
        } else {
            $customer->setWebsiteId($websiteId);
        }
        $customer->loadByEmail($email);

        if ($customer->getId()) {
            return $customer;
        }

        return false;
    }

    /**
     * @param $data
     * @param $website_id
     * @param $store_id
     * @return mixed
     */
    public function createCustomerMultiWebsite($data, $website_id, $store_id)
    {
        $customer = $this->customerFactory->create();
        $customer->setFirstname($data['first_name'])
        ->setLastname($data['last_name'])
        ->setEmail($data['email'])
        ->setWebsiteId($website_id)
        ->setStoreId($store_id);

        $customer = $this->accountManagement->createAccount($customer, $data['password']);

        return $customer;
    }

}