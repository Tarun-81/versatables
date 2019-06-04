<?php
/**
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Quotation\Controller\Quote\Ajax;

use Zoho\CRM\ZohoClient,
	Zoho\CRM\Entities\Lead,
	Zoho\CRM\Exception\ZohoCRMException
	;
	
/**
 * Class UpdateQuote
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class UpdateQuote extends \Cart2Quote\Quotation\Controller\Quote\Ajax\AjaxAbstract
{
    /**
     * Update customer's quote
     *
     * @return void
     */
    public function processAction()
    {
        $this->updateFields();
        $this->updateQuotationProductData();
    }

    /**
     * Update the quotation fields
     *
     * @return void
     */
    private function updateFields()
    {
		
		$quote = $this->quoteSession->getQuote();
		$id = $quote->getId();
		$this->saveQuoteToCRM($quote);
		exit(0);
        $this->_eventManager->dispatch(
            'quotation_controller_before_update_quote',
            [
                'quote' => $this->quoteSession->getQuote(),
                'action' => $this
            ]
        );

        $this->quoteSession->addGuestFieldData(json_decode(
            $this->getRequest()->getParam(\Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA, []), true
        ));

        $this->quoteSession->addConfigData(json_decode(
            $this->getRequest()->getParam(\Cart2Quote\Quotation\Model\Session::QUOTATION_STORE_CONFIG_DATA, []), true
        ));

        $this->quoteSession->addFieldData(json_decode(
            $this->getRequest()->getParam(\Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA, []), true
        ));

        $this->quoteSession->addProductData(json_decode(
            $this->getRequest()->getParam(\Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA, []), true
        ));

        $this->_eventManager->dispatch(
            'quotation_controller_after_update_quote',
            [
                'quote' => $this->quoteSession->getQuote(),
                'action' => $this
            ]
        );
    }
	
	public function saveQuoteToCRM($quote){      
		//$items = $quote->getItems();
		$items = $quote->getAllItems();
		$billingAddress = $quote->getBillingAddress();
		$address = $billingAddress->getData();
		//print_r($address);
		$i =1;
		$xmlProd = "";
		$total = 0;
		foreach($items as $item){
			$sku =  $item->getSku();
			$product = $this->_objectManager->create('\Magento\Catalog\Api\ProductRepositoryInterface');
			$model = $this->_objectManager->create('\Magento\Catalog\Model\Product');
			$_product = $product->get($sku);
			echo $id = $_product->getId();
			$logFactory = $this->_objectManager->create("\Magenest\ZohocrmIntegration\Model\ReportFactory");
			$log = $logFactory->create()->getCollection()
            ->addFieldToFilter('zohocrm_table', "products")
            ->addFieldToFilter('id_magento', $id)
            ->addFieldToFilter('status', 1)
            ->addOrder('datetime', 'DESC')
            ->getFirstItem();
			echo $zohocrmId = $log->getRecordId();die;
			$xmlProd .= '<product no="'.$i++.'">
						<FL val="Product Id">'.$zohocrmId.'</FL>
						<FL val="Unit Price">'.$_product->getId().'</FL>
						<FL val="Quantity">1.0</FL>
						<FL val="Total">'.$_product->getPrice().'</FL>
						<FL val="Discount"></FL>
						<FL val="Total After Discount"></FL>
						<FL val="List Price">'.$_product->getPrice().'</FL>
						<FL val="Net Total">'.$_product->getPrice().'</FL>
					</product>';
			$total += $_product->getPrice();
		}
		echo $total.'----';
		$ZohoClient = new ZohoClient('59ebf7771bd28dc242b6b5ab71aa1aa5'); // Make the connection to zoho api
		$ZohoClient->setModule('Quotes'); // Set the module
		echo $name = $address['firstname'].' '.$address['lastname'];
		$customer_note = $_POST['customer_note'];
		$company = $address['company'];
		$telephone = $address['telephone'];
		$country_id = $address['country_id'];
		echo '---'.$email = $address['email'];
		$validXML = '<Quotes>
						<row no="1">
							<FL val="Subject">'.$name.' - Quote</FL>
							<FL val="Account Name">'.$email.'</FL>
							<FL val="Name">'.$name.'</FL>
							<FL val="Mobile">'.$telephone.'</FL>
							<FL val="Email">'.$email.'</FL>
							<FL val="Created At">'.date('m-d-Y H:i:s').'</FL>
							<FL val="Due Date"></FL>
							<FL val="Sub Total">'.$total.'</FL>
							<FL val="Tax"></FL>
							<FL val="Adjustment"></FL>
							<FL val="Grand Total">'.$total.'</FL>
							<FL val="Billing Street">'.$address['street'].'</FL>
							<FL val="Shipping Street"></FL>
							<FL val="Billing City">'.$address['city'].'</FL>
							<FL val="Shipping City"></FL>
							<FL val="Billing State">'.$address['region'].'</FL>
							<FL val="Shipping State"></FL>
							<FL val="Billing Code"></FL>
							<FL val="Shipping Code"></FL>
							<FL val="Billing Country">'.$country_id.'</FL>
							<FL val="Shipping Country">'.$country_id.'</FL>
							<FL val="Product Details">
								'.$xmlProd.'
							</FL>
							<FL val="Terms and Conditions"></FL>
							<FL val="Description">'.$_POST['customer_note'].'</FL>
						</row>
					</Quotes>';
			try{
				$response = $ZohoClient->insertRecords($validXML);
				print_r($response);die('sdfsdfsd');
			}catch(exception $ex){
				echo $ex->getMessage();die;
			}
	}

}
