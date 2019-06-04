<?php

namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Ajax;

use Magenest\ZohocrmIntegration\Model\Sync;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Queue;

/**
 * Class Account
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Sync
 */
class SyncAjax extends Action
{

    protected $_contact;


    protected $_campaign;


    protected $_account;


    protected $_lead;


    protected $_order;

    protected $_product;

    protected $_invoice;

    protected $queueFactory;
    protected $queueCollectionFactory;
    protected $resultJsonFactory;
    protected $connector;
    protected $mappingField;
    protected $mapFactory;
    protected $dataHelper;


    /**
     * Customer constructor.
     * @param Context $context
     * @param Sync\Account $account
     */
    public function __construct(
        \Magenest\ZohocrmIntegration\Model\QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Model\ResourceModel\Queue\CollectionFactory $queueCollectionFactory,
        Context $context,
        Sync\Contact $contact,
        Sync\Campaign $campaign,
        Sync\Account $account,
        Sync\Lead $lead,
        Sync\SalesOrder $order,
        Sync\Product $product,
        Sync\Invoice $invoice,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        Connector $connector,
        \Magenest\ZohocrmIntegration\Model\MapFactory $mapFactory,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper
    )
    {
        $this->_contact = $contact;
        $this->_campaign = $campaign;
        $this->_account = $account;
        $this->_lead = $lead;
        $this->_order = $order;
        $this->_product = $product;
        $this->_invoice = $invoice;
        $this->queueFactory = $queueFactory;
        $this->queueCollectionFactory = $queueCollectionFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->connector = $connector;
        $this->mapFactory = $mapFactory;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $numberErrorIds = 0;
        $type = $this->getRequest()->getParam('type');
        switch ($type) {
            case Queue::TYPE_ACCOUNT :
                $numberErrorIds = $this->_account->syncAjaxAllQueue();
                break;
            case Queue::TYPE_CAMPAIGN :
                $numberErrorIds = $this->_campaign->syncAjaxAllQueue();
                break;
            case Queue::TYPE_CONTACT :
                $numberErrorIds = $this->_contact->syncAjaxAllQueue();
                break;
            case Queue::TYPE_LEAD :
                $numberErrorIds = $this->_lead->syncAjaxAllQueue();
                break;
            case Queue::TYPE_ORDER :
                $numberErrorIds = $this->_order->syncAjaxAllQueue();
                break;
            case Queue::TYPE_PRODUCT :
                $numberErrorIds = $this->_product->syncAjaxAllQueue();
                break;
            case Queue::TYPE_INVOICE :
                $numberErrorIds = $this->_invoice->syncAjaxAllQueue();
                break;
            case 'All':

                $numberErrorIds = $this->_account->syncAjaxAllQueue();

                $numberErrorIds += $this->_lead->syncAjaxAllQueue();

                $numberErrorIds += $this->_contact->syncAjaxAllQueue();

                $numberErrorIds += $this->_campaign->syncAjaxAllQueue();

                $numberErrorIds += $this->_product->syncAjaxAllQueue();

                $numberErrorIds += $this->_order->syncAjaxAllQueue();

                $numberErrorIds += $this->_invoice->syncAjaxAllQueue();
                break;
        }

        $result = $this->resultJsonFactory->create();
        $result->setData(
            [
                'record_success' => Connector::MAX_RECORD_PER_CONNECT,
            ]
        );
        return $result;

    }


}
