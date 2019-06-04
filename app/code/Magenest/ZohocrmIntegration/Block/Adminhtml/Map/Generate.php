<?php

namespace  Magenest\ZohocrmIntegration\Block\Adminhtml\Map;

use Magento\Backend\Block\Template;
use Magenest\ZohocrmIntegration\Model\Queue as QueueModel;
use Magento\Framework\App\ObjectManager;

/**
 * Class UpdateFields
 *
 * @package Magenest\ZohocrmIntegration\Block\Adminhtml\Map
 */
class Generate extends Template
{
    protected $_template = "Magenest_ZohocrmIntegration::map/generate.phtml";

    protected $queueFactory;
    protected $queueCollectionFactory;

    const TYPE_RECORD = [
        QueueModel::TYPE_ACCOUNT,
        QueueModel::TYPE_CAMPAIGN,
        QueueModel::TYPE_CONTACT,
        QueueModel::TYPE_LEAD,
        QueueModel::TYPE_ORDER,
        QueueModel::TYPE_PRODUCT,
        QueueModel::TYPE_SUBSCRIBER,
        QueueModel::TYPE_INVOICE
    ];

    public function __construct(
        \Magenest\ZohocrmIntegration\Model\QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory,
        Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->queueFactory = $queueFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
    }

    public function getTotalRecordType(){
        $arr = [];
        foreach (self::TYPE_RECORD as $type){
            $collection = $this->queueCollectionFactory->create();
            $typeSize = $collection->addFieldToFilter("type", $type)->getSize();
            if($typeSize>0){
                $arr[$type] = $typeSize;
            }
            $collection->clear();
        }
        return $arr;
    }
    public function getTotalRecord(){
        return $this->queueCollectionFactory->create()->getSize();
    }
}