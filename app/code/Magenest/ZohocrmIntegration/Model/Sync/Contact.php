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
 * Class Contact using to sync to Contacts table
 *
 * @package Magenest\ZohocrmIntegration\Model\Sync
 */
class Contact extends Sync
{

    function getType()
    {
        return "Contacts";
    }

    public function getCollection()
    {
        $objectManager = ObjectManager::getInstance();
        $collections = $objectManager->create('Magento\Customer\Model\ResourceModel\Customer\Collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('bill_street', 'customer_address/street', 'default_billing', null, 'left')
            ->joinAttribute('bill_postcode', 'customer_address/postcode', 'default_billing', null, 'left')
            ->joinAttribute('bill_city', 'customer_address/city', 'default_billing', null, 'left')
            ->joinAttribute('bill_telephone', 'customer_address/telephone', 'default_billing', null, 'left')
            ->joinAttribute('bill_fax', 'customer_address/fax', 'default_billing', null, 'left')
            ->joinAttribute('bill_region', 'customer_address/region', 'default_billing', null, 'left')
            ->joinAttribute('bill_country_id', 'customer_address/country_id', 'default_billing', null, 'left')
            ->joinAttribute('bill_firstname', 'customer_address/firstname', 'default_billing', null, 'left')
            ->joinAttribute('bill_middlename', 'customer_address/middlename', 'default_billing', null, 'left')
            ->joinAttribute('bill_lastname', 'customer_address/lastname', 'default_billing', null, 'left')
            ->joinAttribute('bill_company', 'customer_address/company', 'default_billing', null, 'left')
            ->joinAttribute('ship_street', 'customer_address/street', 'default_shipping', null, 'left')
            ->joinAttribute('ship_postcode', 'customer_address/postcode', 'default_shipping', null, 'left')
            ->joinAttribute('ship_city', 'customer_address/city', 'default_shipping', null, 'left')
            ->joinAttribute('ship_telephone', 'customer_address/telephone', 'default_shipping', null, 'left')
            ->joinAttribute('ship_fax', 'customer_address/fax', 'default_shipping', null, 'left')
            ->joinAttribute('ship_region', 'customer_address/region', 'default_shipping', null, 'left')
            ->joinAttribute('ship_country_id', 'customer_address/country_id', 'default_shipping', null, 'left')
            ->joinAttribute('ship_firstname', 'customer_address/firstname', 'default_shipping', null, 'left')
            ->joinAttribute('ship_middlename', 'customer_address/middlename', 'default_shipping', null, 'left')
            ->joinAttribute('ship_lastname', 'customer_address/lastname', 'default_shipping', null, 'left')
            ->joinAttribute('ship_company', 'customer_address/company', 'default_shipping', null, 'left')
            ->joinAttribute('taxvat', 'customer/taxvat', 'entity_id', null, 'left');
        return $collections;
    }

    /**
     * Get All Record Contact
     *
     * @param $collections
     * @return string
     */
    public function getCollectionDataV2($collections)
    {
        $data = [];
        $number = 0;
        foreach ($collections as $customer) {
            if (is_array($this->mappingField)) {
                foreach ($this->mappingField as $field) {
                    if($field['zoho_field'] == 'Created Time' || $field['zoho_field'] == 'Modified Time'){
                        $data[$number][str_replace(' ', '_', $field['zoho_field'])] = date(DATE_ATOM, strtotime($customer->getData($field['magento_field'])));
                        continue;
                    }
                    $data[$number][str_replace(' ', '_', $field['zoho_field'])] = htmlspecialchars($customer->getData($field['magento_field']));
                }
            }
            $data[$number]['Last_Name'] = $customer->getData('lastname');
            $data[$number]['First_Name'] = $customer->getData('firstname');
            $data[$number]['Email'] = $customer->getData('email');
            $data[$number]['Account_Name'] = $customer->getData('firstname') .' '. $customer->getData('lastname') . ", " . $customer->getData('entity_id');

            $number++;
        }
        $params['data'] = $data;
        return $params;
    }
}