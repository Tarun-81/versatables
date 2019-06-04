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

namespace Lof\RequestForQuote\Controller\Cart;

use Magento\Customer\Api\Data\GroupInterface;

class SaveQuote extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * @var \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress
     */
    protected $_remoteAddress;

    /**
     * @var \Lof\RequestForQuote\Model\Cart
     */
    protected $quoteCart;

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

    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Model\Quote\TotalsCollector
     */
    protected $_totalsCollector;


    /**
     * SaveQuote constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Url $urlBuilder
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Lof\RequestForQuote\Model\Cart $quoteCart
     * @param \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Lof\RequestForQuote\Helper\Mail $rfqMail
     * @param \Lof\RequestForQuote\Helper\Data $rfqHelper
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\Url $urlBuilder,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Lof\RequestForQuote\Model\Cart $quoteCart,
        \Lof\RequestForQuote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Lof\RequestForQuote\Helper\Mail $rfqMail,
        \Lof\RequestForQuote\Helper\Data $rfqHelper,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
    ) {
        parent::__construct($context);
        $this->_formKeyValidator = $formKeyValidator;
        $this->_customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->_urlBuilder = $urlBuilder;
        $this->_remoteAddress = $remoteAddress;
        $this->quoteCart = $quoteCart;
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->rfqMail = $rfqMail;
        $this->_dataHelper = $rfqHelper;
        $this->_moduleManager = $moduleManager;
        $this->_totalsCollector = $totalsCollector;
    }

    /**
     * Create order action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $ip = $this->_remoteAddress->getRemoteAddress();
        // reCaptcha
        if (isset($_POST['g-recaptcha-response']) && ((int)$_POST['g-recaptcha-response']) === 0) {
            if ($this->getRequest()->isAjax()) {
                $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                return;
            }

            $this->messageManager->addError(__('Please check reCaptcha and try again.'));
            $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/quote'));
            $this->getDataPersistor()->set('quote_data', $this->getRequest()->getParams());
            return $resultRedirect;
        }
        if (isset($_POST['g-recaptcha-response'])) {
            $captcha = $_POST['g-recaptcha-response'];
            $secretKey = $this->_dataHelper->getConfig('recaptcha/captcha_privatekey');
            $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secretKey . "&response=" . $captcha . "&remoteip=" . $ip);
            $responseKeys = json_decode($response, true);
            if (intval($responseKeys["success"]) !== 1) {
                if ($this->getRequest()->isAjax()) {
                    $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                    return;
                }

                $this->messageManager->addError(__('Please check reCaptcha and try again.'));
                $this->getDataPersistor()->set('quote_data', $this->getRequest()->getParams());
                $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/quote'));
                return $resultRedirect;
            }
        }

        $quote = $this->quoteCart->getQuote();
        $post = $this->getRequest()->getPostValue();
		
        $only_save_address = isset($post['only_save_address']) ? (int)$post['only_save_address'] : 0;

        if (!isset($post['username']) || (isset($post['username']) && !$post['username'])) {
            $this->messageManager->addError(__('Please enter your email.'));
            $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/quote'));
            return $resultRedirect;
        }

        try {
            $quote_id_prefix = $this->_dataHelper->getConfig("general/quote_id_prefix");
            $digits_number = $this->_dataHelper->getConfig("general/digits_number");
            $digits_number = $digits_number ? (int)$digits_number : 1000000000;
            $limit = $this->_dataHelper->getConfig("quote_item/limit_useage");
            $remind_day = $this->_dataHelper->getConfig("quote_item/remind_day");
            $expiry_day = $this->_dataHelper->getConfig("quote_item/expiry_day");
            $expiry_day = $expiry_day ? (int)$expiry_day : 5;
            $firstname = isset($post['first_name']) ? $post['first_name'] : "";
            $lastname = isset($post['last_name']) ? $post['last_name'] : "";
            $company = isset($post['company']) ? $post['company'] : "";
            $street = isset($post['street']) ? $post['street'] : "";
            $city = isset($post['city']) ? $post['city'] : "";
            $telephone = isset($post['telephone']) ? $post['telephone'] : "";
            $address = isset($post['address']) ? $post['address'] : "";
            $region_id = isset($post['region_id']) ? $post['region_id'] : "0";
            $postcode = isset($post['postcode']) ? $post['postcode'] : "0";
            $country_id = isset($post['country_id']) ? $post['country_id'] : "";
            $tax_id = isset($post['tax_id']) ? $post['tax_id'] : "";
            $questions = isset($post['question']) ? $post['question'] : [];
            $question_string = '';
            if (is_array($questions) && $questions) {
                $tmp_questions = [];
                foreach ($questions as $key => $question) {
                    $label = isset($question['label']) ? $question['label'] : $key;
                    $val = isset($question['value']) ? $question['value'] : '';
                    $label = strip_tags($label);
                    $label = trim($label);
                    $label = stripslashes($label);
                    $label = addslashes($label);
                    $val = strip_tags($val);
                    $val = trim($val);
                    $val = stripslashes($val);
                    $val = addslashes($val);
                    if ($val) {
                        $tmp_questions[$key] = ['value' => $val, 'label' => $label];
                    }
                }
                $question_string = serialize($tmp_questions);
            }
			
            /** MAGE QUOTE */
            if (!$this->getCustomerSession()->isLoggedIn()) {
                $quote->setCustomerId(null)
                    ->setCustomerEmail($post['username'])
                    ->setCustomerNote(strip_tags($post['customer_note']))
                    ->setCustomerIsGuest(true)
                    ->setCustomerGroupId(GroupInterface::NOT_LOGGED_IN_ID);

                if (isset($post['billing'])) {
                    $billing = $post['billing'];
                    $street = $billing['street'];
                    if (is_array($street)) {
                        $street = trim(implode("\n", $street));
                    }
                    if ($billing['firstname']) {
                        $firstname = (string)$billing['firstname'];
                    }
                    if ($billing['lastname']) {
                        $lastname = (string)$billing['lastname'];
                    }
                    $quote->getBillingAddress()
                        ->setCountryId((string)$billing['country_id'])
                        ->setCity((string)$billing['city'])
                        ->setPostcode((string)$billing['postcode'])
                        ->setRegionId((string)$billing['region_id'])
                        ->setRegion((string)$billing['region'])
                        ->setFirstname((string)$billing['firstname'])
                        ->setLastname((string)$billing['lastname'])
                        ->setTelephone((string)$billing['telephone'])
                        ->setCompany((string)$billing['company'])
                        ->setStreet($street)
                        ->setCollectShippingRates(true)
                        ->save();

                    if (isset($post['billing-address-same-as-shipping'])) {
                        $quote->getShippingAddress()
                            ->setCountryId((string)$billing['country_id'])
                            ->setCity((string)$billing['city'])
                            ->setPostcode((string)$billing['postcode'])
                            ->setRegionId((string)$billing['region_id'])
                            ->setRegion((string)$billing['region'])
                            ->setFirstname((string)$billing['firstname'])
                            ->setLastname((string)$billing['lastname'])
                            ->setTelephone((string)$billing['telephone'])
                            ->setCompany((string)$billing['company'])
                            ->setStreet($street)
                            ->setSameAsBilling(1)
                            ->setCollectShippingRates(true)
                            ->save();
                    } else {
                        $shipping = $post['shipping'];
                        $street = $shipping['street'];
                        if (is_array($street)) {
                            $street = trim(implode("\n", $street));
                        }
                        $quote->getShippingAddress()
                            ->setCountryId((string)$shipping['country_id'])
                            ->setCity((string)$shipping['city'])
                            ->setPostcode((string)$shipping['postcode'])
                            ->setRegionId((string)$shipping['region_id'])
                            ->setRegion((string)$shipping['region'])
                            ->setFirstname((string)$shipping['firstname'])
                            ->setLastname((string)$shipping['lastname'])
                            ->setTelephone((string)$shipping['telephone'])
                            ->setCompany((string)$shipping['company'])
                            ->setStreet($street)
                            ->setSameAsBilling(1)
                            ->setCollectShippingRates(true)
                            ->save();
                    }
                } else {
                    $region = "";
                    if (!is_numeric($region_id)) {
                        $region = $region_id;
                    }
                    $region_id = is_numeric($region_id) ? (int)$region_id : 0;
                    $quote->getBillingAddress()
                        ->setCountryId((string)$country_id)
                        ->setCity((string)$city)
                        ->setPostcode((string)$postcode)
                        ->setRegionId((string)$region_id)
                        ->setRegion((string)$region)
                        ->setFirstname((string)$firstname)
                        ->setLastname((string)$lastname)
                        ->setTelephone((string)$telephone)
                        ->setCompany((string)$company)
                        ->setStreet($street)
                        ->setCollectShippingRates(true)
                        ->save();

                    $quote->getShippingAddress()
                        ->setCountryId((string)$country_id)
                        ->setCity((string)$city)
                        ->setPostcode((string)$postcode)
                        ->setRegionId((string)$region_id)
                        ->setRegion((string)$region)
                        ->setFirstname((string)$firstname)
                        ->setLastname((string)$lastname)
                        ->setTelephone((string)$telephone)
                        ->setCompany((string)$company)
                        ->setStreet($street)
                        ->setSameAsBilling(1)
                        ->setCollectShippingRates(true)
                        ->save();
                }
                if (isset($post['firstname'])) {
                    $quote->setFirstname($post['firstname']);
                }
                if ($only_save_address) {
                    $this->_totalsCollector->collectQuoteTotals($quote);
                    $this->messageManager->addSuccess(__('Updated the quote billing/shipping address.'));
                    $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/quote'));
                    return $resultRedirect;
                } else {
                    $quote->setRemoteIp($ip);
                    $quote->setData('rfq_parent_quote_id', null);
                    $this->quoteRepository->save($quote);
                    $this->quoteCart->save();
                    $this->_totalsCollector->collectQuoteTotals($quote);
                }
            } else {
                if (!$quote->getCustomerId()) {
                    $customer_object = $this->getCustomerSession()->getCustomer();

                    $firstname = $customer_object->getFirstname();
                    $lastname = $customer_object->getLastname();
                    $group_id = $customer_object->getGroupId();
                    $billing_address = $customer_object->getDefaultBilling();
                    $email = $customer_object->getEmail();

                    //Get billing address information
                    /*
                    $telephone = isset($post['telephone'])?$post['telephone']:"";
                    $address = isset($post['address'])?$post['address']:"";
                    $region_id = isset($post['region_id'])?$post['region_id']:"0";
                    $postcode = isset($post['postcode'])?$post['postcode']:"0";
                    $country_id = isset($post['country_id'])?$post['country_id']:"";
                    $tax_id = isset($post['tax_id'])?$post['tax_id']:"";
                    */

                    $quote->setCustomerId((int)$customer_object->getId())
                        ->setCustomerEmail($email)
                        ->setFirstname($firstname)
                        ->setLastname($lastname)
                        ->setCustomerGroupId($group_id);
                } else {
                    $customer_object = $this->getCustomerSession()->getCustomer();
                    $defaultShippingAddress = $customer_object->getDefaultShippingAddress();
                    if ($defaultShippingAddress) {
                        $defaultShippingAddress = $defaultShippingAddress->getData();

                        $shippingAddress = $this->_objectManager->create(
                            \Magento\Quote\Model\Quote\Address::class
                        )->setData(
                            $defaultShippingAddress
                        )->setAddressType(
                            \Magento\Quote\Model\Quote\Address::TYPE_SHIPPING
                        );

                        $quote->setShippingAddress($shippingAddress);
                    }
                }

                if (!$only_save_address) {
                    $quote->setRemoteIp($ip);
                    $quote->setCustomerNote(strip_tags($post['customer_note']));
                    $quote->setData('rfq_parent_quote_id', null);
                    $this->quoteRepository->save($quote);
                    $this->quoteCart->save();
                }
            }
			

            if ($quote) {
                if ($quote_address = $quote->getShippingAddress()) {
                    if (!$company) {
                        $company = $quote_address->getData("company");
                    }
                    if (!$telephone) {
                        $telephone = $quote_address->getData("telephone");
                    }
                    if (!$address) {
                        $address = $quote_address->getData("street");
                    }
                    if (!$region_id) {
                        $region_id = $quote_address->getData("region");
                    }
                    if (!$postcode) {
                        $postcode = $quote_address->getData("postcode");
                    }
                    if (!$country_id) {
                        $country_id = $quote_address->getData("country_id");
                    }
                }
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
            /** RFQ QUOTE */
            $count = $this->_quoteCollectionFactory->create()->getSize();
            if ($count) {
                $incrementId = $digits_number + $count + 1;
            } else {
                $incrementId = $digits_number + 1;
            }
            $incrementId = $quote_id_prefix . $incrementId;
            $customer = $quote->getCustomer();
            $email = $customer ? $quote->getCustomerEmail() : $customer->getEmail();
            if (!$email) {
                $email = isset($post['username']) ? $post['username'] : "";
            }
            $data = [
                'quote_id' => $quote->getId(),
                'increment_id' => $incrementId,
                'status' => $this->_dataHelper->getConfig("general/quote_status"),
                'email' => $email,
                'customer_id' => $customer ? $customer->getId() : '',
                'limit_useage' => $limit,
                'expiry' => $expiry,
                'remind' => $remind,
                'send_expiry_email' => $send_expiry_email,
                'send_remind_email' => $send_remind_email,
                'telephone' => $telephone,
                'tax_id' => $tax_id,
                'first_name' => $firstname,
                'last_name' => $lastname,
                'company' => $company,
                'address' => $address,
                'city' => $city,
                'street' => $street,
                'region_id' => $region_id,
                'postcode' => $postcode,
                'country_id' => $country_id,
                'question' => $question_string
            ];
			
			
            $rfqQuote = $this->_objectManager->create('Lof\RequestForQuote\Model\Quote');
            $rfqQuote->setData($data);
            $rfqQuote->save();

//            $mageQuote = $this->checkoutSession->getQuote();
//            $mageQuote->setIsActive(false);
//            $this->quoteRepository->save($mageQuote);
//            $this->checkoutSession->clearQuote()->clearStorage();
			
            $quote->setIsActive(false);
            $this->quoteRepository->save($quote);

            /** SEND CONFIRMATIONS EMAIL */
            $this->checkoutSession->setRfqLastQuoteId($rfqQuote->getId());
            $file = [];
            if ($this->_moduleManager->isEnabled('Lof_RequestForQuotePdf')) {
                try{
                    $pdfModel = $this->_objectManager->create('\Lof\RequestForQuotePdf\Model\Quote\Pdf\Quote');
                    $file = $pdfModel->generatePdf($rfqQuote, $quote);
                }catch (\Exception $e) {
                }
            }
            $this->rfqMail->sendNotificationNewQuoteEmail($quote, $rfqQuote, $file);

            $this->_eventManager->dispatch(
                'lof_rfq_controller_cart_save_quote',
                ['mage_quote' => $quote, 'lof_quote' => $rfqQuote]
            );
			
            $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/cart/success'));

        } catch (\Exception $e) {
            $this->messageManager->addError(__('Something went wrong while processing your quote. Please try again later.'));
            $resultRedirect->setUrl($this->_urlBuilder->getUrl('quotation/quote'));
        }
        return $resultRedirect;
    }

    /**
     * Get customer session object
     *
     * @return \Magento\Customer\Model\Session
     * @codeCoverageIgnore
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

}
