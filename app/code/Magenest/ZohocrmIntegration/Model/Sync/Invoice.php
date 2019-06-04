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

namespace Magenest\ZohocrmIntegration\Model\Sync;

use Magento\Framework\App\ObjectManager;
use Magenest\ZohocrmIntegration\Model\Connector;
use Magenest\ZohocrmIntegration\Model\Queue;

/**
 * Class Invoice using to sync to Invoices table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Invoice extends Sync
{

    function getType()
    {
        return "Invoices";
    }

    public function getCollection()
    {
        $objectManager = ObjectManager::getInstance();
        $collections = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Invoice\Collection')
            ->addAttributeToSelect('*');
        return $collections;
    }

    /**
     * Get All Invoice
     *
     * @param $collections
     * @return string
     */
    public function getCollectionDataV2($collections)
    {
        $data = [];
        $number = 0;

        $productIds = [];
        $customerIds = [];
        $orderIds = [];
        foreach ($collections as $collection) {
            $billingAddress = $collection->getBillingAddress();
            $shippingAddress = $collection->getShippingAddress();
            $order = $collection->getOrder();
            if ($order) {
                $collection->setData("customer_email", $order->getData('customer_email'));
                if (!$collection->getOrder()->getData('customer_is_guest')) {
                    $collection->setData("customer_id", $order->getData('customer_id'));
                    $collection->setData("customer_firstname", $order->getData('customer_firstname'));
                    $collection->setData("customer_lastname", $order->getData('customer_lastname'));
                }
                if (!in_array($order->getId(), $orderIds)) {
                    $orderIds[] = $order->getId();
                }
            }
            if ($billingAddress) {
                $collection->setData("bill_firstname", $billingAddress->getData('firstname'));
                $collection->setData("bill_middlename", $billingAddress->getData('middlename'));
                $collection->setData("bill_lastname", $billingAddress->getData('lastname'));
                $collection->setData("bill_company", $billingAddress->getData('company'));
                $collection->setData("bill_street", $billingAddress->getData('street'));
                $collection->setData("bill_city", $billingAddress->getData('city'));
                $collection->setData("bill_region", $billingAddress->getData('region'));
                $collection->setData("bill_postcode", $billingAddress->getData('postcode'));
                $collection->setData("bill_country_id", $billingAddress->getData('country_id'));
            }
            if ($shippingAddress) {
                $collection->setData("ship_firstname", $shippingAddress->getData('firstname'));
                $collection->setData("ship_middlename", $shippingAddress->getData('middlename'));
                $collection->setData("ship_lastname", $shippingAddress->getData('lastname'));
                $collection->setData("ship_company", $shippingAddress->getData('company'));
                $collection->setData("ship_street", $shippingAddress->getData('street'));
                $collection->setData("ship_city", $shippingAddress->getData('city'));
                $collection->setData("ship_region", $shippingAddress->getData('region'));
                $collection->setData("ship_postcode", $shippingAddress->getData('postcode'));
                $collection->setData("ship_country_id", $shippingAddress->getData('country_id'));
            }
            foreach ($collection->getOrder()->getAllItems() as $it) {
                if (!in_array($it->getProductId(), $productIds)) {
                    $productIds[] = $it->getProductId();
                }
            }
            if ($collection->getData('customer_id')) {
                if (!in_array($collection->getData('customer_id'), $customerIds)) {
                    $customerIds[] = $collection->getData('customer_id');
                }
            }

        }
        //sync product lost
        if (sizeof($productIds) > 0) {
            $this->syncProductLost($productIds, $collections, 'Invoices');
        }
        $linkCol = $this->productLinkCollectionFactory->create()
            ->addFieldToFilter("entity_id", ['in' => $productIds])
            ->addFieldToFilter("type", "Products");

        $productLinkData = $linkCol->getData();
        $productLinkArr = [];
        if ((count($productLinkData) > 0) && (is_array($productLinkData))) {
            foreach ($productLinkData as $value) {
                $productLinkArr[$value['entity_id']] = $value['zoho_entity_id'];
            }
        }
        //sync contact lost
        if (sizeof($customerIds) > 0) {
            $this->syncContactLost($customerIds, $collections);
        }
        $linkCol = $this->productLinkCollectionFactory->create();
        $linkCol->addFieldToFilter("entity_id", ['in' => $customerIds])
            ->addFieldToFilter("type", "Contacts");
        $contactLinkData = $linkCol->getData();
        $contactLinkArr = [];
        if ((count($contactLinkData) > 0) && (is_array($contactLinkData))) {
            foreach ($contactLinkData as $value) {
                $contactLinkArr[$value['entity_id']] = $value['zoho_entity_id'];
            }
        }
        //sync SalesOrder lost
        if (sizeof($orderIds) > 0) {
            $this->syncSalesOrderLost($orderIds, $collections);
        }
        $linkCol = $this->productLinkCollectionFactory->create();
        $linkCol->addFieldToFilter("entity_id", ['in' => $orderIds])
            ->addFieldToFilter("type", Queue::TYPE_ORDER);
        $orderLinkData = $linkCol->getData();
        $orderLinkArr = [];
        if ((count($orderLinkData) > 0) && (is_array($orderLinkData))) {
            foreach ($orderLinkData as $value) {
                $orderLinkArr[$value['entity_id']] = $value['zoho_entity_id'];
            }
        }

        foreach ($collections as $collection) {
            if (is_array($this->mappingField)) {
                foreach ($this->mappingField as $field) {
                    if($field['zoho_field'] == 'Created Time' || $field['zoho_field'] == 'Modified Time'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = date(DATE_ATOM, strtotime($collection->getData($field['magento_field'])));
                        continue;
                    }
                    $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($collection->getData($field['magento_field']));

                }
            }
            $data[$number]['Subject'] = $collection->getData('increment_id');
            if (isset($contactLinkArr[$collection->getData('customer_id')])) {
                $data[$number]['Contact_Name']['id'] = $contactLinkArr[$collection->getData('customer_id')];
            }
            if ($collection->getOrder()->getData('customer_is_guest') || $collection->getData('customer_id') == null || $collection->getOrder()->getData('customer_firstname') == null) {
                $data[$number]['Account_Name'] = 'Guest, ' . $collection->getData('customer_email');
            } else {
                $data[$number]['Account_Name'] = $collection->getOrder()->getData('customer_firstname') . " " . $collection->getOrder()->getData('customer_lastname') . ", " . $collection->getCustomerId();
            }
            if (isset($orderLinkArr[$collection->getOrder()->getId()])) {
                $data[$number]['Sales_Order'] = $orderLinkArr[$collection->getOrder()->getId()];
            }
            $data[$number]['Invoice_Number'] = $collection->getData('entity_id');
            $data[$number]['Customer_No'] = $collection->getData('customer_id');
            $data[$number]['Status'] = $this->getStatusInvoice($collection->getData('state'));
            if (count($collection->getAllItems()) > 0) {
                $productVal = [];
                $countProd = 0;
                foreach ($collection->getAllItems() as $keyItem => $item) {
                    $productId = $item->getProductId();
                    if (isset($productLinkArr[$productId])) {
                        if($keyItem == 0) {
                            if($item->getOrderItem()->getParentItemId() && !intval($item->getPrice()))
                                continue;
                        }
                        $price = $item->getData('price');
                        $total = $item->getData('row_total');
                        $discount = $item->getData('discount_amount');
                        $tax = $item->getData('tax_amount');
                        $productVal[$countProd]['product']['id'] = $productLinkArr[$productId];
                        $productVal[$countProd]['product']['Product_Code'] = $item->getData('sku');
                        $productVal[$countProd]['product']['Product_Name'] = $item->getData('name');
                        $productVal[$countProd]['quantity'] = intval($item->getData('qty'));
                        $productVal[$countProd]['list_price'] = floatval($price);
                        $productVal[$countProd]['unit_price'] = floatval($price);
                        $productVal[$countProd]['total'] = floatval($total);
                        //$productVal[$countProd]['Discount'] = floatval($discount);
                        $total_after_discount = ($total - $discount);
                        $net_total = ($total_after_discount + floatval($tax));
                        $productVal[$countProd]['total_after_discount'] = $total_after_discount;
                        $productVal[$countProd]['net_total'] = $net_total;
//                        $productVal[$countProd]['line_tax'][0]['percentage'] = floatval($item->getTaxPercent());
//                        $productVal[$countProd]['line_tax'][0]['name'] = 'Vat';
//                        $productVal[$countProd]['line_tax'][0]['value'] = floatval($tax);
                        $productVal[$countProd]['Tax'] = floatval($tax);
                        $countProd++;
                    }
                }
                if ($countProd > 0) {
                    $data[$number]['Product_Details'] = $productVal;
                }
            }
            $data[$number]['Adjustment'] = floatval($collection->getData('shipping_amount'));
            $data[$number]['Grand_Total'] = floatval($collection->getData('grand_total'));
            $data[$number]['Discount'] = -floatval($collection->getData('discount_amount'));
            $subDis = floatval($collection->getSubtotal()+$collection->getDiscountAmount());
            if( $subDis) {
                $data[$number]['$line_tax'][0]['percentage'] = floatval($collection->getData('tax_amount') / $subDis * 100);
            } else {
                $data[$number]['$line_tax'][0]['percentage'] = 0;
            }            $data[$number]['$line_tax'][0]['name'] = 'Vat';
            $number++;

        }

        $params['data'] = $data;
        return $params;
    }


    /**
     * Sync Order lost
     *
     * @param $orderIds , $collections
     * @return
     */
    public function syncSalesOrderLost($orderIds, $collections)
    {
        if ((count($orderIds) > 0) && (is_array($orderIds))) {
            foreach ($orderIds as $value) {
                $orderNonLinkArr[]['order_id'] = $value;
            }
            $allOrderId = [];
            $productIds = [];
            $customerIds = [];
            $orderLinkArr = [];
            foreach ($collections as $collection) {
                if ((count($orderNonLinkArr) > 0) && (is_array($orderNonLinkArr))) {
                    foreach ($orderNonLinkArr as $key => $value) {
                        if ($collection->getOrder()->getId() == $value['order_id']) {
                            $orderLinkArr[$key]["order_id"] = $collection->getOrder()->getId();
                            $billingAddress = $collection->getBillingAddress();
                            $shippingAddress = $collection->getShippingAddress();
                            if ($billingAddress) {
                                $orderLinkArr[$key]["bill_firstname"] = $billingAddress->getData('firstname');
                                $orderLinkArr[$key]["bill_middlename"] = $billingAddress->getData('middlename');
                                $orderLinkArr[$key]["bill_lastname"] = $billingAddress->getData('lastname');
                                $orderLinkArr[$key]["bill_company"] = $billingAddress->getData('company');
                                $orderLinkArr[$key]["bill_street"] = $billingAddress->getData('street');
                                $orderLinkArr[$key]["bill_city"] = $billingAddress->getData('city');
                                $orderLinkArr[$key]["bill_region"] = $billingAddress->getData('region');
                                $orderLinkArr[$key]["bill_postcode"] = $billingAddress->getData('postcode');
                                $orderLinkArr[$key]["bill_country_id"] = $billingAddress->getData('country_id');
                                $orderLinkArr[$key]["bill_telephone"] = $billingAddress->getData('telephone');
                                $orderLinkArr[$key]["bill_fax"] = $billingAddress->getData('fax');
                            }
                            if ($shippingAddress) {
                                $orderLinkArr[$key]["ship_firstname"] = $shippingAddress->getData('firstname');
                                $orderLinkArr[$key]["ship_middlename"] = $shippingAddress->getData('middlename');
                                $orderLinkArr[$key]["ship_lastname"] = $shippingAddress->getData('lastname');
                                $orderLinkArr[$key]["ship_company"] = $shippingAddress->getData('company');
                                $orderLinkArr[$key]["ship_street"] = $shippingAddress->getData('street');
                                $orderLinkArr[$key]["ship_city"] = $shippingAddress->getData('city');
                                $orderLinkArr[$key]["ship_region"] = $shippingAddress->getData('region');
                                $orderLinkArr[$key]["ship_postcode"] = $shippingAddress->getData('postcode');
                                $orderLinkArr[$key]["ship_country_id"] = $shippingAddress->getData('country_id');
                                $orderLinkArr[$key]["ship_telephone"] = $shippingAddress->getData('telephone');
                                $orderLinkArr[$key]["ship_fax"] = $shippingAddress->getData('fax');
                            }
                            $orderLinkArr[$key]['so_number'] = $collection->getOrder()->getData('entity_id');
                            $orderLinkArr[$key]['increment_id'] = $collection->getOrder()->getData('increment_id');
                            $orderLinkArr[$key]['customer_id'] = $collection->getCustomerId();
                            if ($collection->getOrder()->getData('customer_is_guest') || $collection->getData('customer_id') == null || $collection->getOrder()->getData('customer_firstname') == null) {
                                $orderLinkArr[$key]['account_name'] = 'Guest, ' . $collection->getData('customer_email');
                            } else {
                                $orderLinkArr[$key]['account_name'] = $collection->getData('customer_firstname') . " " . $collection->getData('customer_lastname') . ", " . $collection->getData('customer_id');
                            }
                            $orderLinkArr[$key]['status'] = $collection->getData('status');
                            $orderLinkArr[$key]['tax_amount'] = $collection->getData('tax_amount');
                            $orderLinkArr[$key]['grand_total'] = $collection->getData('grand_total');
                            $orderLinkArr[$key]['sub_total'] = $collection->getData('subtotal');
                            $orderLinkArr[$key]['discount_amount'] = $collection->getData('discount_amount');
                            $orderLinkArr[$key]['created_at'] = $collection->getOrder()->getData('created_at');
                            $orderLinkArr[$key]['product_details'] = [];
                            $k = 0;
                            if (count($collection->getOrder()->getAllItems()) > 0) {
                                foreach ($collection->getOrder()->getAllItems() as $item) {
                                    $productId = $item->getProductId();
                                    $orderLinkArr[$key]['product_details'][$k]['product_id'] = $productId;
                                    $orderLinkArr[$key]['product_details'][$k]['price'] = $item->getData('price');
                                    $orderLinkArr[$key]['product_details'][$k]['row_total'] = $item->getData('row_total');
                                    $orderLinkArr[$key]['product_details'][$k]['discount_amount'] = $item->getData('discount_amount');
                                    $orderLinkArr[$key]['product_details'][$k]['tax_amount'] = $item->getData('tax_amount');
                                    $orderLinkArr[$key]['product_details'][$k]['qty_ordered'] = $item->getData('qty_ordered');
                                    $orderLinkArr[$key]['product_details'][$k]['tax_percent'] = $item->getData('tax_percent');
                                    if (!in_array($productId, $productIds)) {
                                        $productIds[] = $productId;
                                    }
                                    $k++;
                                }
                            }
                            $orderLinkArr[$key]['shipping_amount'] =  $collection->getData('shipping_amount');

                            if ($collection->getData('customer_id')) {
                                $customerIds[] = $collection->getData('customer_id');
                            }
                            if (!in_array($value['order_id'], $allOrderId)) {
                                $allOrderId[] = $value['order_id'];
                            }
                        }
                    }
                }
            }
            while (sizeof($orderLinkArr) > 0) {
                $records = array_slice($orderLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                array_splice($orderLinkArr, 0, Connector::MAX_RECORD_PER_CONNECT);
                $syncSaleOrders = $this->allOder($records, $productIds, $customerIds);
                $response = $this->connector->insertRecordsV2(Queue::TYPE_ORDER, $syncSaleOrders);
                //parse response data
                $this->dataHelper->processInsertEntityResponse($response, $allOrderId, Queue::TYPE_ORDER, true);
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
     * Get all record Order
     *
     * @param $collections
     * @return string
     */
    public function allOder($orderNonLinkArr, $productIds, $customerIds)
    {
        $data = [];
        $number = 0;
        $linkCol = $this->productLinkCollectionFactory->create()
            ->addFieldToFilter("entity_id", ['in' => $productIds])
            ->addFieldToFilter("type", "Products");

        $productLinkData = $linkCol->getData();
        $productLinkArr = [];
        if ((count($productLinkData) > 0) && (is_array($productLinkData))) {
            foreach ($productLinkData as $value) {
                $productLinkArr[$value['entity_id']] = $value['zoho_entity_id'];
            }
        }
        $linkCol = $this->productLinkCollectionFactory->create();
        $linkCol->addFieldToFilter("entity_id", ['in' => $customerIds])
            ->addFieldToFilter("type", "Contacts");
        $contactLinkData = $linkCol->getData();
        $contactLinkArr = [];

        if ((count($contactLinkData) > 0) && (is_array($contactLinkData))) {
            foreach ($contactLinkData as $value) {
                $contactLinkArr[$value['entity_id']] = $value['zoho_entity_id'];
            }
        }
        $mappingField = $this->mapFactory->create()
            ->getCollection()
            ->addFieldToFilter("type", Queue::TYPE_ORDER)
            ->addFieldToFilter("status", 1)
            ->addFieldToSelect(["zoho_field", "magento_field"])
            ->getData();
        foreach ($orderNonLinkArr as $value) {

            $data[$number]['SO_Number'] = $value['so_number'];
            $data[$number]['Subject'] = $value['increment_id'];
            if (isset($contactLinkArr[$value['customer_id']])) {
                $data[$number]['Contact_Name']['id'] = $contactLinkArr[$value['customer_id']];
            }
            $data[$number]['Account_Name'] = $value['account_name'];
            $data[$number]['Status'] = $value['status'];
            $data[$number]['Tax'] = $value['tax_amount'];
            $data[$number]['Grand Total'] = $value['grand_total'];
            $data[$number]['Sub_Total'] = $value['sub_total'];
            $data[$number]['Discount'] = -floatval($value['discount_amount']);
            if (is_array($mappingField)) {
                foreach ($mappingField as $field) {
                    if (isset($value[$field['magento_field']])) {
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($value[$field['magento_field']]);
                    }
                }
            }
            if (count($value['product_details']) > 0) {
                $productVal = "";
                $countProd = 0;
                foreach ($value['product_details'] as $product) {
                    if (isset($productLinkArr[$product['product_id']])) {
                        $price = $product['price'];
                        $total = $product['row_total'];
                        $discount = $product['discount_amount'];
                        $tax = $product['tax_amount'];
                        $productVal[$countProd]['product']['id'] = $productLinkArr[$product['product_id']];
                        $productVal[$countProd]['quantity'] = intval($product['qty_ordered']);
                        $productVal[$countProd]['list_price'] = floatval($price);
                        $productVal[$countProd]['unit_price'] = floatval($price);
                        $productVal[$countProd]['total'] = floatval($total);
                        //$productVal[$countProd]['Discount'] = floatval($discount);
                        $total_after_discount = ($total - $discount);
                        $net_total = ($total_after_discount + $tax);
                        $productVal[$countProd]['total_after_discount'] = $total_after_discount;
                        $productVal[$countProd]['net_total'] = $net_total;
                        $productVal[$countProd]['tax'] = floatval($tax);
//                        $productVal[$countProd]['line_tax'][0]['percentage'] = floatval($product['tax_percent']);
//                        $productVal[$countProd]['line_tax'][0]['name'] = 'Vat';
//                        $productVal[$countProd]['line_tax'][0]['value'] = floatval($tax);
                        $countProd++;
                    }
                }
                if ($countProd > 0) {
                    $data[$number]['Product_Details'] = $productVal;
                }
            }
            $data[$number]['Adjustment'] = floatval($value['shipping_amount']);
            $subDis = floatval($value['sub_total']+$value['discount_amount']);
            if( $subDis) {
                $data[$number]['$line_tax'][0]['percentage'] = floatval($value['tax_amount'] / $subDis * 100);
            } else {
                $data[$number]['$line_tax'][0]['percentage'] = 0;
            }
            $number++;
        }
        $params['data'] = $data;
        return $params;
    }
}