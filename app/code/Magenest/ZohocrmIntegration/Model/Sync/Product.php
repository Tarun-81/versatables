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

/**
 * Class Product using to sync to Products table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Product extends Sync
{
    function getType()
    {
        return "Products";
    }

    public function getCollection(){
        $objectManager = ObjectManager::getInstance();
        $collections = $objectManager->create('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToSelect('*')
            ->joinField(
                'qty', 'cataloginventory_stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left'
            );
        return $collections;
    }
    /**
     * Get all record Product
     *
     * @param $collections
     * @return string
     */
    public function getCollectionDataV2($collections)
    {
        $data = [];
        $number=0;
        foreach ($collections as $collection) {
            if(is_array($this->mappingField)){
                foreach ($this->mappingField as $field){
                    if($field['zoho_field'] == 'Product Active'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = ($collection->getData($field['magento_field'])==1)?True:False;
                        continue;
                    }
                    if($field['zoho_field'] == 'Quantity in Stock'){
                        $data[$number]['Qty_in_Stock'] = htmlspecialchars(intval($collection->getData($field['magento_field'])));
                        continue;
                    }
                    if($field['zoho_field'] == 'Unit Price'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars(intval($collection->getData($field['magento_field'])));
                        continue;
                    }
                    if($field['zoho_field'] == 'Created Time' || $field['zoho_field'] == 'Modified Time'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = date(DATE_ATOM, strtotime($collection->getData($field['magento_field'])));
                        continue;
                    }
                    $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($collection->getData($field['magento_field']));
                }
            }
            $data[$number]['Product_Code'] = $collection->getData('sku');
            $data[$number]['Product_Active'] = ($collection->getData('status')==1)?True:False;
            $data[$number]['Product_Name'] = $collection->getData('name').", ".$collection->getData('entity_id');

            $number++;
        }
        $params['data'] = $data;
        return $params;
    }
}