<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class Order
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class Order extends \Magento\Backend\App\Action
{
    /**
     * @var
     */
    protected $orderFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var string
     */
    protected $type = Queue::TYPE_ORDER;

    /**
     * @var int
     */
    protected $orderToInvoiceFlag;

    protected $dataHelper;

    /**
     * Order constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param ScopeConfigInterface $scopeConfigInterface
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        ScopeConfigInterface $scopeConfigInterface,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper
    ) {
        $this->queueFactory = $queueFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $orders = $this->orderFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($orders->getAllIds(), $this->type);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->delete(
            'magenest_zohocrm_queue',
            ['entity_id IN (?)' => $orders->getAllIds(), 'type = ? ' => $this->type, 'status = ?' => 'pending']);
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
        $this->messageManager->addSuccess(
            __('All Orders have been added to queue, you can delete items you do not want to sync')
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
