<?php
ini_set('display_errors', 1);
ini_set('max_execution_time', 0);
ini_set("memory_limit", "-1");
set_time_limit(0);
error_reporting(E_ALL);
use Magento\Framework\App\Bootstrap;
   
/**
 * If the external file is in the root folder
 */
require __DIR__ . '/app/bootstrap.php';
  
$params = $_SERVER;
   
$bootstrap = Bootstrap::create(BP, $params);
   
$obj = $bootstrap->getObjectManager();
   
$state = $obj->get('Magento\Framework\App\State');
$state->setAreaCode('frontend');
 
$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
  
$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
$store = $storeManager->getStore();
$websiteId = $storeManager->getStore()->getWebsiteId();
 
$firstName = 'Thomas';
$lastName = 'Raj';
$email = 'thomas.raj@example.com';
$password = 'Test@123';
   
$address = array(
    'customer_address_id' => '',
    'prefix' => '',
    'firstname' => $firstName,
    'middlename' => '',
    'lastname' => $lastName,
    'suffix' => '',
    'company' => '', 
    'street' => array(
        '0' => 'Customer Address 1', // this is mandatory
        '1' => 'Customer Address 2' // this is optional
    ),
    'city' => 'New York',
    'country_id' => 'US', // two letters country code
    'region' => 'New York', // can be empty '' if no region
    'region_id' => '43', // can be empty '' if no region_id
    'postcode' => '10450',
    'telephone' => '123-456-7890',
    'fax' => '',
    'save_in_address_book' => 1
);
 
$customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
 
/**
 * check whether the email address is already registered or not
 */
$customer = $customerFactory->setWebsiteId($websiteId)->loadByEmail($email);
if (!$customer->getId()) {
    try {
        $customer = $objectManager->get('\Magento\Customer\Model\CustomerFactory')->create();
        $customer->setWebsiteId($websiteId);
        $customer->setEmail($email);
        $customer->setFirstname($firstName);
        $customer->setLastname($lastName);
        $customer->setPassword($password);
        $customer->save();
   
        $customer->setConfirmation(null);
        $customer->save();
 
        $customAddress = $objectManager->get('\Magento\Customer\Model\AddressFactory')->create();
        $customAddress->setData($address)
                      ->setCustomerId($customer->getId())
                      ->setIsDefaultBilling('1')
                      ->setIsDefaultShipping('1')
                      ->setSaveInAddressBook('1');
        $customAddress->save();  
    } catch (Exception $e) {
        echo $e->getMessage();
    }
}
 
$customer = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface')->getById($customer->getId());
try {
 
    $quoteFactory = $objectManager->get('\Magento\Quote\Model\QuoteFactory')->create();
    $quoteFactory->setStore($store);
    $quoteFactory->setCurrency();
    $quoteFactory->assignCustomer($customer);
 
    $productIds = array(1415 => 2);  
    $productIds = array('product' => 1415,'options' => array(136 => 355),'qty' => 2);
        );  

    //foreach($productIds as $productId => $qty) {
        $product = $objectManager->get('\Magento\Catalog\Model\ProductRepository')->getById($productIds['product']);// get product by product id 
        $quoteFactory->addProduct($productIds['product'], $productIds['qty'],$productIds['options']);  // add products to quote
    //} 
  
    /*
     * Set Address to quote
     */
    $quoteFactory->getBillingAddress()->addData($address);
    $quoteFactory->getShippingAddress()->addData($address);
 
    /*
     * Collect Rates and Set Shipping & Payment Method
     */
    $shippingAddress = $quoteFactory->getShippingAddress();
    $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod('freeshipping_freeshipping'); //shipping method
 //rockshippingmodel_rockshippingmodel
    $quoteFactory->setPaymentMethod('cashondelivery'); //payment method
    $quoteFactory->setInventoryProcessed(false);
    $quoteFactory->save();
 
    /*
     * Set Sales Order Payment
     */
    $quoteFactory->getPayment()->importData(['method' => 'cashondelivery']);
 
    /*
     * Collect Totals & Save Quote
     */
    $quoteFactory->collectTotals()->save();
 
  
    /*
     * Create Order From Quote
     */
    $order = $objectManager->get('\Magento\Quote\Model\QuoteManagement')->submit($quoteFactory);
   
    //$order->setEmailSent(0);

    //echo 'Order Id:' . $order->getRealOrderId();
} catch (Exception $e) {
    echo $e->getMessage();
}
