<?php

namespace Lof\RequestForQuote\Helper;

use Magento\Framework\Validator\EmailAddress;

class Quote extends \Magento\Framework\App\Helper\AbstractHelper
{

    protected $_remoteAddress;
    protected $_rfqModel;
    protected $_quoteCollectionFactory;
    protected $rfqMail;
    protected $_dataHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        //\Magento\Catalog\Model\Product $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Quote\Api\CartManagementInterface $cartManagementInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Sales\Model\Order $order,
        \Lof\RequestForQuote\Model\Quote $rfqModel,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Lof\RequestForQuote\Helper\Data $rfqHelper,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistryInterface
    )
    {
        $this->_storeManager = $storeManager;
        //$this->_product = $product;
        $this->_productFactory = $productFactory;
        $this->cartRepositoryInterface = $cartRepositoryInterface;
        $this->cartManagementInterface = $cartManagementInterface;
        $this->customerFactory = $customerFactory;
        $this->customerRepository = $customerRepository;
        $this->order = $order;
        $this->rfqModel = $rfqModel;
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->_dataHelper = $rfqHelper;
        $this->stockRegistryInterface = $stockRegistryInterface;
        parent::__construct($context);
    }

    protected function prepareOrderData($value)
    {
        $_OrderData = array();

        $_OrderData = array(
            'sale_id' => $value['commercial_sale_id'],
            'create_date' => $value['created_date'],
            'updated_date' => $value['updated_date'],
            'source' => $value['source'],
            'status' => $value['status'],
            'audit_status' => $value['audit_status'],
            'audit_status_date' => $value['audit_status_date'],
            'proposal_text' => $value['proposal_text'],
            'call_time' => $value['call_time'],
            'followup_date' => $value['followup_date'],
            'followuptext' => $value['followuptext'],
            'details' => $value['details'],
            'deadline' => $value['deadline'],
            'internal_notes' => $value['internal_notes'],
            'proposal_number' => $value['proposal_number'],
            'reminder_notes' => $value['reminder_notes'],
            'user_data' => $value['user_data']
        );

        $_BillingRegion = $this->getRegionID($value['user_data']['state_short_name']);

        $value['user_data']['customer_name'] = trim($value['user_data']['customer_name']);
        if (isset($value['user_data']['customer_name']) && $value['user_data']['customer_name'] != "") {

//            $_billsaddressstr = str_replace(array('`'), ' ', $value['user_data']['customer_name']);
            $_billsaddressstr = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $value['user_data']['customer_name']);
            $_billsaddressstr = trim($_billsaddressstr);
            $billing_name = explode(' ', (string)preg_replace('!\s+!', ' ', $_billsaddressstr));
            $billing_firstName = isset($billing_name[0]) ? $billing_name[0] : 'Missing';

            if (count($billing_name) == 3) {
                $billing_middleName = isset($billing_name[1]) ? $billing_name[1] : '';
                $billing_lastName = isset($billing_name[2]) ? $billing_name[2] : 'Missing';
            } else {
                $billing_middleName = '';
                $billing_lastName = isset($billing_name[1]) ? $billing_name[1] : 'Missing';
            }
        } else {
            $billing_firstName = $billing_lastName = "Missing";
        }

        $_OrderData['billing_data'] = array(
            'first_name' => $billing_firstName,
            'last_name' => $billing_lastName,
            'company' => trim($value['user_data']['company_name']),
            'phone' => $value['user_data']['phone_number'],
            'address1' => $value['user_data']['address'],
            'address2' => $value['user_data']['address2'],
            'city' => $value['user_data']['city'],
            //'province' => $value['user_data']['province'],
            'billing_state_name' => $value['user_data']['state_name'],
            'billing_country_name' => $value['user_data']['country_name'],
            'billing_country_Code' => $value['user_data']['country_Code'],
            'billing_zip' => $value['user_data']['zip'],
            'billing_region' => $_BillingRegion,
            'billing_state_short_name' => $value['user_data']['state_short_name']
        );

        $shipping_firstName = $shipping_middleName = $shipping_lastName = '';
        $value['shipping_data']['ship_customer_name'] = trim($value['shipping_data']['ship_customer_name']);
        if (isset($value['shipping_data']['ship_customer_name']) && $value['shipping_data']['ship_customer_name'] != "") {

            $_saddressstr = str_replace(array('`'), ' ', $value['shipping_data']['ship_customer_name']);
            $_saddressstr = preg_replace('/[^A-Za-z0-9\. -]/', ' ', $value['shipping_data']['ship_customer_name']);
            $_saddressstr = trim($_saddressstr);
            //$shipping_name = explode(' ', (string) $_saddressstr);
            $shipping_name = explode(' ', (string)preg_replace('!\s+!', ' ', $_saddressstr));
            //$shipping_firstName = isset($shipping_name[0]) ? $shipping_name[0] : $_OrderData['user_data']['fname'];
            $shipping_firstName = isset($shipping_name[0]) ? $shipping_name[0] : 'Missing';

            if (count($shipping_name) == 3) {
                $shipping_middleName = isset($shipping_name[1]) ? $shipping_name[1] : '';
                //$shipping_lastName = isset($shipping_name[2]) ? $shipping_name[2] : $_OrderData['user_data']['lname'];
                $shipping_lastName = isset($shipping_name[2]) ? $shipping_name[2] : 'Missing';
            } else {
                $shipping_middleName = '';
                //$shipping_lastName = isset($shipping_name[1]) ? $shipping_name[1] : $_OrderData['user_data']['lname'];
                $shipping_lastName = isset($shipping_name[1]) ? $shipping_name[1] : 'Missing';
            }
        } else {
            $shipping_firstName = $shipping_lastName = "Missing";
        }

        $_shippingRegion = $this->getRegionID($value['shipping_data']['shipping_state_short_name']);

        $_OrderData['shipping_data'] = array(
            'shipping_first_name' => trim($shipping_firstName),
            'shipping_middle_name' => trim($shipping_middleName),
            'shipping_last_name' => trim($shipping_lastName),
            'shipping_company' => trim($value['shipping_data']['ship_company_name']),
            'shipping_address1' => ($value['shipping_data']['ship_add1'] != "" ? $value['shipping_data']['ship_add1'] : "Missing"),
            'shipping_address2' => $value['shipping_data']['ship_add2'],
            'shipping_city' => $value['shipping_data']['ship_city'],
            'shipping_state' => $value['shipping_data']['ship_state'],
            'shipping_state_name' => $value['shipping_data']['shipping_state_name'],
            //'shipping_province' => $value['shipping_province'],
            'shipping_zip' => $value['shipping_data']['ship_zip'],
            'ship_phone_number' => $value['shipping_data']['ship_phone_number'],
            'shipping_country' => $value['shipping_data']['country_name'],
            'shipping_country_Code' => $value['shipping_data']['country_Code'],
            'shipping_country_name' => $value['shipping_data']['country_name'],
            'shipping_state_short_name' => $value['shipping_data']['shipping_state_short_name'],
            'shipping_region' => $_shippingRegion,
        );


        $_OrderData['order_items'] = $value['product_data'];

        $_OrderData['attach'] = array();
        if ($value['attach1']) {
            $_OrderData['attach'][] = $value['attach1'];
        }
        if ($value['attach2']) {
            $_OrderData['attach'][] = $value['attach2'];
        }
        if ($value['attach3']) {
            $_OrderData['attach'][] = $value['attach3'];
        }
        if ($value['attach4']) {
            $_OrderData['attach'][] = $value['attach4'];
        }
        if ($value['attach5']) {
            $_OrderData['attach'][] = $value['attach5'];
        }
        if ($value['attach6']) {
            $_OrderData['attach'][] = $value['attach6'];
        }
        if ($value['attach7']) {
            $_OrderData['attach'][] = $value['attach7'];
        }
        if ($value['attach8']) {
            $_OrderData['attach'][] = $value['attach8'];
        }


        $_OrderData['notes'] = trim($value['notes']);
        $_OrderData['terms'] = trim($value['terms']);
        $_OrderData['break_line'] = trim($value['break_line']);
        $_OrderData['expect_notes'] = trim($value['expect_notes']);
        $_OrderData['proposal_log'] = $value['proposal_log'];
        //$_OrderData['status_log'] = $value['status_log'];
        //$_OrderData['status_log_payment'] = $value['status_log_payment'];

        return $_OrderData;
    }

    public function validEmail($data)
    {
        $email = (string)str_replace(" ", "", trim($data['user_data']['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email = $data['sale_id'] . "@patioshopper.com";
        }

        return $email;
    }

    public function createQuote($_oData, $import_file = '', $cnt = 0)
    {

        $data = $this->prepareOrderData($_oData);
        //echo "<pre>"; print_r($data); exit;

        $_logNumber = '[ ' . $import_file . ' -- sale_id:' . $data['sale_id'] . ' ]';

        $tmpsku = array("1" => "product-missing", "2" => "product-missing-1", "3" => "product-missing-2", "4" => "product-missing-4", '5' => 'product-missing-5',
            '6' => 'product-missing-6', '7' => 'product-missing-7', '8' => 'product-missing-8', '9' => 'product-missing-9', '10' => 'product-missing-10');

        $tcn = 1;
        $_productMissing = 0;
        $_missingSku = array();
        $productData = array();
        foreach ($data['order_items'] as $item) {
            $sku = (string)$item['product_id'];

            if ($product = $this->_productFactory->create()->loadByAttribute('sku', $sku)) {
                $_productMissing = 0;
                $productData[] = array(
                    'product_id' => $product->getId(),
                    'qty' => (int)$item['product_qty'],
                    'custom_price' => (string)$item['web_price'],
                    'sku' => $sku
                );
            } else {

                $_defaultsku = isset($tmpsku[$tcn]) ? $tmpsku[$tcn] : "product-missing";

                if ($product = $this->_productFactory->create()->loadByAttribute('sku', $_defaultsku)) {
                    $_productMissing = 1;
                    $_missingSku[] = $item['product_id'];
                    $productData[] = array(
                        'product_id' => $product->getId(),
                        'qty' => (int)$item['product_qty'],
                        'custom_price' => (string)$item['web_price'],
                        'sku' => $_defaultsku
                    );
                } else {
                    $this->addLog($_logNumber . ' Error : No product found with sku: ' . $sku, true, $import_file);
                    //return;
                    continue;
                }
            }
            $tcn++;
        }

        $email = $this->validEmail($data);

        $orderData = array(
            'currency' => 'USD',
            'email' => $email,
            'created_at' => $data['create_date'],
            'billing_address' => array(
                'prefix' => '',
                'firstname' => $data['billing_data']['first_name'],
                'middlename' => '',
                'lastname' => $data['billing_data']['last_name'],
                'suffix' => '',
                'company' => trim($data['billing_data']['company']),
                'street' => array(
                    (string)$data['billing_data']['address1'],
                    (string)$data['billing_data']['address2'],
                ),
                'city' => $data['billing_data']['city'],
                'country_id' => $data['billing_data']['billing_country_Code'],
                'region_id' => $data['billing_data']['billing_region'],
                'postcode' => $data['billing_data']['billing_zip'],
                'telephone' => (string)$data['billing_data']['phone'],
                'fax' => '',
                'save_in_address_book' => 1,
                'is_default_billing' => 1,
            ),
            'shipping_address' => array(
                'prefix' => '',
                'firstname' => $data['shipping_data']['shipping_first_name'],
                'middlename' => $data['shipping_data']['shipping_middle_name'],
                'lastname' => $data['shipping_data']['shipping_last_name'],
                'suffix' => '',
                'company' => trim($data['shipping_data']['shipping_company']),
                'street' => array(
                    (string)$data['shipping_data']['shipping_address1'],
                    (string)$data['shipping_data']['shipping_address2'],
                ),
                'city' => $data['shipping_data']['shipping_city'],
                'country_id' => $data['shipping_data']['shipping_country_Code'],
                'region_id' => $data['shipping_data']['shipping_region'],
                'postcode' => $data['shipping_data']['shipping_zip'],
                'telephone' => $data['shipping_data']['ship_phone_number'],
                'fax' => '',
                'save_in_address_book' => 1,
                'is_default_shipping' => 1
            ),
            'items' => $productData
        );

        try {

            $store = $this->_storeManager->getStore();
            $websiteId = $this->_storeManager->getStore()->getWebsiteId();
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->loadByEmail($orderData['email']);

            if (!$customer->getEntityId()) {
                //If not avilable then create this customer 
                $customer->setWebsiteId($websiteId)
                    ->setStore($store)
                    ->setFirstname($orderData['shipping_address']['firstname'])
                    ->setLastname($orderData['shipping_address']['lastname'])
                    ->setEmail($orderData['email'])
                    ->setPassword($orderData['email']);
                $customer->save();
            }

            $cartId = $this->cartManagementInterface->createEmptyCart();
            $quote = $this->cartRepositoryInterface->get($cartId);
            $quote->setStore($store);

            $customer = $this->customerRepository->getById($customer->getEntityId());
            $customer_id = $customer->getId();

            $this->addCustomerAddresses($orderData, $customer->getId());

            $quote->setCurrency();
            $quote->assignCustomer($customer);

            //add items in quote
            /*foreach($orderData['items'] as $item){
                $product = $this->_productFactory->create()->load($item['product_id']);
                $product->setPrice($item['price']);
                $quote->addProduct($product, intval($item['qty']));
            }*/

            foreach ($orderData['items'] as $item) {
                $product = $this->_productFactory->create()->load($item['product_id']);
                $product->setPrice($item['custom_price']);
                $product->setStatus(1);
                $product->setIsInStock(true);
                $product->setStockData(array(
                    'use_config_manage_stock' => 0,
                    'min_sale_qty' => 1,
                    'is_in_stock' => 1,
                ));

                //To fix min sale qty different issue
                $productStockObj = $this->stockRegistryInterface->getStockItem($product->getId());
                $productStockObj->setData('min_sale_qty', 1);
                $productStockObj->setData('use_config_manage_stock', 1);
                $productStockObj->setData('use_config_min_sale_qty', 1);

                $quote->addProduct($product, intval($item['qty']));

                $currentItem = $quote->getItemByProduct($product);
                $currentItem->setCustomPrice($item['custom_price']);
                $currentItem->setOriginalCustomPrice($item['custom_price']);
            }

            //Set Address to quote
            $quote->getBillingAddress()->addData($orderData['shipping_address']);
            $quote->getShippingAddress()->addData($orderData['shipping_address']);
            $quote->getShippingAddress()->setCollectShippingRates(true);
            $quote->collectTotals();

            $quote->save();

            $data['o_quote_id'] = $quote->getId();

            $_rfqquote = $this->rfqModel;

            $limit = $this->_dataHelper->getConfig("quote_item/limit_useage");
            $limit = $limit ? (int)$limit : '';
            $expiry_day = $this->_dataHelper->getConfig("quote_item/expiry_day");
            $expiry_day = $expiry_day ? (int)$expiry_day : 5;
            $remind_day = $this->_dataHelper->getConfig("quote_item/remind_day");
            $quote_id_prefix = $this->_dataHelper->getConfig("general/quote_id_prefix");
            $digits_number = $this->_dataHelper->getConfig("general/digits_number");
            $digits_number = !$digits_number ? (int)$digits_number : 1000000000;
            $firstname = $lastname = "";
            $customer_group_id = 0;

            if ($customer) {
                $firstname = $customer->getFirstName();
                $lastname = $customer->getLastName();
                $customer_group_id = $customer->getGroupId();
            }

            $expiry = null;
            $send_expiry_email = 0;
            if ($expiry_day) {
                $expiry_day = (int)$expiry_day;
                $current_date = $quote->getCreatedAt();
                $date = strtotime("+" . $expiry_day . " days", strtotime($current_date));
                $expiry = date("Y-m-d H:i:s", $date);
                $send_expiry_email = 1;
            }

            $remind = null;
            $send_remind_email = 0;
            if ($remind_day) {
                $remind_day = (int)$remind_day;
                $current_date = $quote->getCreatedAt();
                $date = strtotime("+" . $remind_day . " days", strtotime($current_date));
                $remind = date("Y-m-d H:i:s", $date);
                $send_remind_email = 1;
            }

            $count = $this->_quoteCollectionFactory->create()->getSize();
            if ($count) {
                $incrementId = $digits_number + $count + 1;
            } else {
                $incrementId = $digits_number + 1;
            }
            $incrementId = $quote_id_prefix . $incrementId;

            $admin_note = isset($data['notes']) ? $data['notes'] : '';
            $admin_note = $this->_stripScriptTags($admin_note);

            $terms = isset($data['terms']) ? $data['terms'] : '';
            $terms = $this->_stripScriptTags($terms);

            $wtexpect = isset($data['expect_notes']) ? $data['expect_notes'] : '';
            $wtexpect = $this->_stripScriptTags($wtexpect);

            $break_line = isset($data['break_line']) ? $data['break_line'] : '';
            $break_line = $this->_stripScriptTags($break_line);

            $_followup_date = "";
            if ($data['followup_date'] != "" && $data['followup_date'] != "0000-00-00 00:00:00") {
                $_followup_date = $this->_dataHelper->formatDate($data['followup_date']);
            }

            $qdata = [
                'quote_id' => $quote->getId(),
                'increment_id' => $incrementId,
                'status' => 'pending', //$data['status'],
                'email' => ($customer && $customer->getEmail()) ? $customer->getEmail() : $email,
                'customer_id' => $customer ? $customer->getId() : '',
                'first_name' => $firstname,
                'last_name' => $lastname,
                'limit_useage' => $limit,
                'expiry' => $expiry,
                'remind' => $remind,
                //'sales_rep_id'  => $post['sales_rep'],
                'source' => $data['source'],
                'proposal_number' => $data['proposal_number'],
                'call_time' => $data['call_time'],
                'followup_date' => $_followup_date,
                'reminder_notes' => $data['reminder_notes'],
                'audit_status' => $data['audit_status'],
                'send_expiry_email' => $send_expiry_email,
                'send_remind_email' => $send_remind_email,
                'admin_note' => $admin_note,
                'terms' => $terms,
                'wtexpect' => $wtexpect,
                'break_line' => $break_line
            ];

            $_rfqquote->setData($qdata);
            $_rfqquote->save();

            $data['q_increment_id'] = $incrementId;
            $data['q_rfq_id'] = $_rfqquote->getId();

//            $this->addProposalLog($data);
//            $this->addAttachment($data);

            $this->addLog($_logNumber . ' Success : ' . $incrementId, false, $import_file);
            $this->addLog('=======================================', false, $import_file);

            return "<p style='color:green;margin: 0;padding: 0;'>[" . $import_file . "] Success: [sale_id:" . $data['sale_id'] . " ] == [New Sale ID:" . $incrementId . " ] == [ Time: " . date('Y-m-d H:i:s') . " ]</p>";

        } catch (\Exception $e) {
            //echo $e->getMessage();
            $this->addLog($_logNumber . " Error: " . $e->getMessage(), true);

            return "<p style='color:red;margin: 0;'>[" . $import_file . "] Error: [sale_id:" . $data['sale_id'] . "] == [ Message: " . $e->getMessage() . " ] == [ Time: " . date('Y-m-d H:i:s') . " ]</p>";
        }
    }

    public function addCustomerAddresses($orderData, $customerId)
    {

        $_shippingData = $orderData['shipping_address'];
        $_billingData = $orderData['billing_address'];

        /*Add customer address*/
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();

        $addresss = $objectManager->get('\Magento\Customer\Model\AddressFactory');

        $billaddress = $addresss->create();
        $billaddress->setCustomerId($customerId)
            ->setFirstname($_billingData['firstname'])
            ->setLastname($_billingData['lastname'])
            ->setCountryId($_billingData['country_id'])
            ->setRegionId($_billingData['region_id'])//state/province, only needed if the country is USA
            ->setPostcode($_billingData['postcode'])
            ->setCity($_billingData['city'])
            ->setTelephone($_billingData['telephone'])
            ->setFax($_billingData['fax'])
            ->setCompany($_billingData['company'])
            ->setStreet($_billingData['street'])
            ->setIsDefaultBilling('1')
            ->setSaveInAddressBook('1');
        $billaddress->save();

        $address = $addresss->create();
        $address->setCustomerId($customerId)
            ->setFirstname($_shippingData['firstname'])
            ->setLastname($_shippingData['lastname'])
            ->setCountryId($_shippingData['country_id'])
            ->setRegionId($_shippingData['region_id'])//state/province, only needed if the country is USA
            ->setPostcode($_shippingData['postcode'])
            ->setCity($_shippingData['city'])
            ->setTelephone($_shippingData['telephone'])
            ->setFax($_shippingData['fax'])
            ->setCompany($_shippingData['company'])
            ->setStreet($_shippingData['street'])
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
        $address->save();
    }

    public function addProposalLog($data)
    {

        $_proposalData = isset($data['proposal_log']) ? $data['proposal_log'] : array();
        //if($_quoteId && count($_proposalData) > 0){
        if (count($_proposalData) > 0) {
            foreach ($_proposalData as $_pdata) {
                $_logdata = [
                    'quote_id' => $data['o_quote_id'],
                    'increment_id' => $data['q_increment_id'],
                    'internal_comment' => trim($_pdata['internal_comment']),
                    'status' => $_pdata['status'],
                    //'sent_from'  => $_pdata['sales_rep'],
                    'sales_rep' => $_pdata['sent_from'],
                    'order_id' => '', //$this->_order->getId()
                ];

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $_rfqQuoteLog = $objectManager->create('Lof\RequestForQuote\Model\Log');
                $_rfqQuoteLog->setData($_logdata);
                $_rfqQuoteLog->save();
            }
        }
    }

    public function addAttachment($data)
    {

        $_attachmeData = isset($data['attach']) ? $data['attach'] : array();
        if (count($_attachmeData) > 0) {

            foreach ($_attachmeData as $_pdata) {

                $file_type = "";
                $fileName = trim($_pdata);
                $lastDotPos = strrpos($fileName, '.');
                if ($lastDotPos) {
                    $file_type = substr($fileName, $lastDotPos + 1);
                }

                $_logdata = [
                    'quote_id' => $data['q_rfq_id'],
                    'increment_id' => $data['q_increment_id'],
                    'file_path' => trim($_pdata),
                    'created_at' => date('Y-m-d H:i:s'),
                    'file_type' => $file_type
                ];

                $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
                $_rfqQuoteLog = $objectManager->create('Lof\RequestForQuote\Model\Attachment');
                $_rfqQuoteLog->setData($_logdata);
                $_rfqQuoteLog->save();
            }
        }
    }

    protected function addLog($_message, $_error)
    {

        if ($_error == true) {
            $_errorFilename = "error.log";
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/leads_import/' . $_errorFilename);
        } else {
            $_successFilename = "success.log";
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/leads_import/' . $_successFilename);
        }

        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info($_message);
    }

    protected function _stripScriptTags($html)
    {
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    }

    protected function getRegionID($_regiodShortCode)
    {

        $_magentoRegions = array(
            array('region_id' => '1', 'country_id' => 'US', 'code' => 'AL', 'default_name' => 'Alabama'),
            array('region_id' => '2', 'country_id' => 'US', 'code' => 'AK', 'default_name' => 'Alaska'),
            array('region_id' => '3', 'country_id' => 'US', 'code' => 'AS', 'default_name' => 'American Samoa'),
            array('region_id' => '4', 'country_id' => 'US', 'code' => 'AZ', 'default_name' => 'Arizona'),
            array('region_id' => '5', 'country_id' => 'US', 'code' => 'AR', 'default_name' => 'Arkansas'),
            array('region_id' => '6', 'country_id' => 'US', 'code' => 'AE', 'default_name' => 'Armed Forces Africa'),
            array('region_id' => '7', 'country_id' => 'US', 'code' => 'AA', 'default_name' => 'Armed Forces Americas'),
            array('region_id' => '8', 'country_id' => 'US', 'code' => 'AE', 'default_name' => 'Armed Forces Canada'),
            array('region_id' => '9', 'country_id' => 'US', 'code' => 'AE', 'default_name' => 'Armed Forces Europe'),
            array('region_id' => '10', 'country_id' => 'US', 'code' => 'AE', 'default_name' => 'Armed Forces Middle East'),
            array('region_id' => '11', 'country_id' => 'US', 'code' => 'AP', 'default_name' => 'Armed Forces Pacific'),
            array('region_id' => '12', 'country_id' => 'US', 'code' => 'CA', 'default_name' => 'California'),
            array('region_id' => '13', 'country_id' => 'US', 'code' => 'CO', 'default_name' => 'Colorado'),
            array('region_id' => '14', 'country_id' => 'US', 'code' => 'CT', 'default_name' => 'Connecticut'),
            array('region_id' => '15', 'country_id' => 'US', 'code' => 'DE', 'default_name' => 'Delaware'),
            array('region_id' => '16', 'country_id' => 'US', 'code' => 'DC', 'default_name' => 'District of Columbia'),
            array('region_id' => '17', 'country_id' => 'US', 'code' => 'FM', 'default_name' => 'Federated States Of Micronesia'),
            array('region_id' => '18', 'country_id' => 'US', 'code' => 'FL', 'default_name' => 'Florida'),
            array('region_id' => '19', 'country_id' => 'US', 'code' => 'GA', 'default_name' => 'Georgia'),
            array('region_id' => '20', 'country_id' => 'US', 'code' => 'GU', 'default_name' => 'Guam'),
            array('region_id' => '21', 'country_id' => 'US', 'code' => 'HI', 'default_name' => 'Hawaii'),
            array('region_id' => '22', 'country_id' => 'US', 'code' => 'ID', 'default_name' => 'Idaho'),
            array('region_id' => '23', 'country_id' => 'US', 'code' => 'IL', 'default_name' => 'Illinois'),
            array('region_id' => '24', 'country_id' => 'US', 'code' => 'IN', 'default_name' => 'Indiana'),
            array('region_id' => '25', 'country_id' => 'US', 'code' => 'IA', 'default_name' => 'Iowa'),
            array('region_id' => '26', 'country_id' => 'US', 'code' => 'KS', 'default_name' => 'Kansas'),
            array('region_id' => '27', 'country_id' => 'US', 'code' => 'KY', 'default_name' => 'Kentucky'),
            array('region_id' => '28', 'country_id' => 'US', 'code' => 'LA', 'default_name' => 'Louisiana'),
            array('region_id' => '29', 'country_id' => 'US', 'code' => 'ME', 'default_name' => 'Maine'),
            array('region_id' => '30', 'country_id' => 'US', 'code' => 'MH', 'default_name' => 'Marshall Islands'),
            array('region_id' => '31', 'country_id' => 'US', 'code' => 'MD', 'default_name' => 'Maryland'),
            array('region_id' => '32', 'country_id' => 'US', 'code' => 'MA', 'default_name' => 'Massachusetts'),
            array('region_id' => '33', 'country_id' => 'US', 'code' => 'MI', 'default_name' => 'Michigan'),
            array('region_id' => '34', 'country_id' => 'US', 'code' => 'MN', 'default_name' => 'Minnesota'),
            array('region_id' => '35', 'country_id' => 'US', 'code' => 'MS', 'default_name' => 'Mississippi'),
            array('region_id' => '36', 'country_id' => 'US', 'code' => 'MO', 'default_name' => 'Missouri'),
            array('region_id' => '37', 'country_id' => 'US', 'code' => 'MT', 'default_name' => 'Montana'),
            array('region_id' => '38', 'country_id' => 'US', 'code' => 'NE', 'default_name' => 'Nebraska'),
            array('region_id' => '39', 'country_id' => 'US', 'code' => 'NV', 'default_name' => 'Nevada'),
            array('region_id' => '40', 'country_id' => 'US', 'code' => 'NH', 'default_name' => 'New Hampshire'),
            array('region_id' => '41', 'country_id' => 'US', 'code' => 'NJ', 'default_name' => 'New Jersey'),
            array('region_id' => '42', 'country_id' => 'US', 'code' => 'NM', 'default_name' => 'New Mexico'),
            array('region_id' => '43', 'country_id' => 'US', 'code' => 'NY', 'default_name' => 'New York'),
            array('region_id' => '44', 'country_id' => 'US', 'code' => 'NC', 'default_name' => 'North Carolina'),
            array('region_id' => '45', 'country_id' => 'US', 'code' => 'ND', 'default_name' => 'North Dakota'),
            array('region_id' => '46', 'country_id' => 'US', 'code' => 'MP', 'default_name' => 'Northern Mariana Islands'),
            array('region_id' => '47', 'country_id' => 'US', 'code' => 'OH', 'default_name' => 'Ohio'),
            array('region_id' => '48', 'country_id' => 'US', 'code' => 'OK', 'default_name' => 'Oklahoma'),
            array('region_id' => '49', 'country_id' => 'US', 'code' => 'OR', 'default_name' => 'Oregon'),
            array('region_id' => '50', 'country_id' => 'US', 'code' => 'PW', 'default_name' => 'Palau'),
            array('region_id' => '51', 'country_id' => 'US', 'code' => 'PA', 'default_name' => 'Pennsylvania'),
            array('region_id' => '52', 'country_id' => 'US', 'code' => 'PR', 'default_name' => 'Puerto Rico'),
            array('region_id' => '53', 'country_id' => 'US', 'code' => 'RI', 'default_name' => 'Rhode Island'),
            array('region_id' => '54', 'country_id' => 'US', 'code' => 'SC', 'default_name' => 'South Carolina'),
            array('region_id' => '55', 'country_id' => 'US', 'code' => 'SD', 'default_name' => 'South Dakota'),
            array('region_id' => '56', 'country_id' => 'US', 'code' => 'TN', 'default_name' => 'Tennessee'),
            array('region_id' => '57', 'country_id' => 'US', 'code' => 'TX', 'default_name' => 'Texas'),
            array('region_id' => '58', 'country_id' => 'US', 'code' => 'UT', 'default_name' => 'Utah'),
            array('region_id' => '59', 'country_id' => 'US', 'code' => 'VT', 'default_name' => 'Vermont'),
            array('region_id' => '60', 'country_id' => 'US', 'code' => 'VI', 'default_name' => 'Virgin Islands'),
            array('region_id' => '61', 'country_id' => 'US', 'code' => 'VA', 'default_name' => 'Virginia'),
            array('region_id' => '62', 'country_id' => 'US', 'code' => 'WA', 'default_name' => 'Washington'),
            array('region_id' => '63', 'country_id' => 'US', 'code' => 'WV', 'default_name' => 'West Virginia'),
            array('region_id' => '64', 'country_id' => 'US', 'code' => 'WI', 'default_name' => 'Wisconsin'),
            array('region_id' => '65', 'country_id' => 'US', 'code' => 'WY', 'default_name' => 'Wyoming'),
            array('region_id' => '66', 'country_id' => 'CA', 'code' => 'AB', 'default_name' => 'Alberta'),
            array('region_id' => '67', 'country_id' => 'CA', 'code' => 'BC', 'default_name' => 'British Columbia'),
            array('region_id' => '68', 'country_id' => 'CA', 'code' => 'MB', 'default_name' => 'Manitoba'),
            array('region_id' => '69', 'country_id' => 'CA', 'code' => 'NL', 'default_name' => 'Newfoundland and Labrador'),
            array('region_id' => '70', 'country_id' => 'CA', 'code' => 'NB', 'default_name' => 'New Brunswick'),
            array('region_id' => '71', 'country_id' => 'CA', 'code' => 'NS', 'default_name' => 'Nova Scotia'),
            array('region_id' => '72', 'country_id' => 'CA', 'code' => 'NT', 'default_name' => 'Northwest Territories'),
            array('region_id' => '73', 'country_id' => 'CA', 'code' => 'NU', 'default_name' => 'Nunavut'),
            array('region_id' => '74', 'country_id' => 'CA', 'code' => 'ON', 'default_name' => 'Ontario'),
            array('region_id' => '75', 'country_id' => 'CA', 'code' => 'PE', 'default_name' => 'Prince Edward Island'),
            array('region_id' => '76', 'country_id' => 'CA', 'code' => 'QC', 'default_name' => 'Quebec'),
            array('region_id' => '77', 'country_id' => 'CA', 'code' => 'SK', 'default_name' => 'Saskatchewan'),
            array('region_id' => '78', 'country_id' => 'CA', 'code' => 'YT', 'default_name' => 'Yukon Territory'),
            array('region_id' => '79', 'country_id' => 'DE', 'code' => 'NDS', 'default_name' => 'Niedersachsen'),
            array('region_id' => '80', 'country_id' => 'DE', 'code' => 'BAW', 'default_name' => 'Baden-Württemberg'),
            array('region_id' => '81', 'country_id' => 'DE', 'code' => 'BAY', 'default_name' => 'Bayern'),
            array('region_id' => '82', 'country_id' => 'DE', 'code' => 'BER', 'default_name' => 'Berlin'),
            array('region_id' => '83', 'country_id' => 'DE', 'code' => 'BRG', 'default_name' => 'Brandenburg'),
            array('region_id' => '84', 'country_id' => 'DE', 'code' => 'BRE', 'default_name' => 'Bremen'),
            array('region_id' => '85', 'country_id' => 'DE', 'code' => 'HAM', 'default_name' => 'Hamburg'),
            array('region_id' => '86', 'country_id' => 'DE', 'code' => 'HES', 'default_name' => 'Hessen'),
            array('region_id' => '87', 'country_id' => 'DE', 'code' => 'MEC', 'default_name' => 'Mecklenburg-Vorpommern'),
            array('region_id' => '88', 'country_id' => 'DE', 'code' => 'NRW', 'default_name' => 'Nordrhein-Westfalen'),
            array('region_id' => '89', 'country_id' => 'DE', 'code' => 'RHE', 'default_name' => 'Rheinland-Pfalz'),
            array('region_id' => '90', 'country_id' => 'DE', 'code' => 'SAR', 'default_name' => 'Saarland'),
            array('region_id' => '91', 'country_id' => 'DE', 'code' => 'SAS', 'default_name' => 'Sachsen'),
            array('region_id' => '92', 'country_id' => 'DE', 'code' => 'SAC', 'default_name' => 'Sachsen-Anhalt'),
            array('region_id' => '93', 'country_id' => 'DE', 'code' => 'SCN', 'default_name' => 'Schleswig-Holstein'),
            array('region_id' => '94', 'country_id' => 'DE', 'code' => 'THE', 'default_name' => 'Thüringen'),
            array('region_id' => '95', 'country_id' => 'AT', 'code' => 'WI', 'default_name' => 'Wien'),
            array('region_id' => '96', 'country_id' => 'AT', 'code' => 'NO', 'default_name' => 'Niederösterreich'),
            array('region_id' => '97', 'country_id' => 'AT', 'code' => 'OO', 'default_name' => 'Oberösterreich'),
            array('region_id' => '98', 'country_id' => 'AT', 'code' => 'SB', 'default_name' => 'Salzburg'),
            array('region_id' => '99', 'country_id' => 'AT', 'code' => 'KN', 'default_name' => 'Kärnten'),
            array('region_id' => '100', 'country_id' => 'AT', 'code' => 'ST', 'default_name' => 'Steiermark'),
            array('region_id' => '101', 'country_id' => 'AT', 'code' => 'TI', 'default_name' => 'Tirol'),
            array('region_id' => '102', 'country_id' => 'AT', 'code' => 'BL', 'default_name' => 'Burgenland'),
            array('region_id' => '103', 'country_id' => 'AT', 'code' => 'VB', 'default_name' => 'Vorarlberg'),
            array('region_id' => '104', 'country_id' => 'CH', 'code' => 'AG', 'default_name' => 'Aargau'),
            array('region_id' => '105', 'country_id' => 'CH', 'code' => 'AI', 'default_name' => 'Appenzell Innerrhoden'),
            array('region_id' => '106', 'country_id' => 'CH', 'code' => 'AR', 'default_name' => 'Appenzell Ausserrhoden'),
            array('region_id' => '107', 'country_id' => 'CH', 'code' => 'BE', 'default_name' => 'Bern'),
            array('region_id' => '108', 'country_id' => 'CH', 'code' => 'BL', 'default_name' => 'Basel-Landschaft'),
            array('region_id' => '109', 'country_id' => 'CH', 'code' => 'BS', 'default_name' => 'Basel-Stadt'),
            array('region_id' => '110', 'country_id' => 'CH', 'code' => 'FR', 'default_name' => 'Freiburg'),
            array('region_id' => '111', 'country_id' => 'CH', 'code' => 'GE', 'default_name' => 'Genf'),
            array('region_id' => '112', 'country_id' => 'CH', 'code' => 'GL', 'default_name' => 'Glarus'),
            array('region_id' => '113', 'country_id' => 'CH', 'code' => 'GR', 'default_name' => 'Graubünden'),
            array('region_id' => '114', 'country_id' => 'CH', 'code' => 'JU', 'default_name' => 'Jura'),
            array('region_id' => '115', 'country_id' => 'CH', 'code' => 'LU', 'default_name' => 'Luzern'),
            array('region_id' => '116', 'country_id' => 'CH', 'code' => 'NE', 'default_name' => 'Neuenburg'),
            array('region_id' => '117', 'country_id' => 'CH', 'code' => 'NW', 'default_name' => 'Nidwalden'),
            array('region_id' => '118', 'country_id' => 'CH', 'code' => 'OW', 'default_name' => 'Obwalden'),
            array('region_id' => '119', 'country_id' => 'CH', 'code' => 'SG', 'default_name' => 'St. Gallen'),
            array('region_id' => '120', 'country_id' => 'CH', 'code' => 'SH', 'default_name' => 'Schaffhausen'),
            array('region_id' => '121', 'country_id' => 'CH', 'code' => 'SO', 'default_name' => 'Solothurn'),
            array('region_id' => '122', 'country_id' => 'CH', 'code' => 'SZ', 'default_name' => 'Schwyz'),
            array('region_id' => '123', 'country_id' => 'CH', 'code' => 'TG', 'default_name' => 'Thurgau'),
            array('region_id' => '124', 'country_id' => 'CH', 'code' => 'TI', 'default_name' => 'Tessin'),
            array('region_id' => '125', 'country_id' => 'CH', 'code' => 'UR', 'default_name' => 'Uri'),
            array('region_id' => '126', 'country_id' => 'CH', 'code' => 'VD', 'default_name' => 'Waadt'),
            array('region_id' => '127', 'country_id' => 'CH', 'code' => 'VS', 'default_name' => 'Wallis'),
            array('region_id' => '128', 'country_id' => 'CH', 'code' => 'ZG', 'default_name' => 'Zug'),
            array('region_id' => '129', 'country_id' => 'CH', 'code' => 'ZH', 'default_name' => 'Zürich'),
            array('region_id' => '130', 'country_id' => 'ES', 'code' => 'A Coru?a', 'default_name' => 'A Coruña'),
            array('region_id' => '131', 'country_id' => 'ES', 'code' => 'Alava', 'default_name' => 'Alava'),
            array('region_id' => '132', 'country_id' => 'ES', 'code' => 'Albacete', 'default_name' => 'Albacete'),
            array('region_id' => '133', 'country_id' => 'ES', 'code' => 'Alicante', 'default_name' => 'Alicante'),
            array('region_id' => '134', 'country_id' => 'ES', 'code' => 'Almeria', 'default_name' => 'Almeria'),
            array('region_id' => '135', 'country_id' => 'ES', 'code' => 'Asturias', 'default_name' => 'Asturias'),
            array('region_id' => '136', 'country_id' => 'ES', 'code' => 'Avila', 'default_name' => 'Avila'),
            array('region_id' => '137', 'country_id' => 'ES', 'code' => 'Badajoz', 'default_name' => 'Badajoz'),
            array('region_id' => '138', 'country_id' => 'ES', 'code' => 'Baleares', 'default_name' => 'Baleares'),
            array('region_id' => '139', 'country_id' => 'ES', 'code' => 'Barcelona', 'default_name' => 'Barcelona'),
            array('region_id' => '140', 'country_id' => 'ES', 'code' => 'Burgos', 'default_name' => 'Burgos'),
            array('region_id' => '141', 'country_id' => 'ES', 'code' => 'Caceres', 'default_name' => 'Caceres'),
            array('region_id' => '142', 'country_id' => 'ES', 'code' => 'Cadiz', 'default_name' => 'Cadiz'),
            array('region_id' => '143', 'country_id' => 'ES', 'code' => 'Cantabria', 'default_name' => 'Cantabria'),
            array('region_id' => '144', 'country_id' => 'ES', 'code' => 'Castellon', 'default_name' => 'Castellon'),
            array('region_id' => '145', 'country_id' => 'ES', 'code' => 'Ceuta', 'default_name' => 'Ceuta'),
            array('region_id' => '146', 'country_id' => 'ES', 'code' => 'Ciudad Real', 'default_name' => 'Ciudad Real'),
            array('region_id' => '147', 'country_id' => 'ES', 'code' => 'Cordoba', 'default_name' => 'Cordoba'),
            array('region_id' => '148', 'country_id' => 'ES', 'code' => 'Cuenca', 'default_name' => 'Cuenca'),
            array('region_id' => '149', 'country_id' => 'ES', 'code' => 'Girona', 'default_name' => 'Girona'),
            array('region_id' => '150', 'country_id' => 'ES', 'code' => 'Granada', 'default_name' => 'Granada'),
            array('region_id' => '151', 'country_id' => 'ES', 'code' => 'Guadalajara', 'default_name' => 'Guadalajara'),
            array('region_id' => '152', 'country_id' => 'ES', 'code' => 'Guipuzcoa', 'default_name' => 'Guipuzcoa'),
            array('region_id' => '153', 'country_id' => 'ES', 'code' => 'Huelva', 'default_name' => 'Huelva'),
            array('region_id' => '154', 'country_id' => 'ES', 'code' => 'Huesca', 'default_name' => 'Huesca'),
            array('region_id' => '155', 'country_id' => 'ES', 'code' => 'Jaen', 'default_name' => 'Jaen'),
            array('region_id' => '156', 'country_id' => 'ES', 'code' => 'La Rioja', 'default_name' => 'La Rioja'),
            array('region_id' => '157', 'country_id' => 'ES', 'code' => 'Las Palmas', 'default_name' => 'Las Palmas'),
            array('region_id' => '158', 'country_id' => 'ES', 'code' => 'Leon', 'default_name' => 'Leon'),
            array('region_id' => '159', 'country_id' => 'ES', 'code' => 'Lleida', 'default_name' => 'Lleida'),
            array('region_id' => '160', 'country_id' => 'ES', 'code' => 'Lugo', 'default_name' => 'Lugo'),
            array('region_id' => '161', 'country_id' => 'ES', 'code' => 'Madrid', 'default_name' => 'Madrid'),
            array('region_id' => '162', 'country_id' => 'ES', 'code' => 'Malaga', 'default_name' => 'Malaga'),
            array('region_id' => '163', 'country_id' => 'ES', 'code' => 'Melilla', 'default_name' => 'Melilla'),
            array('region_id' => '164', 'country_id' => 'ES', 'code' => 'Murcia', 'default_name' => 'Murcia'),
            array('region_id' => '165', 'country_id' => 'ES', 'code' => 'Navarra', 'default_name' => 'Navarra'),
            array('region_id' => '166', 'country_id' => 'ES', 'code' => 'Ourense', 'default_name' => 'Ourense'),
            array('region_id' => '167', 'country_id' => 'ES', 'code' => 'Palencia', 'default_name' => 'Palencia'),
            array('region_id' => '168', 'country_id' => 'ES', 'code' => 'Pontevedra', 'default_name' => 'Pontevedra'),
            array('region_id' => '169', 'country_id' => 'ES', 'code' => 'Salamanca', 'default_name' => 'Salamanca'),
            array('region_id' => '170', 'country_id' => 'ES', 'code' => 'Santa Cruz de Tenerife', 'default_name' => 'Santa Cruz de Tenerife'),
            array('region_id' => '171', 'country_id' => 'ES', 'code' => 'Segovia', 'default_name' => 'Segovia'),
            array('region_id' => '172', 'country_id' => 'ES', 'code' => 'Sevilla', 'default_name' => 'Sevilla'),
            array('region_id' => '173', 'country_id' => 'ES', 'code' => 'Soria', 'default_name' => 'Soria'),
            array('region_id' => '174', 'country_id' => 'ES', 'code' => 'Tarragona', 'default_name' => 'Tarragona'),
            array('region_id' => '175', 'country_id' => 'ES', 'code' => 'Teruel', 'default_name' => 'Teruel'),
            array('region_id' => '176', 'country_id' => 'ES', 'code' => 'Toledo', 'default_name' => 'Toledo'),
            array('region_id' => '177', 'country_id' => 'ES', 'code' => 'Valencia', 'default_name' => 'Valencia'),
            array('region_id' => '178', 'country_id' => 'ES', 'code' => 'Valladolid', 'default_name' => 'Valladolid'),
            array('region_id' => '179', 'country_id' => 'ES', 'code' => 'Vizcaya', 'default_name' => 'Vizcaya'),
            array('region_id' => '180', 'country_id' => 'ES', 'code' => 'Zamora', 'default_name' => 'Zamora'),
            array('region_id' => '181', 'country_id' => 'ES', 'code' => 'Zaragoza', 'default_name' => 'Zaragoza'),
            array('region_id' => '182', 'country_id' => 'FR', 'code' => '1', 'default_name' => 'Ain'),
            array('region_id' => '183', 'country_id' => 'FR', 'code' => '2', 'default_name' => 'Aisne'),
            array('region_id' => '184', 'country_id' => 'FR', 'code' => '3', 'default_name' => 'Allier'),
            array('region_id' => '185', 'country_id' => 'FR', 'code' => '4', 'default_name' => 'Alpes-de-Haute-Provence'),
            array('region_id' => '186', 'country_id' => 'FR', 'code' => '5', 'default_name' => 'Hautes-Alpes'),
            array('region_id' => '187', 'country_id' => 'FR', 'code' => '6', 'default_name' => 'Alpes-Maritimes'),
            array('region_id' => '188', 'country_id' => 'FR', 'code' => '7', 'default_name' => 'Ardèche'),
            array('region_id' => '189', 'country_id' => 'FR', 'code' => '8', 'default_name' => 'Ardennes'),
            array('region_id' => '190', 'country_id' => 'FR', 'code' => '9', 'default_name' => 'Ariège'),
            array('region_id' => '191', 'country_id' => 'FR', 'code' => '10', 'default_name' => 'Aube'),
            array('region_id' => '192', 'country_id' => 'FR', 'code' => '11', 'default_name' => 'Aude'),
            array('region_id' => '193', 'country_id' => 'FR', 'code' => '12', 'default_name' => 'Aveyron'),
            array('region_id' => '194', 'country_id' => 'FR', 'code' => '13', 'default_name' => 'Bouches-du-Rhône'),
            array('region_id' => '195', 'country_id' => 'FR', 'code' => '14', 'default_name' => 'Calvados'),
            array('region_id' => '196', 'country_id' => 'FR', 'code' => '15', 'default_name' => 'Cantal'),
            array('region_id' => '197', 'country_id' => 'FR', 'code' => '16', 'default_name' => 'Charente'),
            array('region_id' => '198', 'country_id' => 'FR', 'code' => '17', 'default_name' => 'Charente-Maritime'),
            array('region_id' => '199', 'country_id' => 'FR', 'code' => '18', 'default_name' => 'Cher'),
            array('region_id' => '200', 'country_id' => 'FR', 'code' => '19', 'default_name' => 'Corrèze'),
            array('region_id' => '201', 'country_id' => 'FR', 'code' => '2A', 'default_name' => 'Corse-du-Sud'),
            array('region_id' => '202', 'country_id' => 'FR', 'code' => '2B', 'default_name' => 'Haute-Corse'),
            array('region_id' => '203', 'country_id' => 'FR', 'code' => '21', 'default_name' => 'Côte-d\'Or'),
            array('region_id' => '204', 'country_id' => 'FR', 'code' => '22', 'default_name' => 'Côtes-d\'Armor'),
            array('region_id' => '205', 'country_id' => 'FR', 'code' => '23', 'default_name' => 'Creuse'),
            array('region_id' => '206', 'country_id' => 'FR', 'code' => '24', 'default_name' => 'Dordogne'),
            array('region_id' => '207', 'country_id' => 'FR', 'code' => '25', 'default_name' => 'Doubs'),
            array('region_id' => '208', 'country_id' => 'FR', 'code' => '26', 'default_name' => 'Drôme'),
            array('region_id' => '209', 'country_id' => 'FR', 'code' => '27', 'default_name' => 'Eure'),
            array('region_id' => '210', 'country_id' => 'FR', 'code' => '28', 'default_name' => 'Eure-et-Loir'),
            array('region_id' => '211', 'country_id' => 'FR', 'code' => '29', 'default_name' => 'Finistère'),
            array('region_id' => '212', 'country_id' => 'FR', 'code' => '30', 'default_name' => 'Gard'),
            array('region_id' => '213', 'country_id' => 'FR', 'code' => '31', 'default_name' => 'Haute-Garonne'),
            array('region_id' => '214', 'country_id' => 'FR', 'code' => '32', 'default_name' => 'Gers'),
            array('region_id' => '215', 'country_id' => 'FR', 'code' => '33', 'default_name' => 'Gironde'),
            array('region_id' => '216', 'country_id' => 'FR', 'code' => '34', 'default_name' => 'Hérault'),
            array('region_id' => '217', 'country_id' => 'FR', 'code' => '35', 'default_name' => 'Ille-et-Vilaine'),
            array('region_id' => '218', 'country_id' => 'FR', 'code' => '36', 'default_name' => 'Indre'),
            array('region_id' => '219', 'country_id' => 'FR', 'code' => '37', 'default_name' => 'Indre-et-Loire'),
            array('region_id' => '220', 'country_id' => 'FR', 'code' => '38', 'default_name' => 'Isère'),
            array('region_id' => '221', 'country_id' => 'FR', 'code' => '39', 'default_name' => 'Jura'),
            array('region_id' => '222', 'country_id' => 'FR', 'code' => '40', 'default_name' => 'Landes'),
            array('region_id' => '223', 'country_id' => 'FR', 'code' => '41', 'default_name' => 'Loir-et-Cher'),
            array('region_id' => '224', 'country_id' => 'FR', 'code' => '42', 'default_name' => 'Loire'),
            array('region_id' => '225', 'country_id' => 'FR', 'code' => '43', 'default_name' => 'Haute-Loire'),
            array('region_id' => '226', 'country_id' => 'FR', 'code' => '44', 'default_name' => 'Loire-Atlantique'),
            array('region_id' => '227', 'country_id' => 'FR', 'code' => '45', 'default_name' => 'Loiret'),
            array('region_id' => '228', 'country_id' => 'FR', 'code' => '46', 'default_name' => 'Lot'),
            array('region_id' => '229', 'country_id' => 'FR', 'code' => '47', 'default_name' => 'Lot-et-Garonne'),
            array('region_id' => '230', 'country_id' => 'FR', 'code' => '48', 'default_name' => 'Lozère'),
            array('region_id' => '231', 'country_id' => 'FR', 'code' => '49', 'default_name' => 'Maine-et-Loire'),
            array('region_id' => '232', 'country_id' => 'FR', 'code' => '50', 'default_name' => 'Manche'),
            array('region_id' => '233', 'country_id' => 'FR', 'code' => '51', 'default_name' => 'Marne'),
            array('region_id' => '234', 'country_id' => 'FR', 'code' => '52', 'default_name' => 'Haute-Marne'),
            array('region_id' => '235', 'country_id' => 'FR', 'code' => '53', 'default_name' => 'Mayenne'),
            array('region_id' => '236', 'country_id' => 'FR', 'code' => '54', 'default_name' => 'Meurthe-et-Moselle'),
            array('region_id' => '237', 'country_id' => 'FR', 'code' => '55', 'default_name' => 'Meuse'),
            array('region_id' => '238', 'country_id' => 'FR', 'code' => '56', 'default_name' => 'Morbihan'),
            array('region_id' => '239', 'country_id' => 'FR', 'code' => '57', 'default_name' => 'Moselle'),
            array('region_id' => '240', 'country_id' => 'FR', 'code' => '58', 'default_name' => 'Nièvre'),
            array('region_id' => '241', 'country_id' => 'FR', 'code' => '59', 'default_name' => 'Nord'),
            array('region_id' => '242', 'country_id' => 'FR', 'code' => '60', 'default_name' => 'Oise'),
            array('region_id' => '243', 'country_id' => 'FR', 'code' => '61', 'default_name' => 'Orne'),
            array('region_id' => '244', 'country_id' => 'FR', 'code' => '62', 'default_name' => 'Pas-de-Calais'),
            array('region_id' => '245', 'country_id' => 'FR', 'code' => '63', 'default_name' => 'Puy-de-Dôme'),
            array('region_id' => '246', 'country_id' => 'FR', 'code' => '64', 'default_name' => 'Pyrénées-Atlantiques'),
            array('region_id' => '247', 'country_id' => 'FR', 'code' => '65', 'default_name' => 'Hautes-Pyrénées'),
            array('region_id' => '248', 'country_id' => 'FR', 'code' => '66', 'default_name' => 'Pyrénées-Orientales'),
            array('region_id' => '249', 'country_id' => 'FR', 'code' => '67', 'default_name' => 'Bas-Rhin'),
            array('region_id' => '250', 'country_id' => 'FR', 'code' => '68', 'default_name' => 'Haut-Rhin'),
            array('region_id' => '251', 'country_id' => 'FR', 'code' => '69', 'default_name' => 'Rhône'),
            array('region_id' => '252', 'country_id' => 'FR', 'code' => '70', 'default_name' => 'Haute-Saône'),
            array('region_id' => '253', 'country_id' => 'FR', 'code' => '71', 'default_name' => 'Saône-et-Loire'),
            array('region_id' => '254', 'country_id' => 'FR', 'code' => '72', 'default_name' => 'Sarthe'),
            array('region_id' => '255', 'country_id' => 'FR', 'code' => '73', 'default_name' => 'Savoie'),
            array('region_id' => '256', 'country_id' => 'FR', 'code' => '74', 'default_name' => 'Haute-Savoie'),
            array('region_id' => '257', 'country_id' => 'FR', 'code' => '75', 'default_name' => 'Paris'),
            array('region_id' => '258', 'country_id' => 'FR', 'code' => '76', 'default_name' => 'Seine-Maritime'),
            array('region_id' => '259', 'country_id' => 'FR', 'code' => '77', 'default_name' => 'Seine-et-Marne'),
            array('region_id' => '260', 'country_id' => 'FR', 'code' => '78', 'default_name' => 'Yvelines'),
            array('region_id' => '261', 'country_id' => 'FR', 'code' => '79', 'default_name' => 'Deux-Sèvres'),
            array('region_id' => '262', 'country_id' => 'FR', 'code' => '80', 'default_name' => 'Somme'),
            array('region_id' => '263', 'country_id' => 'FR', 'code' => '81', 'default_name' => 'Tarn'),
            array('region_id' => '264', 'country_id' => 'FR', 'code' => '82', 'default_name' => 'Tarn-et-Garonne'),
            array('region_id' => '265', 'country_id' => 'FR', 'code' => '83', 'default_name' => 'Var'),
            array('region_id' => '266', 'country_id' => 'FR', 'code' => '84', 'default_name' => 'Vaucluse'),
            array('region_id' => '267', 'country_id' => 'FR', 'code' => '85', 'default_name' => 'Vendée'),
            array('region_id' => '268', 'country_id' => 'FR', 'code' => '86', 'default_name' => 'Vienne'),
            array('region_id' => '269', 'country_id' => 'FR', 'code' => '87', 'default_name' => 'Haute-Vienne'),
            array('region_id' => '270', 'country_id' => 'FR', 'code' => '88', 'default_name' => 'Vosges'),
            array('region_id' => '271', 'country_id' => 'FR', 'code' => '89', 'default_name' => 'Yonne'),
            array('region_id' => '272', 'country_id' => 'FR', 'code' => '90', 'default_name' => 'Territoire-de-Belfort'),
            array('region_id' => '273', 'country_id' => 'FR', 'code' => '91', 'default_name' => 'Essonne'),
            array('region_id' => '274', 'country_id' => 'FR', 'code' => '92', 'default_name' => 'Hauts-de-Seine'),
            array('region_id' => '275', 'country_id' => 'FR', 'code' => '93', 'default_name' => 'Seine-Saint-Denis'),
            array('region_id' => '276', 'country_id' => 'FR', 'code' => '94', 'default_name' => 'Val-de-Marne'),
            array('region_id' => '277', 'country_id' => 'FR', 'code' => '95', 'default_name' => 'Val-d\'Oise'),
            array('region_id' => '278', 'country_id' => 'RO', 'code' => 'AB', 'default_name' => 'Alba'),
            array('region_id' => '279', 'country_id' => 'RO', 'code' => 'AR', 'default_name' => 'Arad'),
            array('region_id' => '280', 'country_id' => 'RO', 'code' => 'AG', 'default_name' => 'Arge?'),
            array('region_id' => '281', 'country_id' => 'RO', 'code' => 'BC', 'default_name' => 'Bac?u'),
            array('region_id' => '282', 'country_id' => 'RO', 'code' => 'BH', 'default_name' => 'Bihor'),
            array('region_id' => '283', 'country_id' => 'RO', 'code' => 'BN', 'default_name' => 'Bistri?a-N?s?ud'),
            array('region_id' => '284', 'country_id' => 'RO', 'code' => 'BT', 'default_name' => 'Boto?ani'),
            array('region_id' => '285', 'country_id' => 'RO', 'code' => 'BV', 'default_name' => 'Bra?ov'),
            array('region_id' => '286', 'country_id' => 'RO', 'code' => 'BR', 'default_name' => 'Br?ila'),
            array('region_id' => '287', 'country_id' => 'RO', 'code' => 'B', 'default_name' => 'Bucure?ti'),
            array('region_id' => '288', 'country_id' => 'RO', 'code' => 'BZ', 'default_name' => 'Buz?u'),
            array('region_id' => '289', 'country_id' => 'RO', 'code' => 'CS', 'default_name' => 'Cara?-Severin'),
            array('region_id' => '290', 'country_id' => 'RO', 'code' => 'CL', 'default_name' => 'C?l?ra?i'),
            array('region_id' => '291', 'country_id' => 'RO', 'code' => 'CJ', 'default_name' => 'Cluj'),
            array('region_id' => '292', 'country_id' => 'RO', 'code' => 'CT', 'default_name' => 'Constan?a'),
            array('region_id' => '293', 'country_id' => 'RO', 'code' => 'CV', 'default_name' => 'Covasna'),
            array('region_id' => '294', 'country_id' => 'RO', 'code' => 'DB', 'default_name' => 'Dâmbovi?a'),
            array('region_id' => '295', 'country_id' => 'RO', 'code' => 'DJ', 'default_name' => 'Dolj'),
            array('region_id' => '296', 'country_id' => 'RO', 'code' => 'GL', 'default_name' => 'Gala?i'),
            array('region_id' => '297', 'country_id' => 'RO', 'code' => 'GR', 'default_name' => 'Giurgiu'),
            array('region_id' => '298', 'country_id' => 'RO', 'code' => 'GJ', 'default_name' => 'Gorj'),
            array('region_id' => '299', 'country_id' => 'RO', 'code' => 'HR', 'default_name' => 'Harghita'),
            array('region_id' => '300', 'country_id' => 'RO', 'code' => 'HD', 'default_name' => 'Hunedoara'),
            array('region_id' => '301', 'country_id' => 'RO', 'code' => 'IL', 'default_name' => 'Ialomi?a'),
            array('region_id' => '302', 'country_id' => 'RO', 'code' => 'IS', 'default_name' => 'Ia?i'),
            array('region_id' => '303', 'country_id' => 'RO', 'code' => 'IF', 'default_name' => 'Ilfov'),
            array('region_id' => '304', 'country_id' => 'RO', 'code' => 'MM', 'default_name' => 'Maramure?'),
            array('region_id' => '305', 'country_id' => 'RO', 'code' => 'MH', 'default_name' => 'Mehedin?i'),
            array('region_id' => '306', 'country_id' => 'RO', 'code' => 'MS', 'default_name' => 'Mure?'),
            array('region_id' => '307', 'country_id' => 'RO', 'code' => 'NT', 'default_name' => 'Neam?'),
            array('region_id' => '308', 'country_id' => 'RO', 'code' => 'OT', 'default_name' => 'Olt'),
            array('region_id' => '309', 'country_id' => 'RO', 'code' => 'PH', 'default_name' => 'Prahova'),
            array('region_id' => '310', 'country_id' => 'RO', 'code' => 'SM', 'default_name' => 'Satu-Mare'),
            array('region_id' => '311', 'country_id' => 'RO', 'code' => 'SJ', 'default_name' => 'S?laj'),
            array('region_id' => '312', 'country_id' => 'RO', 'code' => 'SB', 'default_name' => 'Sibiu'),
            array('region_id' => '313', 'country_id' => 'RO', 'code' => 'SV', 'default_name' => 'Suceava'),
            array('region_id' => '314', 'country_id' => 'RO', 'code' => 'TR', 'default_name' => 'Teleorman'),
            array('region_id' => '315', 'country_id' => 'RO', 'code' => 'TM', 'default_name' => 'Timi?'),
            array('region_id' => '316', 'country_id' => 'RO', 'code' => 'TL', 'default_name' => 'Tulcea'),
            array('region_id' => '317', 'country_id' => 'RO', 'code' => 'VS', 'default_name' => 'Vaslui'),
            array('region_id' => '318', 'country_id' => 'RO', 'code' => 'VL', 'default_name' => 'Vâlcea'),
            array('region_id' => '319', 'country_id' => 'RO', 'code' => 'VN', 'default_name' => 'Vrancea'),
            array('region_id' => '320', 'country_id' => 'FI', 'code' => 'Lappi', 'default_name' => 'Lappi'),
            array('region_id' => '321', 'country_id' => 'FI', 'code' => 'Pohjois-Pohjanmaa', 'default_name' => 'Pohjois-Pohjanmaa'),
            array('region_id' => '322', 'country_id' => 'FI', 'code' => 'Kainuu', 'default_name' => 'Kainuu'),
            array('region_id' => '323', 'country_id' => 'FI', 'code' => 'Pohjois-Karjala', 'default_name' => 'Pohjois-Karjala'),
            array('region_id' => '324', 'country_id' => 'FI', 'code' => 'Pohjois-Savo', 'default_name' => 'Pohjois-Savo'),
            array('region_id' => '325', 'country_id' => 'FI', 'code' => 'Etelä-Savo', 'default_name' => 'Etelä-Savo'),
            array('region_id' => '326', 'country_id' => 'FI', 'code' => 'Etelä-Pohjanmaa', 'default_name' => 'Etelä-Pohjanmaa'),
            array('region_id' => '327', 'country_id' => 'FI', 'code' => 'Pohjanmaa', 'default_name' => 'Pohjanmaa'),
            array('region_id' => '328', 'country_id' => 'FI', 'code' => 'Pirkanmaa', 'default_name' => 'Pirkanmaa'),
            array('region_id' => '329', 'country_id' => 'FI', 'code' => 'Satakunta', 'default_name' => 'Satakunta'),
            array('region_id' => '330', 'country_id' => 'FI', 'code' => 'Keski-Pohjanmaa', 'default_name' => 'Keski-Pohjanmaa'),
            array('region_id' => '331', 'country_id' => 'FI', 'code' => 'Keski-Suomi', 'default_name' => 'Keski-Suomi'),
            array('region_id' => '332', 'country_id' => 'FI', 'code' => 'Varsinais-Suomi', 'default_name' => 'Varsinais-Suomi'),
            array('region_id' => '333', 'country_id' => 'FI', 'code' => 'Etelä-Karjala', 'default_name' => 'Etelä-Karjala'),
            array('region_id' => '334', 'country_id' => 'FI', 'code' => 'Päijät-Häme', 'default_name' => 'Päijät-Häme'),
            array('region_id' => '335', 'country_id' => 'FI', 'code' => 'Kanta-Häme', 'default_name' => 'Kanta-Häme'),
            array('region_id' => '336', 'country_id' => 'FI', 'code' => 'Uusimaa', 'default_name' => 'Uusimaa'),
            array('region_id' => '337', 'country_id' => 'FI', 'code' => 'Itä-Uusimaa', 'default_name' => 'Itä-Uusimaa'),
            array('region_id' => '338', 'country_id' => 'FI', 'code' => 'Kymenlaakso', 'default_name' => 'Kymenlaakso'),
            array('region_id' => '339', 'country_id' => 'FI', 'code' => 'Ahvenanmaa', 'default_name' => 'Ahvenanmaa'),
            array('region_id' => '340', 'country_id' => 'EE', 'code' => 'EE-37', 'default_name' => 'Harjumaa'),
            array('region_id' => '341', 'country_id' => 'EE', 'code' => 'EE-39', 'default_name' => 'Hiiumaa'),
            array('region_id' => '342', 'country_id' => 'EE', 'code' => 'EE-44', 'default_name' => 'Ida-Virumaa'),
            array('region_id' => '343', 'country_id' => 'EE', 'code' => 'EE-49', 'default_name' => 'Jõgevamaa'),
            array('region_id' => '344', 'country_id' => 'EE', 'code' => 'EE-51', 'default_name' => 'Järvamaa'),
            array('region_id' => '345', 'country_id' => 'EE', 'code' => 'EE-57', 'default_name' => 'Läänemaa'),
            array('region_id' => '346', 'country_id' => 'EE', 'code' => 'EE-59', 'default_name' => 'Lääne-Virumaa'),
            array('region_id' => '347', 'country_id' => 'EE', 'code' => 'EE-65', 'default_name' => 'Põlvamaa'),
            array('region_id' => '348', 'country_id' => 'EE', 'code' => 'EE-67', 'default_name' => 'Pärnumaa'),
            array('region_id' => '349', 'country_id' => 'EE', 'code' => 'EE-70', 'default_name' => 'Raplamaa'),
            array('region_id' => '350', 'country_id' => 'EE', 'code' => 'EE-74', 'default_name' => 'Saaremaa'),
            array('region_id' => '351', 'country_id' => 'EE', 'code' => 'EE-78', 'default_name' => 'Tartumaa'),
            array('region_id' => '352', 'country_id' => 'EE', 'code' => 'EE-82', 'default_name' => 'Valgamaa'),
            array('region_id' => '353', 'country_id' => 'EE', 'code' => 'EE-84', 'default_name' => 'Viljandimaa'),
            array('region_id' => '354', 'country_id' => 'EE', 'code' => 'EE-86', 'default_name' => 'Võrumaa'),
            array('region_id' => '355', 'country_id' => 'LV', 'code' => 'LV-DGV', 'default_name' => 'Daugavpils'),
            array('region_id' => '356', 'country_id' => 'LV', 'code' => 'LV-JEL', 'default_name' => 'Jelgava'),
            array('region_id' => '357', 'country_id' => 'LV', 'code' => 'J?kabpils', 'default_name' => 'J?kabpils'),
            array('region_id' => '358', 'country_id' => 'LV', 'code' => 'LV-JUR', 'default_name' => 'J?rmala'),
            array('region_id' => '359', 'country_id' => 'LV', 'code' => 'LV-LPX', 'default_name' => 'Liep?ja'),
            array('region_id' => '360', 'country_id' => 'LV', 'code' => 'LV-LE', 'default_name' => 'Liep?jas novads'),
            array('region_id' => '361', 'country_id' => 'LV', 'code' => 'LV-REZ', 'default_name' => 'R?zekne'),
            array('region_id' => '362', 'country_id' => 'LV', 'code' => 'LV-RIX', 'default_name' => 'R?ga'),
            array('region_id' => '363', 'country_id' => 'LV', 'code' => 'LV-RI', 'default_name' => 'R?gas novads'),
            array('region_id' => '364', 'country_id' => 'LV', 'code' => 'Valmiera', 'default_name' => 'Valmiera'),
            array('region_id' => '365', 'country_id' => 'LV', 'code' => 'LV-VEN', 'default_name' => 'Ventspils'),
            array('region_id' => '366', 'country_id' => 'LV', 'code' => 'Aglonas novads', 'default_name' => 'Aglonas novads'),
            array('region_id' => '367', 'country_id' => 'LV', 'code' => 'LV-AI', 'default_name' => 'Aizkraukles novads'),
            array('region_id' => '368', 'country_id' => 'LV', 'code' => 'Aizputes novads', 'default_name' => 'Aizputes novads'),
            array('region_id' => '369', 'country_id' => 'LV', 'code' => 'Akn?stes novads', 'default_name' => 'Akn?stes novads'),
            array('region_id' => '370', 'country_id' => 'LV', 'code' => 'Alojas novads', 'default_name' => 'Alojas novads'),
            array('region_id' => '371', 'country_id' => 'LV', 'code' => 'Alsungas novads', 'default_name' => 'Alsungas novads'),
            array('region_id' => '372', 'country_id' => 'LV', 'code' => 'LV-AL', 'default_name' => 'Al?ksnes novads'),
            array('region_id' => '373', 'country_id' => 'LV', 'code' => 'Amatas novads', 'default_name' => 'Amatas novads'),
            array('region_id' => '374', 'country_id' => 'LV', 'code' => 'Apes novads', 'default_name' => 'Apes novads'),
            array('region_id' => '375', 'country_id' => 'LV', 'code' => 'Auces novads', 'default_name' => 'Auces novads'),
            array('region_id' => '376', 'country_id' => 'LV', 'code' => 'Bab?tes novads', 'default_name' => 'Bab?tes novads'),
            array('region_id' => '377', 'country_id' => 'LV', 'code' => 'Baldones novads', 'default_name' => 'Baldones novads'),
            array('region_id' => '378', 'country_id' => 'LV', 'code' => 'Baltinavas novads', 'default_name' => 'Baltinavas novads'),
            array('region_id' => '379', 'country_id' => 'LV', 'code' => 'LV-BL', 'default_name' => 'Balvu novads'),
            array('region_id' => '380', 'country_id' => 'LV', 'code' => 'LV-BU', 'default_name' => 'Bauskas novads'),
            array('region_id' => '381', 'country_id' => 'LV', 'code' => 'Bever?nas novads', 'default_name' => 'Bever?nas novads'),
            array('region_id' => '382', 'country_id' => 'LV', 'code' => 'Broc?nu novads', 'default_name' => 'Broc?nu novads'),
            array('region_id' => '383', 'country_id' => 'LV', 'code' => 'Burtnieku novads', 'default_name' => 'Burtnieku novads'),
            array('region_id' => '384', 'country_id' => 'LV', 'code' => 'Carnikavas novads', 'default_name' => 'Carnikavas novads'),
            array('region_id' => '385', 'country_id' => 'LV', 'code' => 'Cesvaines novads', 'default_name' => 'Cesvaines novads'),
            array('region_id' => '386', 'country_id' => 'LV', 'code' => 'Ciblas novads', 'default_name' => 'Ciblas novads'),
            array('region_id' => '387', 'country_id' => 'LV', 'code' => 'LV-CE', 'default_name' => 'C?su novads'),
            array('region_id' => '388', 'country_id' => 'LV', 'code' => 'Dagdas novads', 'default_name' => 'Dagdas novads'),
            array('region_id' => '389', 'country_id' => 'LV', 'code' => 'LV-DA', 'default_name' => 'Daugavpils novads'),
            array('region_id' => '390', 'country_id' => 'LV', 'code' => 'LV-DO', 'default_name' => 'Dobeles novads'),
            array('region_id' => '391', 'country_id' => 'LV', 'code' => 'Dundagas novads', 'default_name' => 'Dundagas novads'),
            array('region_id' => '392', 'country_id' => 'LV', 'code' => 'Durbes novads', 'default_name' => 'Durbes novads'),
            array('region_id' => '393', 'country_id' => 'LV', 'code' => 'Engures novads', 'default_name' => 'Engures novads'),
            array('region_id' => '394', 'country_id' => 'LV', 'code' => 'Garkalnes novads', 'default_name' => 'Garkalnes novads'),
            array('region_id' => '395', 'country_id' => 'LV', 'code' => 'Grobi?as novads', 'default_name' => 'Grobi?as novads'),
            array('region_id' => '396', 'country_id' => 'LV', 'code' => 'LV-GU', 'default_name' => 'Gulbenes novads'),
            array('region_id' => '397', 'country_id' => 'LV', 'code' => 'Iecavas novads', 'default_name' => 'Iecavas novads'),
            array('region_id' => '398', 'country_id' => 'LV', 'code' => 'Ikš?iles novads', 'default_name' => 'Ikš?iles novads'),
            array('region_id' => '399', 'country_id' => 'LV', 'code' => 'Il?kstes novads', 'default_name' => 'Il?kstes novads'),
            array('region_id' => '400', 'country_id' => 'LV', 'code' => 'In?ukalna novads', 'default_name' => 'In?ukalna novads'),
            array('region_id' => '401', 'country_id' => 'LV', 'code' => 'Jaunjelgavas novads', 'default_name' => 'Jaunjelgavas novads'),
            array('region_id' => '402', 'country_id' => 'LV', 'code' => 'Jaunpiebalgas novads', 'default_name' => 'Jaunpiebalgas novads'),
            array('region_id' => '403', 'country_id' => 'LV', 'code' => 'Jaunpils novads', 'default_name' => 'Jaunpils novads'),
            array('region_id' => '404', 'country_id' => 'LV', 'code' => 'LV-JL', 'default_name' => 'Jelgavas novads'),
            array('region_id' => '405', 'country_id' => 'LV', 'code' => 'LV-JK', 'default_name' => 'J?kabpils novads'),
            array('region_id' => '406', 'country_id' => 'LV', 'code' => 'Kandavas novads', 'default_name' => 'Kandavas novads'),
            array('region_id' => '407', 'country_id' => 'LV', 'code' => 'Kokneses novads', 'default_name' => 'Kokneses novads'),
            array('region_id' => '408', 'country_id' => 'LV', 'code' => 'Krimuldas novads', 'default_name' => 'Krimuldas novads'),
            array('region_id' => '409', 'country_id' => 'LV', 'code' => 'Krustpils novads', 'default_name' => 'Krustpils novads'),
            array('region_id' => '410', 'country_id' => 'LV', 'code' => 'LV-KR', 'default_name' => 'Kr?slavas novads'),
            array('region_id' => '411', 'country_id' => 'LV', 'code' => 'LV-KU', 'default_name' => 'Kuld?gas novads'),
            array('region_id' => '412', 'country_id' => 'LV', 'code' => 'K?rsavas novads', 'default_name' => 'K?rsavas novads'),
            array('region_id' => '413', 'country_id' => 'LV', 'code' => 'Lielv?rdes novads', 'default_name' => 'Lielv?rdes novads'),
            array('region_id' => '414', 'country_id' => 'LV', 'code' => 'LV-LM', 'default_name' => 'Limbažu novads'),
            array('region_id' => '415', 'country_id' => 'LV', 'code' => 'Lub?nas novads', 'default_name' => 'Lub?nas novads'),
            array('region_id' => '416', 'country_id' => 'LV', 'code' => 'LV-LU', 'default_name' => 'Ludzas novads'),
            array('region_id' => '417', 'country_id' => 'LV', 'code' => 'L?gatnes novads', 'default_name' => 'L?gatnes novads'),
            array('region_id' => '418', 'country_id' => 'LV', 'code' => 'L?v?nu novads', 'default_name' => 'L?v?nu novads'),
            array('region_id' => '419', 'country_id' => 'LV', 'code' => 'LV-MA', 'default_name' => 'Madonas novads'),
            array('region_id' => '420', 'country_id' => 'LV', 'code' => 'Mazsalacas novads', 'default_name' => 'Mazsalacas novads'),
            array('region_id' => '421', 'country_id' => 'LV', 'code' => 'M?lpils novads', 'default_name' => 'M?lpils novads'),
            array('region_id' => '422', 'country_id' => 'LV', 'code' => 'M?rupes novads', 'default_name' => 'M?rupes novads'),
            array('region_id' => '423', 'country_id' => 'LV', 'code' => 'Naukš?nu novads', 'default_name' => 'Naukš?nu novads'),
            array('region_id' => '424', 'country_id' => 'LV', 'code' => 'Neretas novads', 'default_name' => 'Neretas novads'),
            array('region_id' => '425', 'country_id' => 'LV', 'code' => 'N?cas novads', 'default_name' => 'N?cas novads'),
            array('region_id' => '426', 'country_id' => 'LV', 'code' => 'LV-OG', 'default_name' => 'Ogres novads'),
            array('region_id' => '427', 'country_id' => 'LV', 'code' => 'Olaines novads', 'default_name' => 'Olaines novads'),
            array('region_id' => '428', 'country_id' => 'LV', 'code' => 'Ozolnieku novads', 'default_name' => 'Ozolnieku novads'),
            array('region_id' => '429', 'country_id' => 'LV', 'code' => 'LV-PR', 'default_name' => 'Prei?u novads'),
            array('region_id' => '430', 'country_id' => 'LV', 'code' => 'Priekules novads', 'default_name' => 'Priekules novads'),
            array('region_id' => '431', 'country_id' => 'LV', 'code' => 'Prieku?u novads', 'default_name' => 'Prieku?u novads'),
            array('region_id' => '432', 'country_id' => 'LV', 'code' => 'P?rgaujas novads', 'default_name' => 'P?rgaujas novads'),
            array('region_id' => '433', 'country_id' => 'LV', 'code' => 'P?vilostas novads', 'default_name' => 'P?vilostas novads'),
            array('region_id' => '434', 'country_id' => 'LV', 'code' => 'P?avi?u novads', 'default_name' => 'P?avi?u novads'),
            array('region_id' => '435', 'country_id' => 'LV', 'code' => 'Raunas novads', 'default_name' => 'Raunas novads'),
            array('region_id' => '436', 'country_id' => 'LV', 'code' => 'Riebi?u novads', 'default_name' => 'Riebi?u novads'),
            array('region_id' => '437', 'country_id' => 'LV', 'code' => 'Rojas novads', 'default_name' => 'Rojas novads'),
            array('region_id' => '438', 'country_id' => 'LV', 'code' => 'Ropažu novads', 'default_name' => 'Ropažu novads'),
            array('region_id' => '439', 'country_id' => 'LV', 'code' => 'Rucavas novads', 'default_name' => 'Rucavas novads'),
            array('region_id' => '440', 'country_id' => 'LV', 'code' => 'Rug?ju novads', 'default_name' => 'Rug?ju novads'),
            array('region_id' => '441', 'country_id' => 'LV', 'code' => 'Rund?les novads', 'default_name' => 'Rund?les novads'),
            array('region_id' => '442', 'country_id' => 'LV', 'code' => 'LV-RE', 'default_name' => 'R?zeknes novads'),
            array('region_id' => '443', 'country_id' => 'LV', 'code' => 'R?jienas novads', 'default_name' => 'R?jienas novads'),
            array('region_id' => '444', 'country_id' => 'LV', 'code' => 'Salacgr?vas novads', 'default_name' => 'Salacgr?vas novads'),
            array('region_id' => '445', 'country_id' => 'LV', 'code' => 'Salas novads', 'default_name' => 'Salas novads'),
            array('region_id' => '446', 'country_id' => 'LV', 'code' => 'Salaspils novads', 'default_name' => 'Salaspils novads'),
            array('region_id' => '447', 'country_id' => 'LV', 'code' => 'LV-SA', 'default_name' => 'Saldus novads'),
            array('region_id' => '448', 'country_id' => 'LV', 'code' => 'Saulkrastu novads', 'default_name' => 'Saulkrastu novads'),
            array('region_id' => '449', 'country_id' => 'LV', 'code' => 'Siguldas novads', 'default_name' => 'Siguldas novads'),
            array('region_id' => '450', 'country_id' => 'LV', 'code' => 'Skrundas novads', 'default_name' => 'Skrundas novads'),
            array('region_id' => '451', 'country_id' => 'LV', 'code' => 'Skr?veru novads', 'default_name' => 'Skr?veru novads'),
            array('region_id' => '452', 'country_id' => 'LV', 'code' => 'Smiltenes novads', 'default_name' => 'Smiltenes novads'),
            array('region_id' => '453', 'country_id' => 'LV', 'code' => 'Stopi?u novads', 'default_name' => 'Stopi?u novads'),
            array('region_id' => '454', 'country_id' => 'LV', 'code' => 'Stren?u novads', 'default_name' => 'Stren?u novads'),
            array('region_id' => '455', 'country_id' => 'LV', 'code' => 'S?jas novads', 'default_name' => 'S?jas novads'),
            array('region_id' => '456', 'country_id' => 'LV', 'code' => 'LV-TA', 'default_name' => 'Talsu novads'),
            array('region_id' => '457', 'country_id' => 'LV', 'code' => 'LV-TU', 'default_name' => 'Tukuma novads'),
            array('region_id' => '458', 'country_id' => 'LV', 'code' => 'T?rvetes novads', 'default_name' => 'T?rvetes novads'),
            array('region_id' => '459', 'country_id' => 'LV', 'code' => 'Vai?odes novads', 'default_name' => 'Vai?odes novads'),
            array('region_id' => '460', 'country_id' => 'LV', 'code' => 'LV-VK', 'default_name' => 'Valkas novads'),
            array('region_id' => '461', 'country_id' => 'LV', 'code' => 'LV-VM', 'default_name' => 'Valmieras novads'),
            array('region_id' => '462', 'country_id' => 'LV', 'code' => 'Varak??nu novads', 'default_name' => 'Varak??nu novads'),
            array('region_id' => '463', 'country_id' => 'LV', 'code' => 'Vecpiebalgas novads', 'default_name' => 'Vecpiebalgas novads'),
            array('region_id' => '464', 'country_id' => 'LV', 'code' => 'Vecumnieku novads', 'default_name' => 'Vecumnieku novads'),
            array('region_id' => '465', 'country_id' => 'LV', 'code' => 'LV-VE', 'default_name' => 'Ventspils novads'),
            array('region_id' => '466', 'country_id' => 'LV', 'code' => 'Vies?tes novads', 'default_name' => 'Vies?tes novads'),
            array('region_id' => '467', 'country_id' => 'LV', 'code' => 'Vi?akas novads', 'default_name' => 'Vi?akas novads'),
            array('region_id' => '468', 'country_id' => 'LV', 'code' => 'Vi??nu novads', 'default_name' => 'Vi??nu novads'),
            array('region_id' => '469', 'country_id' => 'LV', 'code' => 'V?rkavas novads', 'default_name' => 'V?rkavas novads'),
            array('region_id' => '470', 'country_id' => 'LV', 'code' => 'Zilupes novads', 'default_name' => 'Zilupes novads'),
            array('region_id' => '471', 'country_id' => 'LV', 'code' => '?dažu novads', 'default_name' => '?dažu novads'),
            array('region_id' => '472', 'country_id' => 'LV', 'code' => '?rg?u novads', 'default_name' => '?rg?u novads'),
            array('region_id' => '473', 'country_id' => 'LV', 'code' => '?eguma novads', 'default_name' => '?eguma novads'),
            array('region_id' => '474', 'country_id' => 'LV', 'code' => '?ekavas novads', 'default_name' => '?ekavas novads'),
            array('region_id' => '475', 'country_id' => 'LT', 'code' => 'LT-AL', 'default_name' => 'Alytaus Apskritis'),
            array('region_id' => '476', 'country_id' => 'LT', 'code' => 'LT-KU', 'default_name' => 'Kauno Apskritis'),
            array('region_id' => '477', 'country_id' => 'LT', 'code' => 'LT-KL', 'default_name' => 'Klaip?dos Apskritis'),
            array('region_id' => '478', 'country_id' => 'LT', 'code' => 'LT-MR', 'default_name' => 'Marijampol?s Apskritis'),
            array('region_id' => '479', 'country_id' => 'LT', 'code' => 'LT-PN', 'default_name' => 'Panev?žio Apskritis'),
            array('region_id' => '480', 'country_id' => 'LT', 'code' => 'LT-SA', 'default_name' => 'Šiauli? Apskritis'),
            array('region_id' => '481', 'country_id' => 'LT', 'code' => 'LT-TA', 'default_name' => 'Taurag?s Apskritis'),
            array('region_id' => '482', 'country_id' => 'LT', 'code' => 'LT-TE', 'default_name' => 'Telši? Apskritis'),
            array('region_id' => '483', 'country_id' => 'LT', 'code' => 'LT-UT', 'default_name' => 'Utenos Apskritis'),
            array('region_id' => '484', 'country_id' => 'LT', 'code' => 'LT-VL', 'default_name' => 'Vilniaus Apskritis'),
            array('region_id' => '485', 'country_id' => 'BR', 'code' => 'AC', 'default_name' => 'Acre'),
            array('region_id' => '486', 'country_id' => 'BR', 'code' => 'AL', 'default_name' => 'Alagoas'),
            array('region_id' => '487', 'country_id' => 'BR', 'code' => 'AP', 'default_name' => 'Amapá'),
            array('region_id' => '488', 'country_id' => 'BR', 'code' => 'AM', 'default_name' => 'Amazonas'),
            array('region_id' => '489', 'country_id' => 'BR', 'code' => 'BA', 'default_name' => 'Bahia'),
            array('region_id' => '490', 'country_id' => 'BR', 'code' => 'CE', 'default_name' => 'Ceará'),
            array('region_id' => '491', 'country_id' => 'BR', 'code' => 'ES', 'default_name' => 'Espírito Santo'),
            array('region_id' => '492', 'country_id' => 'BR', 'code' => 'GO', 'default_name' => 'Goiás'),
            array('region_id' => '493', 'country_id' => 'BR', 'code' => 'MA', 'default_name' => 'Maranhão'),
            array('region_id' => '494', 'country_id' => 'BR', 'code' => 'MT', 'default_name' => 'Mato Grosso'),
            array('region_id' => '495', 'country_id' => 'BR', 'code' => 'MS', 'default_name' => 'Mato Grosso do Sul'),
            array('region_id' => '496', 'country_id' => 'BR', 'code' => 'MG', 'default_name' => 'Minas Gerais'),
            array('region_id' => '497', 'country_id' => 'BR', 'code' => 'PA', 'default_name' => 'Pará'),
            array('region_id' => '498', 'country_id' => 'BR', 'code' => 'PB', 'default_name' => 'Paraíba'),
            array('region_id' => '499', 'country_id' => 'BR', 'code' => 'PR', 'default_name' => 'Paraná'),
            array('region_id' => '500', 'country_id' => 'BR', 'code' => 'PE', 'default_name' => 'Pernambuco'),
            array('region_id' => '501', 'country_id' => 'BR', 'code' => 'PI', 'default_name' => 'Piauí'),
            array('region_id' => '502', 'country_id' => 'BR', 'code' => 'RJ', 'default_name' => 'Rio de Janeiro'),
            array('region_id' => '503', 'country_id' => 'BR', 'code' => 'RN', 'default_name' => 'Rio Grande do Norte'),
            array('region_id' => '504', 'country_id' => 'BR', 'code' => 'RS', 'default_name' => 'Rio Grande do Sul'),
            array('region_id' => '505', 'country_id' => 'BR', 'code' => 'RO', 'default_name' => 'Rondônia'),
            array('region_id' => '506', 'country_id' => 'BR', 'code' => 'RR', 'default_name' => 'Roraima'),
            array('region_id' => '507', 'country_id' => 'BR', 'code' => 'SC', 'default_name' => 'Santa Catarina'),
            array('region_id' => '508', 'country_id' => 'BR', 'code' => 'SP', 'default_name' => 'São Paulo'),
            array('region_id' => '509', 'country_id' => 'BR', 'code' => 'SE', 'default_name' => 'Sergipe'),
            array('region_id' => '510', 'country_id' => 'BR', 'code' => 'TO', 'default_name' => 'Tocantins'),
            array('region_id' => '511', 'country_id' => 'BR', 'code' => 'DF', 'default_name' => 'Distrito Federal'),
            array('region_id' => '512', 'country_id' => 'HR', 'code' => 'HR-01', 'default_name' => 'Zagreba?ka županija'),
            array('region_id' => '513', 'country_id' => 'HR', 'code' => 'HR-02', 'default_name' => 'Krapinsko-zagorska županija'),
            array('region_id' => '514', 'country_id' => 'HR', 'code' => 'HR-03', 'default_name' => 'Sisa?ko-moslava?ka županija'),
            array('region_id' => '515', 'country_id' => 'HR', 'code' => 'HR-04', 'default_name' => 'Karlova?ka županija'),
            array('region_id' => '516', 'country_id' => 'HR', 'code' => 'HR-05', 'default_name' => 'Varaždinska županija'),
            array('region_id' => '517', 'country_id' => 'HR', 'code' => 'HR-06', 'default_name' => 'Koprivni?ko-križeva?ka županija'),
            array('region_id' => '518', 'country_id' => 'HR', 'code' => 'HR-07', 'default_name' => 'Bjelovarsko-bilogorska županija'),
            array('region_id' => '519', 'country_id' => 'HR', 'code' => 'HR-08', 'default_name' => 'Primorsko-goranska županija'),
            array('region_id' => '520', 'country_id' => 'HR', 'code' => 'HR-09', 'default_name' => 'Li?ko-senjska županija'),
            array('region_id' => '521', 'country_id' => 'HR', 'code' => 'HR-10', 'default_name' => 'Viroviti?ko-podravska županija'),
            array('region_id' => '522', 'country_id' => 'HR', 'code' => 'HR-11', 'default_name' => 'Požeško-slavonska županija'),
            array('region_id' => '523', 'country_id' => 'HR', 'code' => 'HR-12', 'default_name' => 'Brodsko-posavska županija'),
            array('region_id' => '524', 'country_id' => 'HR', 'code' => 'HR-13', 'default_name' => 'Zadarska županija'),
            array('region_id' => '525', 'country_id' => 'HR', 'code' => 'HR-14', 'default_name' => 'Osje?ko-baranjska županija'),
            array('region_id' => '526', 'country_id' => 'HR', 'code' => 'HR-15', 'default_name' => 'Šibensko-kninska županija'),
            array('region_id' => '527', 'country_id' => 'HR', 'code' => 'HR-16', 'default_name' => 'Vukovarsko-srijemska županija'),
            array('region_id' => '528', 'country_id' => 'HR', 'code' => 'HR-17', 'default_name' => 'Splitsko-dalmatinska županija'),
            array('region_id' => '529', 'country_id' => 'HR', 'code' => 'HR-18', 'default_name' => 'Istarska županija'),
            array('region_id' => '530', 'country_id' => 'HR', 'code' => 'HR-19', 'default_name' => 'Dubrova?ko-neretvanska županija'),
            array('region_id' => '531', 'country_id' => 'HR', 'code' => 'HR-20', 'default_name' => 'Me?imurska županija'),
            array('region_id' => '532', 'country_id' => 'HR', 'code' => 'HR-21', 'default_name' => 'Grad Zagreb')
        );


        $_regionId = '';
        $_region = $this->array_search_result($_magentoRegions, 'code', $_regiodShortCode);
        if (count($_region) > 0) {
            $_regionId = $_region[0]['region_id'];
        }

        return $_regionId;
    }

    protected function array_search_result($_array, $_key, $_value)
    {
        $_result = array();
        foreach ($_array as $_k => $_v) {
            if (array_key_exists($_key, $_v) && ($_v[$_key] == $_value)) {
                $_result[] = $_v;
            }
        }
        return $_result;
    }
}

?>
