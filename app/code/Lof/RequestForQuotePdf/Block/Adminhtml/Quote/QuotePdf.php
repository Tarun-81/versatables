<?php
/**
 * Landofcoder
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Landofcoder.com license that is
 * available through the world-wide-web at this URL:
 * http://landofcoder.com/license
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category   Landofcoder
 * @package    Lof_RequestForQuote
 * @copyright  Copyright (c) 2017 Landofcoder (http://www.landofcoder.com/)
 * @license    http://www.landofcoder.com/LICENSE-1.0.html
 */

namespace Lof\RequestForQuotePdf\Block\Adminhtml\Quote;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;

class QuotePdf extends \Magento\Framework\View\Element\Template
{
    protected $_quoteAddress;

    protected $_quote_address_data = null;

    protected $_quote_extra_field_data = null;

    protected $_quote_shipping_address = null;

    protected $_quote_billing_address = null;

    protected $moduleHelper = null;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Directory\Block\Data $directoryData,
        CustomerRepository $customerRepository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Lof\RequestForQuote\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->_coreRegistry      = $coreRegistry;
        parent::__construct($context, $data);
        $this->_directoryData     = $directoryData;
        $this->customerRepository = $customerRepository;
        $this->urlHelper          = $urlHelper;
        $this->moduleHelper       = $moduleHelper;

    }
    public function getLogoPath($imageUrl) {
        $image_path = "pub/media/lof/requestforquote/".$imageUrl;
        return $image_path;
    }
     public function getCustomerAddress($address_type = "shipping") {
        if(!$this->_quote_address_data || ($this->_quote_address_data && !isset($this->_quote_address_data[$address_type]))) {
            $quote = $this->getQuote();
            $mage_quote = $this->getMageQuote();
            if($address_type == "shipping"){
                $addresses = $this->getMageQuoteShippingAddress();
            } else {
                $addresses = $this->getMageQuoteBillingAddress();
            }
            

            if(!$this->_quote_address_data)
                $this->_quote_address_data = [];

            $this->_quote_address_data[$address_type] = [];

            if($quote && $mage_quote){
                $this->_quote_address_data[$address_type]['first_name'] = $quote->getFirstName();
                $this->_quote_address_data[$address_type]['last_name'] = $quote->getLastName();
                $this->_quote_address_data[$address_type]['company'] = $quote->getCompany();
                $this->_quote_address_data[$address_type]['telephone'] = $quote->getTelephone();
                $this->_quote_address_data[$address_type]['address'] = '';//$quote->getAddress();
                $this->_quote_address_data[$address_type]['quote_address'] = $quote->getAddress();
                $this->_quote_address_data[$address_type]['email'] = $quote->getEmail();
                $this->_quote_address_data[$address_type]['tax_id'] = $quote->getTaxId();
                $this->_quote_address_data[$address_type]['middlename'] = $mage_quote->getData('customer_middlename');

                if(!$this->_quote_address_data[$address_type]['first_name']) {
                    $this->_quote_address_data[$address_type]['first_name'] = $mage_quote->getData('customer_firstname');
                }
                if(!$this->_quote_address_data[$address_type]['last_name']) {
                    $this->_quote_address_data[$address_type]['last_name'] = $mage_quote->getData('customer_lastname');
                }
                if(!$this->_quote_address_data[$address_type]['email']) {
                    $this->_quote_address_data[$address_type]['email'] = $mage_quote->getData('customer_email');
                }
                if(!$this->_quote_address_data[$address_type]['telephone']) {
                    $this->_quote_address_data[$address_type]['telephone'] = $addresses->getData('telephone');
                }
                if(!$this->_quote_address_data[$address_type]['company']) {
                    $this->_quote_address_data[$address_type]['company'] = $addresses->getData('company');
                }

                $street = $addresses->getData("street");
                $region = $addresses->getData("region");
                $postcode = $addresses->getData("postcode");
                $city = $addresses->getData("city");
                $country_id = $addresses->getData("country_id");

                if(!$street && ($q_street = $mage_quote->getData('street'))){
                    $street = $q_street;
                }
                if(!$region && ($q_region = $mage_quote->getData('region'))){
                    $region = $q_region;
                }
                if(!$postcode && ($q_postcode = $mage_quote->getData('postcode'))){
                    $postcode = $q_postcode;
                }
                if(!$country_id && ($q_country_id = $mage_quote->getData('country_id'))){
                    $country_id = $q_country_id;
                }
                if(!$city && ($q_city = $mage_quote->getData('city'))){
                    $city = $q_city;
                }

                if(!$this->_quote_address_data[$address_type]['address']) {
                    if(!$country_id){
                        $country_id = $quote->getCountryId();
                    }
                    $country_name = $this->moduleHelper->getCountryname($country_id);
                    if(!$street){
                        $street = $quote->getStreet();
                    }
                    if(!$region){
                        $region = $quote->getRegion();
                    }
                    if(!$postcode){
                        $postcode = $quote->getPostcode();
                    }
                    if(!$city){
                        $city = $quote->getCity();
                    }
                    
                    $this->_quote_address_data[$address_type]['street'] = $street;
                    $this->_quote_address_data[$address_type]['region'] = $region;
                    $this->_quote_address_data[$address_type]['postcode'] = $postcode;
                    $this->_quote_address_data[$address_type]['country_id'] = $country_id;
                    $this->_quote_address_data[$address_type]['city'] = $city;

                    if($street || $region || $postcode || $country_id || $city || $country_name){
                        $quote_address = "";
                        if($street){
                            $quote_address .= $street."<br/>";
                        }
                        if($city) {
                            $quote_address .= $city. ", ";
                        }
                        if($region) {
                            $quote_address .= $region. ", ";
                        }
                        if($postcode) {
                            $quote_address .= $postcode. "<br/>";
                        }
                        $quote_address .= $country_name;
                        $this->_quote_address_data[$address_type]['address'] = $quote_address;
                        $this->_quote_address_data[$address_type]['quote_address'] = $quote_address;
                    }
                }
            }
        }
        
        return $this->_quote_address_data[$address_type];
    }
    public function getMageQuoteShippingAddress() {
        if(!$this->_quote_shipping_address) {
            $mage_quote = $this->getMageQuote();
            $addresses = $mage_quote->getAddressesCollection();
            foreach($addresses as $address) {
                $address_type = $address->getAddressType();
                if($address_type == "shipping") {
                    $this->_quote_shipping_address = $address;
                    break;
                }
            }
        }

        return $this->_quote_shipping_address;
    }
    public function getMageQuoteBillingAddress() {
        if(!$this->_quote_billing_address) {
            $mage_quote = $this->getMageQuote();
            $addresses = $mage_quote->getAddressesCollection();
            foreach($addresses as $address) {
                $address_type = $address->getAddressType();
                if($address_type == "billing") {
                    $this->_quote_billing_address = $address;
                    break;
                }
            }
        }

        return $this->_quote_billing_address;
    }
}
