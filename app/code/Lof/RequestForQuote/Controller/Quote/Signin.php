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

namespace Lof\RequestForQuote\Controller\Quote;

use Magento\Customer\Controller\AccountInterface;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Exception\NotFoundException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;

class Signin extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    protected $storeManager;
    protected $accountManagement;
    protected $customerUrl;


    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Registry $registry,
        \Lof\RequestForQuote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Lof\RequestForQuote\Helper\Customer $customerHelper,
        \Lof\RequestForQuote\Helper\Data $rfqData,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $accountManagement,
        CustomerUrl $customerUrl,
        \Magento\Framework\Url $urlBuilder
    )
    {
        $this->resultPageFactory = $resultPageFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context);
        $this->mageQuoteFactory = $mageQuoteFactory;
        $this->quoteFactory = $quoteFactory;
        $this->rfqData = $rfqData;
        $this->dataHelper = $customerHelper;
        $this->storeManager = $storeManager;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->_urlBuilder = $urlBuilder;
    }

    public function execute()
    {
        if (!$this->rfqData->getConfig('general/enable')) {
            throw new NotFoundException(__('Page not found.'));
        }
        $resultPage = $this->resultPageFactory->create();
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $quote = $this->quoteFactory->create()->loadByIncrementId($quoteId);

            $data = [];
            $data["email"] = $quote->getEmail();
            $data['first_name'] = $quote->getFirstName();
            $data['last_name'] = $quote->getLastName();
            $data['password'] = $this->dataHelper->createPassword($quoteId);
            $data['password_confirmation'] = $data['password'];

            $company = $quote->getCompany();
            $telephone = $quote->getTelephone();
            $address = $quote->getAddress();
            $street = $quote->getStreet();
            $city = $quote->getCity();
            $region = $quote->getRegion();
            $region_id = $quote->getRegionId();
            $postcode = $quote->getPostcode();
            $country_id = $quote->getCountryId();

            if ($data['email']) {
                $store_id = $this->storeManager->getStore()->getStoreId();
                $website_id = $this->storeManager->getStore()->getWebsiteId();
                $customer = $this->dataHelper->getCustomerByEmail($data['email'], $website_id);

                if (!$customer || !$customer->getId()) {
                    $customer = $this->dataHelper->createCustomerMultiWebsite($data, $website_id, $store_id);
                    try {
                        $this->accountManagement->sendPasswordReminderEmail($customer);

                        $customer_info = [];
                        $customer_info['customer_id'] = $customer->getId();
                        $customer_info['email'] = $data['email'];
                        $customer_info['customer_group_id'] = (int)$customer->getGroupId();

                        $lofquote = $this->quoteFactory->create();
                        $lofquote->updateCustomerForQuote($quote->getId(), $quote->getQuoteId(), $customer_info);

                        $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());

                        if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                            $email = $this->customerUrl->getEmailConfirmationUrl($customer->getEmail());
                            // @codingStandardsIgnoreStart
                            $this->messageManager->addSuccess(
                                __(
                                    'You must confirm your account. Please check your email for the confirmation link or <a href="%1">click here</a> for a new link.',
                                    $email
                                )
                            );
                        } else {
                            $this->messageManager->addSuccess(__('Your account was created successfully.'));
                        }

                        $this->_eventManager->dispatch(
                            'lof_rfq_controller_signin',
                            ['customer' => $customer, 'lof_quote' => $lofquote]
                        );

                    } catch (Exception $e) {
                        $this->messageManager->addError(__('We can\'t process your request right now. Sorry, that\'s all we know.'));
                    }
                }
            }
        }
        $back_url = $this->_urlBuilder->getUrl('quotation/track');
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($back_url);

        return $resultRedirect;
    }
}