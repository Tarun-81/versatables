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

namespace Lof\RequestForQuote\Controller\Adminhtml\Quote\Create;

use Magento\Customer\Api\Data\GroupInterface;

class Save extends \Lof\RequestForQuote\Controller\Adminhtml\Quote\Create
{
    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Lof\RequestForQuote\Model\ResourceModel\Quote\Collection
     */
    protected $_quoteCollectionFactory;

    /**
     * @var \Lof\RequestForQuote\Helper\Mail
     */
    protected $rfqMail;
    /**
     * @var \Lof\RequestForQuote\Helper\Data
     */

    protected $_dataHelper;

    protected $_moduleManager;

    protected $customerDataFactory;

    protected $customerRepository;

    protected $customerFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Lof\RequestForQuote\Helper\Mail $rfqMail,
        \Lof\RequestForQuote\Helper\Data $rfqHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        parent::__construct($context, $productHelper, $escaper, $resultPageFactory, $resultForwardFactory);
        $this->_remoteAddress = $remoteAddress;
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->rfqMail = $rfqMail;
        $this->_dataHelper = $rfqHelper;
        $this->_moduleManager = $moduleManager;
        $this->customerDataFactory = $customerDataFactory;
        $this->customerRepository = $customerRepository;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Saving quote and create order
     *
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Lof_RequestForQuote::quote_save')
                && !$this->_getSession()->getCustomerId()
                && !$this->_getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }
            $limit = $this->_dataHelper->getConfig("quote_item/limit_useage");
            $limit = $limit ? (int)$limit : '';
            $expiry_day = $this->_dataHelper->getConfig("quote_item/expiry_day");
            $expiry_day = $expiry_day ? (int)$expiry_day : 5;
            $remind_day = $this->_dataHelper->getConfig("quote_item/remind_day");
            $quote_id_prefix = $this->_dataHelper->getConfig("general/quote_id_prefix");
            $digits_number = $this->_dataHelper->getConfig("general/digits_number");
            $digits_number = $digits_number ? (int)$digits_number : 1000000000;
            $quote = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))
                ->getQuote();
            //check if quote not have any items, will return the previous page
            $quote_items = $quote->getAllItems(); 
            if (!$quote_items || count($quote_items) <= 0) {
                $this->messageManager->addError(__('Can not create the quote. Please select products before.'));
                $resultRedirect->setPath('quotation/quote/*');
                return $resultRedirect;
            }
            $this->updateOriginalPrice($quote_items);
            $ip = $this->_remoteAddress->getRemoteAddress();
            $post = $this->getRequest()->getPost();
            $customer = $quote->getCustomer();
            $customer_post = isset($post['order']['account']) ? $post['order']['account'] : array();
            $billing = isset($post['order']['billing_address']) ? $post['order']['billing_address'] : array();

            if($post['shipping_same_as_billing'] == "on"){
                $shipping = $post['order']['billing_address'];
            } else {
                $shipping = isset($post['order']['shipping_address']) ? $post['order']['shipping_address'] : array();
            }

            $firstName = "";
            $lastName = "";
            $customerGroupId = GroupInterface::NOT_LOGGED_IN_ID;

            if($this->customerExists($customer->getEmail())){
                $customer = $this->customerRepository->get($customer->getEmail());
                $firstName = $customer->getFirstName();
                $lastName = $customer->getLastName();
                $customerGroupId = $customer->getGroupId();
            } else {
                $firstName = isset($billing['firstname']) ? $billing['firstname'] : '';
                $lastName = isset($billing['lastname']) ? $billing['lastname'] : '';
                $customerGroupId = isset($customer_post['customer_group_id']) ? (int)$customer_post['customer_group_id'] : GroupInterface::NOT_LOGGED_IN_ID;

                $customerData = $this->customerDataFactory->create();
                $customerData->setFirstname($firstName);
                $customerData->setLastname($lastName);
                $customerData->setEmail($post['order']['account']['email']);
                $customer =  $this->customerRepository->save($customerData);
            }

            $quote->setCustomer($customer);

            $quote->setCustomerId($customer->getId());

            if (!$billing) {
                $address_model = $quote->getBillingAddress();
                $billing = $address_model->getData();
            }

            $telephone = $tax_id = $company = $street = $city = $region_id = $country_id = $address = '';
            if ($billing) {
                $telephone = isset($billing['telephone']) ? $billing['telephone'] : '';
                $tax_id = isset($billing['tax_id']) ? $billing['tax_id'] : '';
                $company = isset($billing['company']) ? $billing['company'] : '';
                $street = isset($billing['street']) ? $billing['street'] : '';
                $city = isset($billing['city']) ? $billing['city'] : '';
                $region_id = isset($billing['region_id']) ? $billing['region_id'] : '';
                $postcode = isset($billing['postcode']) ? $billing['postcode'] : '';
                $country_id = isset($billing['country_id']) ? $billing['country_id'] : '';
            }

            $customer_note = $this->_stripScriptTags($post['order']['account']['customer_note']);

            if (!$quote->getCustomerFirstname()) {
                $quote->setCustomerFirstname($firstName);
            }
            if (!$quote->getCustomerLastname()) {
                $quote->setCustomerLastname($lastName);
            }
            if (!$quote->getCustomerGroupId()) {
                $quote->setCustomerGroupId($customerGroupId);
            }
            $quote->setRemoteIp($ip);
            $quote->setCustomerNote($customer_note);
            $quote->setCustomerEmail(($customer && $customer->getEmail()) ? $customer->getEmail() : $post['order']['account']['email']);
            $quote->save();

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

            /** RFQ QUOTE */
            $count = $this->_quoteCollectionFactory->create()->getSize();
            if ($count) {
                $incrementId = $digits_number + $count + 1;
            } else {
                $incrementId = $digits_number + 1;
            }
            $incrementId = $quote_id_prefix . $incrementId;

            $admin_note = isset($post['order']['account']['admin_note']) ? $post['order']['account']['admin_note'] : '';
            $admin_note = $this->_stripScriptTags($admin_note);

            $terms = isset($post['order']['account']['terms']) ? $post['order']['account']['terms'] : '';
            $terms = $this->_stripScriptTags($terms);

            $wtexpect = isset($post['order']['account']['wtexpect']) ? $post['order']['account']['wtexpect'] : '';
            $wtexpect = $this->_stripScriptTags($wtexpect);

            $break_line = isset($post['order']['account']['break_line']) ? $post['order']['account']['break_line'] : '';
            $break_line = $this->_stripScriptTags($break_line);

            $data = [
                'quote_id' => $quote->getId(),
                'increment_id' => $incrementId,
                'status' => $this->_dataHelper->getConfig("general/quote_status"),
                'email' => ($customer && $customer->getEmail()) ? $customer->getEmail() : $post['order']['account']['email'],
                'customer_id' => $customer ? $customer->getId() : '',
                'first_name' => $firstName,
                'last_name' => $lastName,
                'limit_useage' => $limit,
                'expiry' => $expiry,
                'remind' => $remind,
                'send_expiry_email' => $send_expiry_email,
                'send_remind_email' => $send_remind_email,
                'admin_note' => $admin_note,
                'terms' => $terms,
                'wtexpect' => $wtexpect,
                'break_line' => $break_line,
                'telephone' => $telephone,
                'tax_id' => $tax_id,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'region_id' => $region_id,
                'postcode' => $postcode,
                'country_id' => $country_id
            ];

            $rfqQuote = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
            $rfqQuote->setData($data);
            $rfqQuote->save();

            /** SEND CONFIRMATIONS EMAIL */

            $file = [];
            if ($this->_moduleManager->isEnabled('Lof_RequestForQuotePdf')) {
                $pdfModel = $this->_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
                $file = $pdfModel->generatePdf($rfqQuote, $quote);
            }

            $this->rfqMail->sendNotificationNewQuoteEmail($quote, $rfqQuote);

            $this->_eventManager->dispatch(
                'lof_rfq_controller_admin_new_quote',
                ['mage_quote' => $quote, 'lof_quote' => $rfqQuote]
            );

            $this->_getSession()->clearRfqQuote();

            $this->messageManager->addSuccess(__('You created the quote.'));

            $resultRedirect->setPath('quotation/quote/edit', ['entity_id' => $rfqQuote->getId()]);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote/*');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote/*');
        }
        return $resultRedirect;
    }

    protected function updateOriginalPrice($quote_items){
        foreach ($quote_items as $itemId => $iteminfo) {
            $custom_price = $iteminfo->getCustomPrice();
            $price = $iteminfo->getPrice();  
            if(isset($custom_price) && $custom_price !== $price){
                $original_price = $iteminfo->getOriginalPrice();
                if (!$original_price || ((float)$original_price <= 0.0000)) {
                    $iteminfo->setOriginalPrice($custom_price);
                }
            }else{
                $original_price = $iteminfo->getPrice();
                $iteminfo->setOriginalPrice($original_price);
            }
        }
    }

    protected function _stripScriptTags($html)
    {
        return preg_replace('#<script(.*?)>(.*?)</script>#is', '', $html);
    }

    protected function customerExists($email, $websiteId = 1)
    {
        $customerModel = $this->customerFactory->create();
        if ($websiteId) {
            $customerModel->setWebsiteId($websiteId);
        }
        $customerModel->loadByEmail($email);
        if ($customerModel->getId()) {
            return true;
        }

        return false;
    }

}

