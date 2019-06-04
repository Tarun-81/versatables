<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\CatalogRule\Model\RuleFactory;

/**
 * Class Campaign
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Campaign extends \Magento\Backend\App\Action
{
    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_CAMPAIGN;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $_ruleFactory;

    protected $dataHelper;

    /**
     * Order constructor.
     * @param Context $context
     * @param RuleFactory $ruleFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        RuleFactory $ruleFactory,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper
    ) {
        $this->queueFactory = $queueFactory;
        $this->_ruleFactory = $ruleFactory;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $rules = $this->_ruleFactory->create()->getCollection();
        $dataInsert = $this->dataHelper->getInsertData($rules->getAllIds(), $this->type);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->delete(
            'magenest_zohocrm_queue',
            ['entity_id IN (?)' => $rules->getAllIds(), 'type = ? ' => $this->type, 'status = ?' => 'pending']);
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $this->messageManager->addSuccess(
            __('All Campaigns have been added to queue, you can delete items you do not want to sync')
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
