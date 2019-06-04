<?php

namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\MassQueue;


use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;

class Account extends \Magento\Backend\App\Action
{

    /**
     * @var Filter
     */
    protected $filter;

    /**
     * @var CollectionFactory
     */
    protected $collectionFactory;


    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    protected $type = Queue::TYPE_ACCOUNT;

    protected $dataHelper;

    /**
     * MassDelete constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper

    )
    {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->queueFactory = $queueFactory;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    public function execute()
    {
        try {
            $customers = $this->filter->getCollection($this->collectionFactory->create());
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
            $this->messageManager->addSuccess(__('A total of %1 record(s) have been added to queue', count($dataInsert)));
        } catch (\Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::queue');
    }
}
