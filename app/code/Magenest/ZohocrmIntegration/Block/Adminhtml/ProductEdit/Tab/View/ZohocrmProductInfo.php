<?php

namespace Magenest\ZohocrmIntegration\Block\Adminhtml\ProductEdit\Tab\View;

use Magenest\ZohocrmIntegration\Model\Connector;
use Magento\Customer\Model\Customer;
use Magenest\ZohocrmIntegration\Model\Queue;

/**
 * Class ZohocrmCustomerInfo
 * @package Magenest\Zohocrm\Block\Adminhtml\Edit\Tab\View
 */
class ZohocrmProductInfo extends \Magento\Backend\Block\Template
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var \Magenest\ZohocrmIntegration\Model\ReportFactory
     */
    protected $logFactory;

    protected $connector;

    /**
     * ZohocrmItemInfo constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magenest\ZohocrmIntegration\Model\ReportFactory $logFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magenest\ZohocrmIntegration\Model\ReportFactory $logFactory,
        Connector $connector,
        array $data = []
    ) {
        $this->logFactory = $logFactory;
        $this->coreRegistry = $registry;
        $this->connector = $connector;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->coreRegistry->registry('current_product');
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    /**
     * @return int
     */
    public function getSku()
    {
        return $this->getProduct()->getSku();
    }

    /**
     * Get customer creation date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $log = $this->logFactory->create()->getCollection()
                    ->addFieldToFilter('zohocrm_table', Queue::TYPE_PRODUCT)
                    ->addFieldToFilter('id_magento', $this->getProductId())
                    ->getFirstItem();
        return $log->getData('datetime') ? $this->formatDate($log->getData('datetime'), \IntlDateFormatter::MEDIUM, true) : 'Never';
    }

    /**
     * Get customer creation date
     *
     * @return string
     */
    public function getLastUpdatedAt()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_PRODUCT)
            ->addFieldToFilter('id_magento', $this->getProductId())
            ->getLastItem();
        return $log->getData('datetime') ? $this->formatDate($log->getData('datetime'), \IntlDateFormatter::MEDIUM, true) : 'Never';
    }

    /**
     * @return string
     */
    public function getZohocrmId($type)
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', $type)
            ->addFieldToFilter('id_magento', $this->getProductId())
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
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_PRODUCT)
            ->addFieldToFilter('id_magento', $this->getProductId())
            ->addOrder('datetime', 'DESC')
            ->setPageSize(10)
            ->setCurPage(1);
        return $log;
    }

    public function getSyncButtonUrl()
    {
        return $this->getUrl('zohocrm/queue/single/type/Products/id/'.$this->getProductId());
    }
}
