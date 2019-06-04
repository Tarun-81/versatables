<?php

namespace BetterDeals\ZopimChat\Helper;

class Chat extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_USE_CUSTOMER_DATA = 'zopim/customer_data/enabled';
    const XML_PATH_USE_CUSTOMER_DATA_NAME = 'zopim/customer_data/name';
    const XML_PATH_USE_CUSTOMER_DATA_EMAIL = 'zopim/customer_data/email';
    const XML_PATH_USE_CUSTOMER_DATA_TELEPHONE = 'zopim/customer_data/telephone';
    const XML_PATH_USE_CUSTOMER_DATA_ORDERS = 'zopim/customer_data/orders';

    /**
     * Store manager
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Initialize dependencies.
     *
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    public function useCustomerData()
    {
        return $this->isSetFlag(self::XML_PATH_USE_CUSTOMER_DATA);
    }

    public function useCustomerName()
    {
        return $this->isSetFlag(self::XML_PATH_USE_CUSTOMER_DATA_NAME);
    }

    public function useCustomerEmail()
    {
        return $this->isSetFlag(self::XML_PATH_USE_CUSTOMER_DATA_EMAIL);
    }

    public function useCustomerTelephone()
    {
        return $this->isSetFlag(self::XML_PATH_USE_CUSTOMER_DATA_TELEPHONE);
    }

    public function useCustomerOrders()
    {
        return $this->isSetFlag(self::XML_PATH_USE_CUSTOMER_DATA_ORDERS);
    }

    public function getConfig($path)
    {
        return $this->scopeConfig->getValue(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

    public function isSetFlag($path)
    {
        return $this->scopeConfig->isSetFlag(
            $path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->storeManager->getStore()
        );
    }

}
