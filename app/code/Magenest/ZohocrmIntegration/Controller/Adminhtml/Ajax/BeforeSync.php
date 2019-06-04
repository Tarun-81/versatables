<?php

namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Ajax;


use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magenest\ZohocrmIntegration\Model\Connector;

/**
 * Class Account
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class BeforeSync extends Action
{
    protected $queueFactory;
    protected $queueCollectionFactory;
    protected $resultJsonFactory;

    /**
     * Customer constructor.
     * @param Context $context
     */
    public function __construct(
        \Magenest\ZohocrmIntegration\Model\QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory,
        Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
    )
    {

        $this->queueFactory = $queueFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $type = $this->getRequest()->getParam('type');
        if ($type == 'All'){
            $totalRecord = $this->getTotalRecord();
        }
        else {
            $totalRecord = $this->getTotalRecordType($type);
        }
        if ( $totalRecord / Connector::MAX_RECORD_PER_CONNECT == 0) {
            $totalRequest = $totalRecord / Connector::MAX_RECORD_PER_CONNECT;
        } else {
            $totalRequest = $totalRecord / Connector::MAX_RECORD_PER_CONNECT + 1;
        }
        $result = $this->resultJsonFactory->create();
        $result->setData(
            [
                'total_record' => $totalRecord,
                'total_request' => $totalRequest,
            ]
        );
        return $result;
    }

    public function getTotalRecordType($type)
    {
        $collection = $this->queueCollectionFactory->create();
        $typeSize = $collection->addFieldToFilter("type", $type)
            ->addFieldToFilter("status", 'pending')->getSize();
        $collection->clear();
        return $typeSize;

    }
    public function getTotalRecord(){
        return $this->queueCollectionFactory->create()
            ->addFieldToFilter("status", 'pending')->getSize();
    }

}
