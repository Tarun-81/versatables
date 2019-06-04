<?php
error_reporting(1);
ini_set('max_execution_time', 0);
use \Magento\Framework\App\Bootstrap;

require __DIR__ . '/app/bootstrap.php';

$bootstrap = Bootstrap::create(BP, $_SERVER);
$objectManager = $bootstrap->getObjectManager();
$url = \Magento\Framework\App\ObjectManager::getInstance();
$storeManager = $url->get('\Magento\Store\Model\StoreManagerInterface');
$mediaurl = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
$state = $objectManager->get('\Magento\Framework\App\State');
$state->setAreaCode('frontend');

$objectManager = \Magento\Framework\App\ObjectManager::getInstance();

$_storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
$_product = $objectManager->get('\Magento\Catalog\Model\Product');
$_formkey = $objectManager->get('\Magento\Framework\Data\Form\FormKey');
$quote = $objectManager->get('\Magento\Quote\Model\QuoteFactory');
$quoteManagement = $objectManager->get('\Magento\Quote\Model\QuoteManagement');
$customerFactory = $objectManager->get('\Magento\Customer\Model\CustomerFactory');
$customerRepository = $objectManager->get('\Magento\Customer\Api\CustomerRepositoryInterface');
$orderService = $objectManager->get('\Magento\Sales\Model\Service\OrderService');
$_productRepository = $objectManager->get('\Magento\Catalog\Model\ProductRepository');
$productFactory = $objectManager->get('\Magento\Catalog\Model\ProductFactory');
$_orderRepository = $objectManager->get('\Magento\Sales\Model\Service\InvoiceService');
$_invoiceService = $objectManager->get('\Magento\Sales\Api\OrderRepositoryInterface');
$_transaction = $objectManager->get('\Magento\Framework\DB\Transaction');
$_order = $objectManager->get('\Magento\Sales\Model\Order');
$_convertOrder = $objectManager->get('\Magento\Sales\Model\Convert\Order');
$_resource = $objectManager->get('\Magento\Framework\App\ResourceConnection');
$cartRepositoryInterface = $objectManager->get('\Magento\Quote\Api\CartRepositoryInterface');
$cartManagementInterface = $objectManager->get('\Magento\Quote\Api\CartManagementInterface');


$store = $_storeManager->getStore();
$websiteId = $_storeManager->getStore()->getWebsiteId();
$customer = $customerFactory->create();
$customer->setWebsiteId($websiteId);

/*
$payments = array();

        if ($datum['grand_total'] > 0) {
            $tempOrder = array();
            $tempOrder['currency_id'] = 'USD';
            $tempOrder['email'] = $datum['customer_email'];
            $tempOrder['shipping_address'] = $datum['shipping_address'];
            if (isset($datum['billing_address']))
                $tempOrder['billing_address'] = $datum['billing_address'];


                $tempOrder['grand_total'] = $datum['grand_total'];
                $tempOrder['shipping_amount'] = $datum['shipping_amount'];
                $tempOrder['tax_amount'] = $datum['tax_amount'];
                $tempOrder['shipping_description'] = "Free Shipping";//$datum['shipping_description'];
                $tempOrder['state'] = $datum['state'];
                $tempOrder['status'] = $datum['status'];


                $tempOrder['payments'] = $datum['payments'];

                foreach ($datum['trackings'] as $tracking) {
                    $tempOrder['shipping_description'] = $tracking['carrier_name'];
                }

                $tempOrder['items'] = $datum['items'];
                if ($datum['created_at']) {
                    $tempOrder['created_at'] = $datum['created_at'];
                }

                $orderData = $tempOrder;


                $store = $_storeManager->getStore();
                $websiteId = $_storeManager->getStore()->getWebsiteId();
                $customer = $customerFactory->create();
                $customer->setWebsiteId($websiteId);
                $customer->loadByEmail($orderData['email']);// load customet by email address

                $quote1 = $quote->create(); //Create object of quote
                if (!$customer->getEntityId()) {
                    //If not avilable then create this customer

                    $quote1->setCheckoutMethod('guest')
                        ->setCustomerId(null)
                        ->setCustomerEmail($orderData['email'])
                        ->setCustomerIsGuest(true)
                        ->setCustomerGroupId(0);
                    //$logger->info("Customer not exist " . $orderData['email'] . "\r\n");
                    //continue;
                } else {
                    $customer = $customerRepository->getById($customer->getEntityId());
                    $quote1->assignCustomer($customer); //Assign quote to customer
                }

                // if you have allready buyer id then you can load customer directly


                $quote1->setStore($store); //set store for which you create quote


                $quote1->setCurrency();


                 //add items in quote
                $shipping_method = "";
                foreach ($orderData['items'] as $item) {
                    $product = $productFactory->create();
                    $product->load($product->getIdBySku($item['sku']));


                    if ($product->getId()) {
                        $product->setPrice($item['price']);

                        $product->setTaxClassId(0);
                        $quote1->addProduct($product, $item['qty_ordered']);


                    }
                }


                //Set Address to quote
                if (isset($orderData['billing_address'])) {
                    $quote1->getBillingAddress()->addData($orderData['billing_address']);
                } else {
                    $billing = $orderData['shipping_address'];
                    $billing['address_type'] = 'billing';
                    $quote1->getBillingAddress()->addData($billing);
                }
                $orderData['shipping_address']['p21_freight'] = "FREIGHT FREE";


                $quote1->getShippingAddress()->addData($orderData['shipping_address']);

                // Collect Rates and Set Shipping & Payment Method

                $shippingAddress = $quote1->getShippingAddress();
                $shippingAddress->setCollectShippingRates(true)
                    ->collectShippingRates()
                    ->setShippingMethod('freeshipping_freeshipping'); //shipping method
                // $quote->setPaymentMethod('elementpayment'); //payment method
                $quote1->setInventoryProcessed(false); //not effetc inventory


                $quote1->save(); //Now Save quote and your quote is ready

                $payment_method = "";
                $payment = array("method" => "checkmo");


                    $payment_method = $payment['method'];


                // Set Sales Order Payment
                $quote1->getPayment()->importData($payment);


                // Collect Totals & Save Quote
                $quote1->collectTotals()->save();



                $manual_date = "0000-00-00 00:00:00";
                if (isset($orderData['created_at'])) {
                    //  4/26/2016
                    $date = explode("/", $orderData['created_at']);
                    //print_r($date);
                    $orderCustomData['created_at'] = $date[2] . "-" . str_pad($date[0], 2, "0", STR_PAD_LEFT) . "-" . str_pad($date[1], 2, "0", STR_PAD_LEFT) . " 05:05:05";
                    $manual_date = $orderCustomData['created_at'];
                    //exit;

                }


                $orderCustomData['shipping_amount'] = $orderData['shipping_amount'];
                $orderCustomData['base_shipping_amount'] = $orderData['shipping_amount'];
                $orderCustomData['grand_total'] = $orderData['grand_total'];
                $orderCustomData['base_grand_total'] = $orderData['grand_total'];
                $orderCustomData['tax_amount'] = $orderData['tax_amount'];
                $orderCustomData['base_tax_amount'] = $orderData['tax_amount'];
                $orderCustomData['shipping_description'] = $orderData['shipping_description'];
                $orderCustomData['state'] = 'complete';
                $orderCustomData['status'] = 'complete';

                // Create Order From Quote
                $order = "";
                try {

                    $order = $quoteManagement->submit($quote1, $orderCustomData);


                } catch (Exception $e) {

                }*/
?>