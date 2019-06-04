<?php
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue;

use Magenest\ZohocrmIntegration\Model\Queue;
use Magenest\ZohocrmIntegration\Model\QueueFactory;
use Magento\Backend\App\Action\Context;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Controller\ResultFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Sales\Model\Order\InvoiceFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Config\Model\ResourceModel\Config;
use Magento\CatalogRule\Model\RuleFactory;

/**
 * Class All
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Queue
 */
class All extends \Magento\Backend\App\Action
{
    /**
     * @var CustomerFactory
     */
    protected $customerFactory;

    /**
     * @var ProductFactory
     */
    protected $productFactory;

    /**
     * @var OrderFactory
     */
    protected $orderFactory;

    /**
     * @var QueueFactory
     */
    protected $queueFactory;

    /**
     * @var ScopeConfigInterface
     */
    protected $_configInterface;

    /**
     * @var Config
     */
    protected $_config;

    /**
     * @var \Magento\CatalogRule\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var InvoiceFactory
     */
    protected $invoiceFactory;

    protected $dataHelper;
    protected $_resource;

    /**
     * All constructor.
     * @param Context $context
     * @param OrderFactory $orderFactory
     * @param CustomerFactory $customerFactory
     * @param ProductFactory $productFactory
     * @param Config $config
     * @param ScopeConfigInterface $configInterface
     * @param RuleFactory $ruleFactory
     * @param InvoiceFactory $invoiceFactory
     * @param QueueFactory $queueFactory
     */
    public function __construct(
        Context $context,
        OrderFactory $orderFactory,
        CustomerFactory $customerFactory,
        ProductFactory $productFactory,
        Config $config,
        ScopeConfigInterface $configInterface,
        RuleFactory $ruleFactory,
        InvoiceFactory $invoiceFactory,
        QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->invoiceFactory = $invoiceFactory;
        $this->customerFactory = $customerFactory;
        $this->productFactory = $productFactory;
        $this->orderFactory = $orderFactory;
        $this->queueFactory = $queueFactory;
        $this->ruleFactory = $ruleFactory;
        $this->_config = $config;
        $this->_configInterface = $configInterface;
        parent::__construct($context);
        $this->dataHelper = $dataHelper;
        $this->_resource = $resource;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $connection = $this->_resource->getConnection();
        $connection->delete(
            'magenest_zohocrm_queue',
            ['status = ?' => 'pending']);

        $this->enqueueAccounts();

        $this->enqueueCampaigns();

        $this->enqueueContacts();

        $this->enqueueOrders();

        $this->enqueueProducts();

        $this->enqueueLeads();

        $this->enqueueInvoices();

        $this->messageManager->addSuccess(
            __('All Data have been added to queue, you can delete items you do not want to sync')
        );
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setUrl($this->getUrl('*/*/index'));
        return $resultRedirect;
    }

    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magenest_ZohocrmIntegration::config_zohocrm');
    }

    protected function enqueueAccounts()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($customers->getAllIds(), Queue::TYPE_ACCOUNT);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }

    protected function enqueueContacts()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($customers->getAllIds(), Queue::TYPE_CONTACT);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }

    protected function enqueueLeads()
    {
        $customers = $this->customerFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($customers->getAllIds(), Queue::TYPE_LEAD);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }

    protected function enqueueProducts()
    {
        $products = $this->productFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($products->getAllIds(), Queue::TYPE_PRODUCT);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }

    protected function enqueueOrders()
    {
        $orders = $this->orderFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($orders->getAllIds(), Queue::TYPE_ORDER);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }

    protected function enqueueCampaigns()
    {
        $rules = $this->ruleFactory->create()->getCollection();
        /** @var \Magento\CatalogRule\Model\Rule $rule */
        foreach ($rules as $rule) {
            $queue = $this->queueFactory->create();
            if (!$queue->queueExisted(Queue::TYPE_CAMPAIGN, $rule->getId())) {
                $queue->enqueue(Queue::TYPE_CAMPAIGN, $rule->getId());
            }
        }
    }

    protected function enqueueInvoices()
    {
        $invoices = $this->invoiceFactory->create()->getCollection();
        /** @var \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection $queueCollection */
        $dataInsert = $this->dataHelper->getInsertData($invoices->getAllIds(), Queue::TYPE_INVOICE);
        $queueCollection = $this->_objectManager->create("Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\Collection");
        $connection = $queueCollection->getResource()->getConnection();
        $connection->insertMultiple(
            $queueCollection->getResource()->getTable('magenest_zohocrm_queue'),
            $dataInsert
        );
    }
}
