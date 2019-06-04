<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
use Magento\Framework\App\Bootstrap;
 
/**
 * If your external file is in root folder
 */
require __DIR__ . '/app/bootstrap.php';
 
$reuestID = $_REQUEST['SalesOrderID'];
//$reuestID = "3093418000002346001";
$authtokken ="03d95794d824e8c2c65a2a2b6cfedc65";
$url = "https://crm.zoho.com/crm/private/json/SalesOrders/getRecordById?&authtoken=".$authtokken."&scope=crmapi&id=".$reuestID;
$salesorderresult = salesordercurl($url);
$dataArray = array();
$productarray = array();
if(isset($salesorderresult['response']))
{
	$flarraydatas = $salesorderresult['response']['result']['SalesOrders']['row']['FL'];
	  foreach($flarraydatas as $flarraydata)
	  {
		  if($flarraydata['val'] == "Product Details")
		  {
			  $counter = 0;
			  if(isset($flarraydata['product']))
			  {  
				 $totalpdct = count($flarraydata['product']);
				  $i=0;
				  //echo "cooo---".$i;
				  if(isset($flarraydata['product'][$i]))
				  {
				   for ($i = 0;$i<$totalpdct;$i++)
				  {
				  $pdetails = $flarraydata['product'][$i]['FL'];
				  foreach($pdetails as $pdetail)
				  {
				     $dataArray[$flarraydata['val']][$i][$pdetail['val']] = $pdetail['content'];
				  }
				 }
				  }else
				  {
					$pdetails = $flarraydata['product']['FL'];  
					foreach($pdetails as $pdetail)
				  {
				     $dataArray[$flarraydata['val']][$i][$pdetail['val']] = $pdetail['content'];
				  }
				  }
			  }
		  }
		  else
		  {
		    $dataArray[$flarraydata['val']] = $flarraydata['content'];
		  }
	  }
}
if(!isset($dataArray['Email']))
{
	echo "<script>alert('Email cannot be empty');</script>";
	die();
}
if(!isset($dataArray['Phone']))
{
	echo "<script>alert('Telephone cannot be empty');</script>";
	die();
}

$products = $dataArray['Product Details'];

$productHtml = '';
$p= array();
$j=0;
$options=array();

foreach($products as $key =>$value)
{
	$Product_Id = $value['Product Id'];
	//echo $Product_Id;
	
	$url = "https://crm.zoho.com/crm/private/json/Products/getRecordById?&authtoken=".$authtokken."&scope=crmapi&id=".$Product_Id;
	$productresult = salesordercurl($url);
	$productCode = '';
	if(isset($productresult['response']))
	{
		$flarraydatas = $productresult['response']['result']['Products']['row']['FL'];
		//print_r($flarraydatas);
	   foreach($flarraydatas as $flarraydata)
		  {   
		      if($flarraydata['val']=="Color" || $flarraydata['val']=="Control Switch" || $flarraydata['val']== "Depth" || $flarraydata['val']=="Frame Color" || $flarraydata['val']== "Surface Color" || $flarraydata['val']=="Width" || $flarraydata['val']=="Select Users")
			  {
				  $options[$j][$flarraydata['val']]=$flarraydata['content'];
			  }
			  if($flarraydata['val'] == "Parent SKU")				  
			  {  
               	$p[]=$flarraydata['content'];
				$productCode  = $flarraydata['content'];
			  }
		  } 
		  $j++;
	}
}

	//billing information
	if(isset($dataArray['Billing First Name']))
	{
	$bfname = $dataArray['Billing First Name'];
	}
	else
	{
	$bfname = $dataArray['Shipping First Name'];
	}
	if(isset($dataArray['Billing Last Name']))
	{
	$blname = $dataArray['Billing Last Name'];
	}
	else
	{
	$blname = $dataArray['Shipping Last Name'];	
	}
	$bfullname = $bfname." ".$blname;
	if(isset($dataArray['Billing_State']))
	{
	$billstate =  $dataArray['Billing_State'];	
	}
	else
	{
	$billstate =  $dataArray['Shipping_State'];	
	}
	if(isset($dataArray['Billing Company Name']))
	{
	$billCompany =  $dataArray['Billing Company Name'];	
	}
	else
	{
	$billCompany =  $dataArray['Shipping Company Name'];	
	}
	if(isset($dataArray['Billing City']))
	{
	$billcity =  $dataArray['Billing City'];	
	}
	else
	{
	$billcity =  $dataArray['Shipping City'];	
	}
	if(isset($dataArray['Billing Street']))
	{
	  $billStreet =  $dataArray['Billing Street'];
	}
	else
	{
	 $billStreet =  $dataArray['Shipping Street'];	
	}
	if(isset($dataArray['Billing_Country']))
	{
	  $billCountry =  $dataArray['Billing_Country'];
	}
	else
	{
		$billCountry =  $dataArray['Shipping_Country'];
	}
	if($dataArray['Billing Code'])
	{
	  $billCode =  $dataArray['Billing Code'];
	}
	if(isset($dataArray['Source']))
	{
	 $billSource  =  $dataArray['Source'];
	}
	//shipping information
	if(isset($dataArray['Shipping City']))
	{
		$shipcity =  $dataArray['Shipping City'];
	}
	else
	{
		$shipcity =  $dataArray['Billing City'];
	}
	if(isset($dataArray['Shipping_State']))
	{
		$shipingstate =  $dataArray['Shipping_State'];
	}
	else
	{
		$shipingstate =  $dataArray['Billing_State'];
	}
	if(isset($dataArray['Shipping_Country']))
	{
	$shipcntry =  $dataArray['Shipping_Country'];	
	}
	else
	{
	$shipcntry =  $dataArray['Billing_Country'];	
	}
	if(isset($dataArray['Shipping First Name']))
	{
	   $shipf =  $dataArray['Shipping First Name'];	
	}
	else
	{
		$shipf =  $dataArray['Billing First Name'];
	}
	if(isset($dataArray['Shipping Last Name']))
	{
	   $shipL =  $dataArray['Shipping Last Name'];	
	}
	else
	{
		$shipL =  $dataArray['Billing Last Name'];
	}
	$shipfullname = $shipf." ".$shipL;
	if(isset($dataArray['Shipping Company Name']))
	{
		$shipCompany =  $dataArray['Shipping Company Name'];	
	}
	else
	{
		$shipCompany =  $dataArray['Billing Company Name'];
	}
	if(isset($dataArray['Shipping Street']))
	{
		$shipstreet =  $dataArray['Shipping Street'];	
	}
	else
	{
		$shipstreet =  $dataArray['Billing Street'];
	}
	if(isset($dataArray['Shipping Code']))
	{
		$shipCode =  $dataArray['Shipping Code'];	
	}
	else
	{
		$shipCode =  $dataArray['Billing Code'];
	}
	if(isset($dataArray['Shipping Method']))
	{
		$shipMethod =  $dataArray['Shipping Method'];	
	}
	else
	{
		$shipMethod =  $dataArray['Billing Method'];
	}
	if(isset($dataArray['Purchase Order']))
	{
		$eav_attribute_option_value =  $dataArray['Purchase Order'];	
	}
	else
	{
		$eav_attribute_option_value =  "";
	}
	
	$Type =  $dataArray['Type'];
	$Industry =  $dataArray['Industry'];
	$Referredby =  $dataArray['Referred by'];
	$Order_Status  =   $dataArray['Order Status'];
	$Order_Description  =   $dataArray['Description'];
	$orderData =[
    'currency_id'  => $dataArray['Currency'],
    'email'        => $dataArray['Email'], //buyer email id
    'shipping_address' =>[
        'firstname'    => $shipf, //address Details
        'lastname'     => $shipL,        
		'street' => $shipstreet,
		'city' => $shipcity,
		'country_id' => $shipcntry,
		'region' => $shipingstate,
		'postcode' => $shipCode,
		'telephone' => $dataArray['Phone'],		
		'save_in_address_book' => 1
    ],
	'billing_address'=>[
		'firstname' => $bfname, //address Details
        'lastname'     => $blname,        
		'street' => $billStreet,
        'city' => $billcity,
		'country_id' => $billCountry,
		'region' => $billstate,
		'postcode' => $billCode,
		'telephone' => $dataArray['Phone'],
		'save_in_address_book' => 1
	]
]; 

		  
$params = $_SERVER; 
$bootstrap = Bootstrap::create(BP, $params); 
$obj = $bootstrap->getObjectManager(); 
$state = $obj->get('\Magento\Framework\App\State');
$state->setAreaCode('frontend'); 
$om = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $om->get('Psr\Log\LoggerInterface');
$storeManager->info('Magecomp Log');
//$total=$om->get('\Magento\Quote\Model\Quote\Address\Total');
$storeManager=$om->get('\Magento\Store\Model\StoreManagerInterface');
$messageManager  = $om->get('\Magento\Framework\Message\ManagerInterface'); 
$product=$om->get('\Magento\Catalog\Model\Product');
$context = $om->get('\Magento\Framework\App\Action\Context'); 
$resultPageFactory = $om->get('\Magento\Framework\View\Result\PageFactory'); 
$quote=$om->get('\Magento\Quote\Model\QuoteFactory');
$quoteManagement=$om->get('\Magento\Quote\Model\QuoteManagement');
$customerFactory=$om->get('\Magento\Customer\Model\CustomerFactory');
$customerRepository=$om->get('\Magento\Customer\Api\CustomerRepositoryInterface');
$orderService=$om->get('\Magento\Sales\Model\Service\OrderService');
$cart=$om->get('\Magento\Checkout\Model\Cart');
$formKey =$om->get('\Magento\Framework\Data\Form\FormKey');
$productFactory=$om->get('\Magento\Catalog\Model\ProductFactory');
$cartRepositoryInterface = $om->get('\Magento\Quote\Api\CartRepositoryInterface');
$cartManagementInterface = $om->get('\Magento\Quote\Api\CartManagementInterface');
$formKEYY = $formKey->getFormKey();
 
$addressInformation = $om->create('Magento\Checkout\Api\Data\ShippingInformationInterface');
$extAttributes = $addressInformation->getExtensionAttributes();
$resource = $om->get('Magento\Framework\App\ResourceConnection');
$connection = $resource->getConnection();


$k=0;

foreach($dataArray['Product Details'] as $values)
{
	
	$orderData['items'][$k]['form_key']=$formKEYY;
	$orderData['items'][$k]['sku']=$p[$k];
	$orderData['items'][$k]['qty']=$values['Quantity'];
	$orderData['items'][$k]['price']=$values['List Price'];
	$orderData['items'][$k]['discount']=$values['Discount'];
	$_product  = $productFactory->create()->loadByAttribute('sku',$p[$k]);
		$prodctID = $_product->getEntityId();
		$productdata  = $productFactory->create()->load($prodctID);
		
	if(isset($options[$k]))
		{
			if($_product) 
			{

				foreach($options[$k] as $key => $opt)
				{   
					foreach ($productdata->getOptions() as $o) 
					{   			   
						  if($o['title']==$key)
						  {
							 // echo "<pre>";
							 // print_r($o['title']);
								foreach ($o->getValues() as $value) 
								{    
									
                            	$optionName = str_replace('"',"",($value->get_title()));
                                   

									if($optionName==$opt)
									{
										 $ap[$value['option_id']] = $value['option_type_id'];
                                         //$orderData['items'][$k]['options'][$value['option_id']]=$value->get_title();
										 $orderData['items'][$k]['options'][$value['option_id']]=$value['option_type_id'];
										//$orderData['items'][$k]['options'][$o['title']]=$value->get_title();
									}
								 } 
							}
					}
			  
				}	
				
			}
		}
	$k++;
}

$store=$storeManager->getStore();
$websiteId =$storeManager->getStore()->getWebsiteId();
$customer=$customerFactory->create();
$customer->setWebsiteId($websiteId);
$customer->loadByEmail($orderData['email']);// load customet by email address
if(!$customer->getEntityId()){
    //If not avilable then create this customer
    $customer->setWebsiteId($websiteId)
        ->setStore($store)
        ->setFirstname($orderData['shipping_address']['firstname'])
        ->setLastname($orderData['shipping_address']['lastname'])
        ->setEmail($orderData['email'])
				->setPassword($orderData['email']);
				//$customer-> setData('custom_attributes[purchase_order]','1244');
    $customer->save();
}


$cart_id = $cartManagementInterface->createEmptyCart();
$cart = $cartRepositoryInterface->get($cart_id); 
$cart->setStore($store); 
// if you have already had the buyer id, you can load customer directly
$customer = $customerRepository->getById($customer->getEntityId());
$cart->setCurrency();
$cart->assignCustomer($customer); //Assign quote to customer
 
//add items in quote
$ap = array();
$count = 0;
foreach($orderData['items'] as $key=>$item)
{
			$product1  = $productFactory->create()->loadByAttribute('sku',$item['sku']);
			$prodctID = $product1->getEntityId();
			$productdata  = $productFactory->create()->load($prodctID); 
			$customOptions = $om->get('Magento\Catalog\Model\Product\Option')->getProductOptionCollection($productdata);
			$attributes = $productdata->getAttributes();
	

			$info = new \Magento\Framework\DataObject($item);
					//$object = new Varien_Object();
			$info->setPrice($item['price']);

						//$info->addData($params);			
			$cart->addProduct($productdata, $info);
			$cart->save();

	//die();
}

/*
$cart->getBillingAddress()->addData($orderData['billing_address']);
$cart->getShippingAddress()->addData($orderData['shipping_address']);
*/
$cart->getBillingAddress()->addData($orderData['billing_address']);
//$quote->getShippingAddress()->addData($orderData['shipping_address']);
$cart->getShippingAddress()->addData($orderData['shipping_address']);
 
/*$this->shippingRate
    ->setCode('freeshipping_freeshipping')
    ->getPrice(1);
*/
$shippingAddress = $cart->getShippingAddress();
 
$shippingAddress->setCollectShippingRates(true)
    ->collectShippingRates()
    ->setShippingMethod('rockshippingmodel_rockshippingmodel'); //shipping method
 
$cart->setPaymentMethod('cashondelivery'); //payment method
 
$cart->setInventoryProcessed(true);
 
// Set sales order payment
$cart->getPayment()->importData(['method' => 'cashondelivery']);
 
// Collect total and save
$cart->collectTotals();
 
// Submit the quote and create the order
$cart->save();
$cart = $cartRepositoryInterface->get($cart->getId());
$order_id = $cartManagementInterface->placeOrder($cart->getId());

echo "Order Created -".$order_id."<br>Thanks for Submitting";
$order = $om->create('\Magento\Sales\Model\Order')
                           ->load($order_id); 
		$eav_attribute_option = $resource->getTableName('aitoc_sales_order_value');
		
		$sql = "Update $eav_attribute_option set value = '".$eav_attribute_option_value."' WHERE attribute_id = 179 and order_id = $order_id";
		try {
				$resp = $connection->query($sql);
		} catch (Exception $e) {
				echo '<pre>';  print_r($e->getMessage());
		}
$order->setState($Order_Status)->setStatus($Order_Status);
$orderIncrementId = $order->getIncrementId();
$history = $order->addStatusHistoryComment($Order_Description, true);
$order->save();

$updatexml = "";
$updatexml.='<SalesOrders><row no="1">';
$updatexml.='<FL val="Order ID">'.$orderIncrementId.'</FL>';
$updatexml.='</row></SalesOrders>';

		$updateurl = "https://crm.zoho.com/crm/private/xml/SalesOrders/updateRecords?";
		$updatequery = "authtoken=03d95794d824e8c2c65a2a2b6cfedc65&scope=crmapi&id=".$reuestID."&xmlData=".$updatexml;
		$updateResult = salesordercurl($updateurl,$updatequery);
		
// header("Location: https://crm.zoho.com/crm/org663283527/tab/SalesOrders/".$reuestID."");


function salesordercurl($url,$query=null)
 {     
	 
	$ch1 = curl_init();
                curl_setopt($ch1, CURLOPT_URL, $url);
                curl_setopt($ch1, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, FALSE);
                curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch1, CURLOPT_TIMEOUT, 30);
                curl_setopt($ch1, CURLOPT_POST, 1);
                curl_setopt($ch1, CURLOPT_POSTFIELDS, $query);
                $response = curl_exec($ch1);
                curl_close($ch1);
								$result = json_decode($response,true);
								return $result;	
        //echo $imageurl.print_r($post);
 }

?>