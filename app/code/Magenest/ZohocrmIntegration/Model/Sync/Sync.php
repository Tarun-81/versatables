<?php

namespace Magenest\ZohocrmIntegration\Model\Sync;

use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Queue;
use Magento\Framework\App\ObjectManager;
abstract class Sync
{
    protected $_queueFactory;

    protected $mappingField;

    protected $mapFactory;

    protected $connector;

    protected $dataHelper;

    protected $productLinkCollectionFactory;

    public function __construct(
        \Magenest\ZohocrmIntegration\Model\QueueFactory $queueFactory,
        \Magenest\ZohocrmIntegration\Model\MapFactory $mapFactory,
        \Magenest\ZohocrmIntegration\Model\Connector $connector,
        \Magenest\ZohocrmIntegration\Helper\Data $dataHelper,
        \Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\CollectionFactory $productLinkCollectionFactory,
        \Magento\CatalogRule\Model\Rule $rule,
        \Magenest\ZohocrmIntegration\Model\Data $data
    )
    {
        $this->_queueFactory = $queueFactory;
        $this->mapFactory = $mapFactory;
        $this->connector = $connector;
        $this->dataHelper = $dataHelper;
        $this->productLinkCollectionFactory = $productLinkCollectionFactory;
        $this->_rule = $rule;
        $this->_data = $data;
        $this->getMappingField();
    }

    public function getConnector()
    {
        return $this->connector;
    }

    public function getMappingField()
    {
        $this->mappingField = $this->mapFactory->create()
            ->getCollection()
            ->addFieldToFilter("type", $this->getType())
            ->addFieldToFilter("status", "1")
            ->addFieldToSelect(["zoho_field", "magento_field"])
            ->getData();
    }
    /**
     * Sync all data
     *
     * @return string
     */
    public function syncAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', $this->getType())
            ->addFieldToFilter('status', 'pending')
            ->addFieldToSelect("entity_id")
            ->addFieldToSelect("id");
        $allRecord = $collections->getData();
        $objectManager = ObjectManager::getInstance();
        $productLinkCollection = $objectManager->get("Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection");
        $connection = $productLinkCollection->getResource()->getConnection();
        $connection->delete(
            $productLinkCollection->getResource()->getTable('magenest_zohocrm_link_entity')
        );
        $collection = $this->getCollection();

        while (sizeof($allRecord) > 0) {
            $records = array_slice($allRecord, 0, Connector::MAX_RECORD_PER_CONNECT);
            array_splice($allRecord, 0, Connector::MAX_RECORD_PER_CONNECT);
            $entityIds = [];
            $ids = [];
            foreach ($records as $record) {
                $entityIds[] = $record['entity_id'];
                $ids[] = $record['id'];
            }

            /** @var \Magento\Eav\Model\Entity\Collection $collection */
            //perform get data
            if ($this->getType() == Queue::TYPE_CAMPAIGN) {
                $collection->addFieldToFilter("rule_id", ['in' => $entityIds]);
            } else {
                $collection->addFieldToFilter("entity_id", ['in' => $entityIds]);
            }
            $collection->load();
            $syncData = $this->getCollectionDataV2($collection);
            $response = $this->connector->insertRecordsV2($this->getType(), $syncData);
            $errorIds = $this->dataHelper->processInsertEntityResponse($response, $collection->getAllIds(), $this->getType());
            $collection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);

            //handle response insert api
            //if result sync success
            if (1) {

                $connection = $collection->getResource()->getConnection();
                $data = [
                    'status' => 'error',
                ];
                //update status queue data
                if (sizeof($errorIds) > 0) {
                    $connection->update(
                        $collection->getResource()->getTable('magenest_zohocrm_queue'),
                        $data,
                        ['entity_id IN (?)' => $errorIds, 'type = ? ' => $this->getType()]
                    );
                }
                //delete queue data
                $connection->delete(
                    $collection->getResource()->getTable('magenest_zohocrm_queue'),
                    ['id IN (?)' => $ids, 'type = ? ' => $this->getType(), 'status = ?' => 'pending']
                );
            } else {
                //if reach limit api
                break;
            }
        }
    }

    public function syncAjaxAllQueue()
    {
        $collections = $this->_queueFactory->create()
            ->getCollection()
            ->addFieldToFilter('type', $this->getType())
            ->addFieldToFilter('status', 'pending')
            ->addFieldToSelect("entity_id")
            ->addFieldToSelect("id");
        $allRecord = $collections->getData();
        $objectManager = ObjectManager::getInstance();
        $productLinkCollection = $objectManager->get("Magenest\ZohocrmIntegration\Model\ResourceModel\ProductLink\Collection");
        $connection = $productLinkCollection->getResource()->getConnection();
        $connection->delete(
            $productLinkCollection->getResource()->getTable('magenest_zohocrm_link_entity')
        );
        $collection = $this->getCollection();

        $records = array_slice($allRecord, 0, Connector::MAX_RECORD_PER_CONNECT);
        array_splice($allRecord, 0, Connector::MAX_RECORD_PER_CONNECT);
        $entityIds = [];
        $ids = [];
        foreach ($records as $record) {
            $entityIds[] = $record['entity_id'];
            $ids[] = $record['id'];
        }

        /** @var \Magento\Eav\Model\Entity\Collection $collection */
        //perform get data
        if ($this->getType() == Queue::TYPE_CAMPAIGN) {
            $collection->addFieldToFilter("rule_id", ['in' => $entityIds]);
        } else {
            $collection->addFieldToFilter("entity_id", ['in' => $entityIds]);
        }

        $collection->load();
        $syncData = $this->getCollectionDataV2($collection);
        $response = $this->connector->insertRecordsV2($this->getType(), $syncData);
        $errorIds = $this->dataHelper->processInsertEntityResponse($response, $collection->getAllIds(), $this->getType());
        $collection->clear()->getSelect()->reset(\Zend_Db_Select::WHERE);

        $connection = $collection->getResource()->getConnection();
        $data = [
            'status' => 'error',
        ];
        if (sizeof($errorIds) > 0) {
            $connection->update(
                $collection->getResource()->getTable('magenest_zohocrm_queue'),
                $data,
                ['entity_id IN (?)' => $errorIds, 'type = ? ' => $this->getType()]
            );
        }
        $connection->delete(
            $collection->getResource()->getTable('magenest_zohocrm_queue'),
            ['id IN (?)' => $ids, 'type = ? ' => $this->getType(), 'status = ?' => 'pending']
        );


        return sizeof($errorIds);
    }

    /**
     * Sync Contact lost
     *
     * @param array $customerIds
     * @param mixed $collections
     * @return
     */
    public function syncContactLost($customerIds, $collections)
    {
        if ((count($customerIds) > 0) && (is_array($customerIds))) {
            foreach ($customerIds as $value) {
                $contactNonLinkArr[]['customer_id'] = $value;
            }
            $contactLinkArr = [];
            $allContactId = [];
            foreach ($collections as $collection) {
                if ((count($contactNonLinkArr) > 0) && (is_array($contactNonLinkArr))) {
                    foreach ($contactNonLinkArr as $key => $value) {
                        if ($collection->getData('customer_id') == $value['customer_id']) {
                            $contactLinkArr[$key]['customer_id'] = $collection->getData('customer_id');
                            $contactLinkArr[$key]['firstname'] = $collection->getData('customer_firstname');
                            $contactLinkArr[$key]['lastname'] = $collection->getData('customer_lastname');
                            $contactLinkArr[$key]['email'] = $collection->getData('customer_email');

                            $billingAddress = $collection->getBillingAddress();
                            $shippingAddress = $collection->getShippingAddress();
                            if ($billingAddress) {
                                $contactLinkArr[$key]["bill_firstname"] = $billingAddress->getData('firstname');
                                $contactLinkArr[$key]["bill_middlename"] = $billingAddress->getData('middlename');
                                $contactLinkArr[$key]["bill_lastname"] = $billingAddress->getData('lastname');
                                $contactLinkArr[$key]["bill_company"] = $billingAddress->getData('company');
                                $contactLinkArr[$key]["bill_street"] = $billingAddress->getData('street');
                                $contactLinkArr[$key]["bill_city"] = $billingAddress->getData('city');
                                $contactLinkArr[$key]["bill_region"] = $billingAddress->getData('region');
                                $contactLinkArr[$key]["bill_postcode"] = $billingAddress->getData('postcode');
                                $contactLinkArr[$key]["bill_country_id"] = $billingAddress->getData('country_id');
                                $contactLinkArr[$key]["bill_telephone"] = $billingAddress->getData('telephone');
                                $contactLinkArr[$key]["bill_fax"] = $billingAddress->getData('fax');
                            }
                            if ($shippingAddress) {
                                $contactLinkArr[$key]["ship_firstname"] = $shippingAddress->getData('firstname');
                                $contactLinkArr[$key]["ship_middlename"] = $shippingAddress->getData('middlename');
                                $contactLinkArr[$key]["ship_lastname"] = $shippingAddress->getData('lastname');
                                $contactLinkArr[$key]["ship_company"] = $shippingAddress->getData('company');
                                $contactLinkArr[$key]["ship_street"] = $shippingAddress->getData('street');
                                $contactLinkArr[$key]["ship_city"] = $shippingAddress->getData('city');
                                $contactLinkArr[$key]["ship_region"] = $shippingAddress->getData('region');
                                $contactLinkArr[$key]["ship_postcode"] = $shippingAddress->getData('postcode');
                                $contactLinkArr[$key]["ship_country_id"] = $shippingAddress->getData('country_id');
                                $contactLinkArr[$key]["ship_telephone"] = $shippingAddress->getData('telephone');
                                $contactLinkArr[$key]["ship_fax"] = $shippingAddress->getData('fax');
                            }
                            if (!in_array($value['customer_id'], $allContactId)) {
                                $allContactId[] = $value['customer_id'];
                            }
                        };
                    }
                }
            }
            while (sizeof($contactLinkArr) > 0) {
                $records = array_slice($contactLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                array_splice($contactLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                $syncContacts = $this->allContact($records);
                $response = $this->connector->insertRecordsV2('Contacts', $syncContacts);
                //parse response data
                $this->dataHelper->processInsertEntityResponse($response, $allContactId, 'Contacts',true);
                if (1) {
                } else {
                    //if reach limit api
                    break;
                }
                //handle response insert api
                //if result sync success
            }
        } else {
            return;
        }
    }

    /**
     * Get all record Contact
     *
     * @param $collections
     * @return string
     */
    public function allContact($contactNonLinkArr)
    {
        $data = [];
        $number = 0;

        foreach ($contactNonLinkArr as $value) {
            $data[$number]['Last_Name'] = $value['lastname'];
            $data[$number]['First_Name'] = $value['firstname'];
            $data[$number]['Email'] = $value['email'];
            $data[$number]['Account_Name'] = $value['lastname'] . $value['firstname'] . ", " . $value['customer_id'];

            $mappingField = $this->mapFactory->create()
                ->getCollection()
                ->addFieldToFilter("type", "Contacts")
                ->addFieldToFilter("status", 1)
                ->addFieldToSelect(["zoho_field", "magento_field"])
                ->getData();
            if (is_array($mappingField)) {
                foreach ($mappingField as $field) {
                    if (isset($value[$field['magento_field']])) {
                        $params[$field['zoho_field']] = htmlspecialchars($value[$field['magento_field']]);
                    }
                }
            }
            $number++;
        }
        $params['data'] = $data;
        return $params;
    }

    /**
     * Sync product lost
     *
     * @param $productId
     * @return
     */
    public function syncProductLost($productIds, $collections, $type)
    {
        if ((count($productIds) > 0) && (is_array($productIds))) {
            foreach ($productIds as $value) {
                $productNonLinkArr[]['product_id'] = $value;
            }
            $productLinkArr =[];
            foreach ($collections as $collection) {
                $allItems = $type=='Invoices'?$collection->getOrder()->getAllItems():$collection->getAllItems();
                if (count($allItems) > 0) {
                    foreach ($allItems as $item) {
                        if ((count($productNonLinkArr) > 0) && (is_array($productNonLinkArr))) {
                            foreach ($productNonLinkArr as $key => $value) {
                                if ($item->getProductId() == $value['product_id']) {
                                    $productLinkArr[$key]['product_id'] = $item->getProductId();
                                    $productLinkArr[$key]['product_code'] = $item->getData('sku');
                                    $productLinkArr[$key]['product_name'] = $item->getData('name');
                                };
                            }
                        }
                    }
                }
            }
            while (sizeof($productLinkArr) > 0) {
                $records = array_slice($productLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                array_splice($productLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                $syncProducts = $this->allProduct($records);
                $response = $this->connector->insertRecordsV2('Products', $syncProducts);
                $allProductId = [];
                foreach ($records as $record){
                    $allProductId[] = $record['product_id'];
                }
                //parse response data
                $this->dataHelper->processInsertEntityResponse($response, $allProductId, 'Products',true);

                if (1) {
                } else {
                    //if reach limit api
                    break;
                }
                //handle response insert api
                //if result sync success
            }
        } else {
            return;
        }

    }
    /**
     * Get all record Product
     *
     * @param $collections
     * @return mixed
     */
    public function allProduct($productNonLinkArr)
    {
        $data = [];
        $number = 0;
        foreach ($productNonLinkArr as $value) {
            $data[$number]['Product_Code'] = $value['product_code'];
            $data[$number]['Product_Name'] = $value['product_name'] . ", " . $value['product_id'];
            $number++;

        }
        $params['data'] = $data;
        return $params;
    }
    public function getStatusInvoice($status)
    {
        switch ($status) {
            case 1 :
                return 'Pending';
                break;
            case 2:
                return 'Paid';
                break;
            case 3:
                return 'Canceled';
                break;

        }
    }

}