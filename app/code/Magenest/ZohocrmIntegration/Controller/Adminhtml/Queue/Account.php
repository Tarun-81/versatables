<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config;

/**
 * Class Account
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Account extends \Magento\Backend\App\Action
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configInterface;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_ACCOUNT;

    protected $dataHelper;

    /**
     * Customer constructor.
     * @param Context $context
     * @param CustomerFactory $customerFactory
     * @param Config $config
     * @param ScopeConfigInterface $configInterface
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        CustomerFactory $customerFactory,
        Config $config,
        ScopeConfigInterface $configInterface,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper
    ) {
        $this->queueFactory = $queueFactory;
        $this->_config = $config;
        $this->_configInterface = $configInterface;
        $this->customerFactory = $customerFactory;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($customers->getAllIds(), $this->type);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->delete(
            'magenest_zohocrm_queue',
            ['entity_id IN (?)' => $customers->getAllIds(), 'type = ? ' => $this->type, 'status = ?' => 'pending']);
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
        $this->messageManager->addSuccess(
            __('All Accounts have been added to queue, you can delete items you do not want to sync')
        );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->getUrl('*/*/index'));
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }
}
