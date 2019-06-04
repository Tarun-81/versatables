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
 * Class SalesOrder using to sync SalesOrder
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class SalesOrder extends Sync
{

    function getType()
    {
        return Queue::TYPE_ORDER;
    }

    public function getCollection()
    {
        $objectManager = ObjectManager::getInstance();
        $collections = $objectManager->create('Magento\Sales\Model\ResourceModel\Order\Collection')
            ->addAttributeToSelect('*');
        return $collections;
    }

    /**
     * Get all sales order
     *
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection[] $collections
     * @return mixed
     */
    public function getCollectionDataV2($collections)
    {
        $data = [];
        $number = 0;
        //get list product need to resync
        $productIds = [];
        $customerIds = [];
        foreach ($collections as $collection) {
            $billingAddress = $collection->getBillingAddress();
            $shippingAddress = $collection->getShippingAddress();
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
            foreach ($collection->getAllItems() as $it) {
                if (!in_array($it->getProductId(), $productIds)) {
                    $productIds[] = $it->getProductId();
                }
            }
            if ($collection->getData('customer_is_guest')) {
                $collection->setData("customer_id", null);
            }

            if ($collection->getData('customer_id')) {
                if (!in_array($collection->getData('customer_id'), $customerIds)) {
                    $customerIds[] = $collection->getData('customer_id');
                }
            }
        }
        //sync product lost
        if (sizeof($productIds) > 0) {
            $this->syncProductLost($productIds, $collections, 'SalesOrder');
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

        foreach ($collections as $collection) {
            if (is_array($this->mappingField)) {
               // $data[$number]['SO_Number'] = $collection->getData('entity_id');
                foreach ($this->mappingField as $field) {
                    if($field['zoho_field'] == 'Created Time' || $field['zoho_field'] == 'Modified Time'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = date(DATE_ATOM, strtotime($collection->getData($field['magento_field'])));
                        continue;
                    }
                    $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($collection->getData($field['magento_field']));

                }
                if (isset($contactLinkArr[$collection->getCustomerId()])) {
                    $data[$number]['Contact_Name']['id'] = $contactLinkArr[$collection->getCustomerId()];
                }
                if ($collection->getData('customer_is_guest') || $collection->getData('customer_id') == null || $collection->getData('customer_firstname') == null) {
                    $data[$number]['Account_Name'] = 'Guest, ' . $collection->getData('customer_email');
                } else {
                    $data[$number]['Account_Name'] = $collection->getData('customer_firstname') . " " . $collection->getData('customer_lastname') . ", " . $collection->getCustomerId();
                }
                $data[$number]['Status'] = $collection->getData('status');
                $data[$number]['Tax'] = $collection->getData('tax_amount');
                $data[$number]['Grand_Total'] = $collection->getData('grand_total');
                $data[$number]['Sub_Total'] = $collection->getData('subtotal');
                $data[$number]['Discount'] = -floatval($collection->getData('discount_amount'));
                $data[$number]['Subject'] = $collection->getData('increment_id');
            }
            if (count($collection->getAllItems()) > 0) {
                $productVal = [];
                $countProd = 0;
                foreach ($collection->getAllItems() as $item) {
                    $productId = $item->getProductId();
                    if (isset($productLinkArr[$productId])) {
                        $price = $item->getData('price');
                        $total = $item->getData('row_total');
                        $discount = $item->getData('discount_amount');
                        $tax = $item->getData('tax_amount');
                        $productVal[$countProd]['product']['id'] = $productLinkArr[$productId];
                        $productVal[$countProd]['product']['Product_Code'] = $item->getData('sku');
                        $productVal[$countProd]['product']['Product_Name'] = $item->getData('name');
                        $productVal[$countProd]['quantity'] = intval($item->getData('qty_ordered'));
                        $productVal[$countProd]['list_price'] = floatval($price);
                        $productVal[$countProd]['unit_price'] = floatval($price);
                        $productVal[$countProd]['total'] = floatval($total);
                        //$productVal[$countProd]['Discount'] = floatval($discount);
                        $total_after_discount = ($total - $discount);
                        $net_total = ($total_after_discount + floatval($tax) );
                        $productVal[$countProd]['total_after_discount'] = $total_after_discount;
                        $productVal[$countProd]['net_total'] = $net_total;
                        $productVal[$countProd]['tax'] = floatval($tax);
//                        $productVal[$countProd]['line_tax'][0]['percentage'] = floatval( $item->getData('tax_percent'));
//                        $productVal[$countProd]['line_tax'][0]['name'] = 'Vat';
//                        $productVal[$countProd]['line_tax'][0]['value'] = floatval($tax);
                        $countProd++;
                    }
                }
                if ($countProd > 0) {
                    $data[$number]['Product_Details'] = $productVal;
                }

            }
            $data[$number]['Adjustment'] = floatval($collection->getData('shipping_amount'));
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

}