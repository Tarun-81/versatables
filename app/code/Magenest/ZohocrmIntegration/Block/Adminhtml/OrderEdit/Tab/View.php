<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magenest\ZohocrmIntegration\Block\Adminhtml\OrderEdit\Tab;

use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Queue;

/**
 * Order history tab
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class View extends \Magento\Backend\Block\Template implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     *
     * @var string
     */
    protected $_template = 'tab/view/zohocrm_order_info.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    protected $logFactory;

    protected $connector;

    /**
     * View constructor.
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
        $this->_coreRegistry = $registry;
        $this->connector = $connector;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_coreRegistry->registry('current_order');
    }
    /**
     * Retrieve order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrderId()
    {
        return $this->getOrder()->getEntityId();
    }

    /**
     * Retrieve order increment id
     *
     * @return string
     */
    public function getOrderIncrementId()
    {
        return $this->getOrder()->getIncrementId();
    }
    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Zoho CRM Integration');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Sync History');
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
     * Get customer creation date
     *
     * @return string
     */
    public function getCreatedAt()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_ORDER)
            ->addFieldToFilter('id_magento', $this->getOrderId())
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
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_ORDER)
            ->addFieldToFilter('id_magento', $this->getOrderId())
            ->getLastItem();
        return $log->getData('datetime') ? $this->formatDate($log->getData('datetime'), \IntlDateFormatter::MEDIUM, true) : 'Never';
    }

    /**
     * @return string
     */
    public function getZohocrmId()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_ORDER)
            ->addFieldToFilter('id_magento', $this->getOrderId());
        foreach ($log as $v) {
            if ($v->getData('record_id')) {
                return $v->getData('record_id');
            }
        }
        return '';
    }

    /**
     * @return string
     */

    public function getZohocrmUrl()
    {
        return $this->connector->getZohoRecordUrl().'SalesOrders/'.$this->getZohocrmId();

    }



    public function getSyncLog()
    {
        $log = $this->logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', Queue::TYPE_ORDER)
            ->addFieldToFilter('id_magento', $this->getOrderId())
            ->addOrder('datetime', 'DESC')
            ->setPageSize(10)
            ->setCurPage(1);
        return $log;
    }

    public function getSyncButtonUrl()
    {
        return $this->getUrl('zohocrm/queue/single/type/Sales_Orders/id/'.$this->getOrderId());
    }


}
