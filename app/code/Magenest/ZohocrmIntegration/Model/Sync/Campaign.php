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
 * Class Campaign
 * Sync Catalog Rules Prices to Campaign
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Campaign extends Sync
{
    function getType()
    {
        return "Campaigns";
    }
    public function getCollection(){
        $objectManager = ObjectManager::getInstance();
        $collections = $objectManager->create('Magento\CatalogRule\Model\ResourceModel\Rule\Collection');
        return $collections;
    }
    /**
     * Get All Campaign
     *
     * @param $collections
     * @return string
     */
    public function getCollectionDataV2($collections)
    {
        $data = [];
        $number=0;
        foreach ($collections as $collection) {

            $name    = $collection->getName();
            if (is_array($this->mappingField)) {
                foreach ($this->mappingField as $field) {
                    $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($collection->getData($field['magento_field']));
                    if($field['zoho_field'] == 'Created Time' || $field['zoho_field'] == 'Modified Time'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = date(DATE_ATOM, strtotime($collection->getData($field['magento_field'])));
                        continue;
                    }
                }
            }
            $data[$number]['Campaign_Name'] = trim(str_replace('%', ' percent', $name));
            $number++;
        }
        $params['data'] = $data;
        return $params;
    }
}
