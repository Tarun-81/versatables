<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magenest\ZohocrmIntegration\Model\CronDaily;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Product
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class Product extends \Magento\Backend\App\Action
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
    protected $type = Queue::TYPE_PRODUCT;

    protected $dataHelper;



    /**
     * Order constructor.
     * @param Context $context
     * @param ProductFactory $productFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        ProductFactory $productFactory,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper
    ) {
        $this->queueFactory = $queueFactory;
        $this->productFactory = $productFactory;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {

        $products = $this->productFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($products->getAllIds(), $this->type);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->delete(
            'magenest_zohocrm_queue',
            ['entity_id IN (?)' => $products->getAllIds(), 'type = ? ' => $this->type, 'status = ?' => 'pending']);
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
        $this->messageManager->addSuccess(
            __('All Products have been added to queue, you can delete items you do not want to sync')
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
