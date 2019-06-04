<?php
/**
 * Copyright © 2015 Magenest. All rights reserved.
 * See COPYING.txt for license details.
 *
 * Magenest_ZohocrmIntegration extension
 * NOTICE OF LICENSE
 *
 * @category Magenest
 * @package  Magenest_ZohocrmIntegration
 * @author   ThaoPV
 */
namespace Magenest\ZohocrmIntegration\Controller\Adminhtml\Map;

use Magenest\ZohocrmIntegration\Controller\Adminhtml\Map as MapController;
use Magenest\ZohocrmIntegration\Model\Queue;
use Magento\Framework\Controller\ResultFactory;
use Magenest\ZohocrmIntegration\Model\ResourceModel\Map\CollectionFactory as MapCollectionFactory;
use Magenest\ZohocrmIntegration\Model\MapFactory;
use Magento\Framework\Registry;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Backend\Model\View\Result\ForwardFactory;

/**
 * Class Edit
 *
 * @package Magenest\ZohocrmIntegration\Controller\Adminhtml\Map
 */
class Generate extends MapController
{
    /**
     * @var \Magento\Backend\Model\View\Result\Forward
     */
    protected $resultForwardFactory;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     * @param MapFactory $mapFactory
     * @param PageFactory $resultPageFactory
     * @param ForwardFactory $resultForwardFactory
     * @param MapCollectionFactory $collectionFactory
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry,
        MapFactory $mapFactory,
        PageFactory $resultPageFactory,
        ForwardFactory $resultForwardFactory,
        MapCollectionFactory $collectionFactory
    ) {
        $this->coreRegistry         = $coreRegistry;
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($context, $coreRegistry, $resultPageFactory, $mapFactory, $collectionFactory);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $this->generateAccountMapping();
        $this->generateContactMapping();
        $this->generateLeadMapping();
        $this->generateProductMapping();
        $this->generateOrderMapping();
        $this->generateInvoiceMapping();
        return $this->_redirect("zohocrm/map/index");
    }

    public function generateAccountMapping(){
        $datas=[
            [
                'zoho_field' => "Account Name",
                'magento_field' => "email",
            ],
            [
                'zoho_field' => "Account Number",
                'magento_field' => "entity_id",
            ],
            [
                'zoho_field' => "Account Site",
                'magento_field' => "created_in",
            ],
            [
                'zoho_field' => "Billing City",
                'magento_field' => "bill_city",
            ],
            [
                'zoho_field' => "Billing Code",
                'magento_field' => "bill_postcode",
            ],
            [
                'zoho_field' => "Billing Country",
                'magento_field' => "bill_country_id",
            ],
            [
                'zoho_field' => "Billing State",
                'magento_field' => "bill_region",
            ],
            [
                'zoho_field' => "Billing Street",
                'magento_field' => "bill_street",
            ],
            [
                'zoho_field' => "Shipping City",
                'magento_field' => "ship_city",
            ],
            [
                'zoho_field' => "Shipping Code",
                'magento_field' => "ship_postcode",
            ],
            [
                'zoho_field' => "Shipping Country",
                'magento_field' => "ship_country_id",
            ],
            [
                'zoho_field' => "Shipping State",
                'magento_field' => "ship_region",
            ],
            [
                'zoho_field' => "Shipping Street",
                'magento_field' => "ship_street",
            ],
            [
                'zoho_field' => "Phone",
                'magento_field' => "bill_telephone",
            ],
            [
                'zoho_field' => "Fax",
                'magento_field' => "bill_fax",
            ]
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", "Accounts");
            $model->setData("description", "Auto Generate");
            $model->save();
        }
        $this->messageManager->addSuccessMessage("Generate Account Mapping !");
    }

    public function generateContactMapping(){
        $datas=[
            [
                'zoho_field' => "First Name",
                'magento_field' => "firstname",
            ],
            [
                'zoho_field' => "Last Name",
                'magento_field' => "lastname",
            ],
            [
                'zoho_field' => "Email",
                'magento_field' => "email",
            ],
            [
                'zoho_field' => "Date of Birth",
                'magento_field' => "dob",
            ],
            [
                'zoho_field' => "Mailing City",
                'magento_field' => "bill_city",
            ],
            [
                'zoho_field' => "Mailing Code",
                'magento_field' => "bill_postcode",
            ],
            [
                'zoho_field' => "Mailing Country",
                'magento_field' => "bill_country_id",
            ],
            [
                'zoho_field' => "Mailing State",
                'magento_field' => "bill_region",
            ],
            [
                'zoho_field' => "Mailing Street",
                'magento_field' => "bill_street",
            ],
            [
                'zoho_field' => "Other City",
                'magento_field' => "ship_city",
            ],
            [
                'zoho_field' => "Other Code",
                'magento_field' => "ship_postcode",
            ],
            [
                'zoho_field' => "Other Country",
                'magento_field' => "ship_country_id",
            ],
            [
                'zoho_field' => "Other State",
                'magento_field' => "ship_region",
            ],
            [
                'zoho_field' => "Other Street",
                'magento_field' => "ship_street",
            ],
            [
                'zoho_field' => "Phone",
                'magento_field' => "bill_telephone",
            ],
            [
                'zoho_field' => "Fax",
                'magento_field' => "bill_fax",
            ]
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", "Contacts");
            $model->setData("description", "Auto Generate");
            $model->save();
        }

        $this->messageManager->addSuccessMessage("Generate Contact Mapping !");
    }

    public function generateLeadMapping(){
        $datas=[
            [
                'zoho_field' => "First Name",
                'magento_field' => "firstname",
            ],
            [
                'zoho_field' => "Last Name",
                'magento_field' => "lastname",
            ],
            [
                'zoho_field' => "Email",
                'magento_field' => "email",
            ],
            [
                'zoho_field' => "Company",
                'magento_field' => "email",
            ],
            [
                'zoho_field' => "City",
                'magento_field' => "bill_city",
            ],
            [
                'zoho_field' => "Zip Code",
                'magento_field' => "bill_postcode",
            ],
            [
                'zoho_field' => "Country",
                'magento_field' => "bill_country_id",
            ],
            [
                'zoho_field' => "State",
                'magento_field' => "bill_region",
            ],
            [
                'zoho_field' => "Street",
                'magento_field' => "bill_street",
            ],
            [
                'zoho_field' => "Phone",
                'magento_field' => "bill_telephone",
            ],
            [
                'zoho_field' => "Fax",
                'magento_field' => "bill_fax",
            ]
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", "Leads");
            $model->setData("description", "Auto Generate");
            $model->save();
        }

        $this->messageManager->addSuccessMessage("Generate Lead Mapping !");
    }

    public function generateProductMapping(){
        $datas=[
            [
                'zoho_field' => "Product Name",
                'magento_field' => "name",
            ],
            [
                'zoho_field' => "Product Code",
                'magento_field' => "sku",
            ],
            [
                'zoho_field' => "Product Active",
                'magento_field' => "status",
            ],
            [
                'zoho_field' => "Description",
                'magento_field' => "description",
            ],
            [
                'zoho_field' => "Unit Price",
                'magento_field' => "price",
            ],
            [
                'zoho_field' => "Qty in Stock",
                'magento_field' => "qty",
            ],
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", "Products");
            $model->setData("description", "Auto Generate");
            $model->save();
        }

        $this->messageManager->addSuccessMessage("Generate Product Mapping !");
    }

    public function generateOrderMapping(){
        $datas=[
            [
                'zoho_field' => "Subject",
                'magento_field' => "increment_id",
            ],
            [
                'zoho_field' => "Customer No",
                'magento_field' => "customer_id",
            ],
            [
                'zoho_field' => "Due Date",
                'magento_field' => "created_at",
            ],
            [
                'zoho_field' => "Billing City",
                'magento_field' => "bill_city",
            ],
            [
                'zoho_field' => "Billing Code",
                'magento_field' => "bill_postcode",
            ],
            [
                'zoho_field' => "Billing Country",
                'magento_field' => "bill_country_id",
            ],
            [
                'zoho_field' => "Billing State",
                'magento_field' => "bill_region",
            ],
            [
                'zoho_field' => "Billing Street",
                'magento_field' => "bill_street",
            ],
            [
                'zoho_field' => "Shipping City",
                'magento_field' => "ship_city",
            ],
            [
                'zoho_field' => "Shipping Code",
                'magento_field' => "ship_postcode",
            ],
            [
                'zoho_field' => "Shipping Country",
                'magento_field' => "ship_country_id",
            ],
            [
                'zoho_field' => "Shipping State",
                'magento_field' => "ship_region",
            ],
            [
                'zoho_field' => "Shipping Street",
                'magento_field' => "ship_street",
            ],
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", Queue::TYPE_ORDER);
            $model->setData("description", "Auto Generate");
            $model->save();
        }

        $this->messageManager->addSuccessMessage("Generate Order Mapping !");
    }

    public function generateInvoiceMapping(){
        $datas=[
            [
                'zoho_field' => "Subject",
                'magento_field' => "increment_id",
            ],
            [
                'zoho_field' => "Invoice Date",
                'magento_field' => "created_at",
            ],
            [
                'zoho_field' => "Billing City",
                'magento_field' => "bill_city",
            ],
            [
                'zoho_field' => "Billing Code",
                'magento_field' => "bill_postcode",
            ],
            [
                'zoho_field' => "Billing Country",
                'magento_field' => "bill_country_id",
            ],
            [
                'zoho_field' => "Billing State",
                'magento_field' => "bill_region",
            ],
            [
                'zoho_field' => "Billing Street",
                'magento_field' => "bill_street",
            ],
            [
                'zoho_field' => "Shipping City",
                'magento_field' => "ship_city",
            ],
            [
                'zoho_field' => "Shipping Code",
                'magento_field' => "ship_postcode",
            ],
            [
                'zoho_field' => "Shipping Country",
                'magento_field' => "ship_country_id",
            ],
            [
                'zoho_field' => "Shipping State",
                'magento_field' => "ship_region",
            ],
            [
                'zoho_field' => "Shipping Street",
                'magento_field' => "ship_street",
            ],
        ];

        foreach ($datas as $data){
            $model = $this->_mapFactory->create()->addData($data);
            $model->setData("status", 1);
            $model->setData("type", "Invoices");
            $model->setData("description", "Auto Generate");
            $model->save();
        }

        $this->messageManager->addSuccessMessage("Generate Invoice Mapping !");
    }
}