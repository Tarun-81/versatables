<?php

namespace Magenest\ZohocrmIntegration\Block\Adminhtml\CustomerEdit\Tab\View;

use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Customer\Controller\RegistryConstants;

/**
 * Class ZohocrmCustomerInfo
 * @package Magenest\Zohocrm\Block\Adminhtml\Edit\Tab\View
 */
class ZohocrmCustomerInfo extends \Magento\Backend\Block\Template
{
    /**
     * Customer
     *
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customer;

    /**
     * Customer registry
     *
     * @var \Magento\Customer\Model\CustomerRegistry
     */
    protected $customerRegistry;

    /**
     * Customer data factory
     *
     * @var \Magento\Customer\Api\Data\CustomerInterfaceFactory
     */
    protected $customerDataFactory;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Data object helper
     *
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\ReportFactory
     */
    protected $logFactory;

    protected $connector;

    /**
     * ZohocrmCustomerInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magenest\ZohocrmIntegration\Model\ReportFactory $logFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magenest\ZohocrmIntegration\Model\ReportFactory $logFactory,
        Connector $connector,
        array $data = []
    )
    {
        $this->logFactory = $logFactory;
        $this->coreRegistry = $registry;
        $this->customerDataFactory = $customerDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->connector = $connector;
        parent::__construct($context, $data);
    }

    /**
     * Set customer registry
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @return void
     * @deprecated
     */
    public function setCustomerRegistry(\Magento\Customer\Model\CustomerRegistry $customerRegistry)
    {

        $this->customerRegistry = $customerRegistry;
    }

    /**
     * Get customer registry
     *
     * @return \Magento\Customer\Model\CustomerRegistry
     * @deprecated
     */
    public function getCustomerRegistry()
    {

        if (!($this->customerRegistry instanceof \Magento\Customer\Model\CustomerRegistry)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get('Magento\Customer\Model\CustomerRegistry');
        } else {
            return $this->customerRegistry;
        }
    }

    /**
     * Retrieve customer object
     *
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function getCustomer()
    {
        if (!$this->customer) {
            $this->customer = $this->customerDataFactory->create();
            $this->dataObjectHelper->populateWithArray(
                $this->customer,
                $this->_backendSession->getCustomerData()['account'],
                '\Magento\Customer\Api\Data\CustomerInterface'
            );
        }
        return $this->customer;
    }

    /**
     * Retrieve customer id
     *
     * @return string|null
     */
    public function getCustomerId()
    {
        return $this->coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @return null|string
     */
    public function getCreatedAt()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', ['IN' => ['Contacts', 'Leads', 'Accounts']])
            ->addFieldToFilter('id_magento', $this->getCustomerId())
            ->getFirstItem();
        return $log->getData('datetime') ? $this->formatDate($log->getData('datetime'), \IntlDateFormatter::MEDIUM, true) : 'Never';
    }

    /**
     * @return null|string
     */
    public function getLastUpdatedAt()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', ['IN' => ['Contacts', 'Leads', 'Accounts']])
            ->addFieldToFilter('id_magento', $this->getCustomerId())
            ->getLastItem();
        return $log->getData('datetime') ? $this->formatDate($log->getData('datetime'), \IntlDateFormatter::MEDIUM, true) : 'Never';
    }

    /**
     * @param $type
     * @return string
     */
    public function getZohocrmId($type)
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', $type)
            ->addFieldToFilter('id_magento', $this->getCustomerId())
            ->addFieldToFilter('status', 1)
            ->addOrder('datetime', 'DESC')
            ->getFirstItem();
        return $log->getRecordId();
    }

    public function getZohocrmUrl($type)
    {
        return $this->connector->getZohoRecordUrl() . '/' . $type . '/' . $this->getZohocrmId($type);
    }

    public function getSyncLog()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', ['IN' => ['Contacts', 'Leads', 'Accounts']])
            ->addFieldToFilter('id_magento', $this->getCustomerId())
            ->addOrder('datetime', 'DESC')
            ->setPageSize(10)
            ->setCurPage(1);

        return $log;
    }

    public function getSyncButtonUrl($type)
    {
        return $this->getUrl('zohocrm/queue/single/type/' . $type.'/id/'.$this->getCustomerId());
    }
}
