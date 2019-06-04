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

use Cart2Quote\Quotation\Model\QuotationCart as CustomerCart;

/**
 * Class CreateQuote
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
class CreateQuote extends \Cart2Quote\Quotation\Controller\Quote\Ajax\AjaxAbstract
{
    /**
     * CreateQuote constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Cart2Quote\Quotation\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRequestRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote
     * @param CustomerCart $quotationCart
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Cart2Quote\Quotation\Api\AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRequestRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote,
        CustomerCart $quotationCart,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Helper\Data $helper
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $customerRepository,
            $accountManagement,
            $coreRegistry,
            $translateInline,
            $formKeyValidator,
            $scopeConfig,
            $layoutFactory,
            $quoteRequestRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory,
            $dataObjectFactory,
            $quoteFactory,
            $sender,
            $quoteSession,
            $createQuote,
            $quotationCart,
            $statusCollection,
            $helper
        );
    }

    /**
     * Request customer's quote.
     *
     * @return boolean
     */
    public function processAction()
    {
        $quote = $this->getOnepage()->getQuote();
        $this->saveCustomer($quote);
        $this->addQuotationData();

        $quote->assignCustomerWithAddressChange(
            $quote->getCustomer(),
            $quote->getBillingAddress(),
            $quote->getShippingAddress()
        );

        $this->updateQuotationProductData();

        if ($this->getRequest()->getParam('clear_quote', false)) {
            $quote->setIsActive(false);
        }

        $quotation = $this->save($quote);
        $this->sendEmailToCustomer($quotation);

        if ($this->getRequest()->getParam('clear_quote', false)) {
            $this->quoteSession->fullSessionClear();
            $this->quoteSession->updateLastQuote($quotation);
        }

        $this->result->setData('last_quote_id', $quotation->getId());

        $this->_eventManager->dispatch(
            'quotation_event_after_quote_request', ['quote' => $quotation]
        );

        return true;
    }

    /**
     * Save the customer.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function saveCustomer(\Magento\Quote\Model\Quote $quote)
    {
        $customerEmail = $this->getRequest()->getParam('customer_email', false);
        $checkoutAsGuest = filter_var(
            $this->getRequest()->getParam('checkout_as_guest', false),
            FILTER_VALIDATE_BOOLEAN
        );

        $this->validateCustomerEmail($customerEmail);

        if ($customerEmail && $customerEmail != 'null') {
            $quote = $this->setCustomerName($quote);
            $quote->setCustomerEmail($customerEmail);

            if ($checkoutAsGuest) {
                $this->getOnepage()->setQuote($quote)->saveAsGuest();
            } else {
                if ($this->isCustomerLoggedIn()) {
                    $this->getOnepage()->setQuote($quote)->saveCustomer();
                } else {
                    $this->getOnepage()->setQuote($quote)->saveNewCustomer();
                    $this->autoLogin($quote);
                }
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Email address is mandatory for a quote.')
            );
        }
    }

    /**
     * Checks if the inserted email already exists and if the customer is logged in.
     *
     * @param string $email
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return void
     */
    private function validateCustomerEmail($email)
    {
        try {
            $this->customerRepository->get($email);
        } catch (\Magento\Framework\Exception\LocalizedException  $e) {
            // If the customer does not exists a localizedException will be thrown.
            return;
        }

        if (!$this->isCustomerLoggedIn()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('Customer account already exists, please login to request a quote.')
            );
        }
    }

    /**
     * Set the first and last name
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Magento\Quote\Model\Quote
     */
    private function setCustomerName(\Magento\Quote\Model\Quote $quote)
    {
        /** Get from billing address */
        $firstName = $quote->getBillingAddress()->getFirstname();
        $lastName = $quote->getBillingAddress()->getLastname();

        /** Get from shipping address */
        if (!$firstName && !$lastName) {
            $firstName = $quote->getShippingAddress()->getFirstname();
            $lastName = $quote->getShippingAddress()->getLastname();
        }

        /** Get from quotation session */
        if (!$firstName && !$lastName) {
            $quoteData = $this->quoteSession->getData(
                \Cart2Quote\Quotation\Model\Session::QUOTATION_GUEST_FIELD_DATA
            );

            if (isset($quoteData['firstname'], $quoteData['lastname'])) {
                $firstName = $quoteData['firstname'];
                $lastName = $quoteData['lastname'];
            }
        }

        $quote->setCustomerFirstname($firstName);
        $quote->setCustomerLastname($lastName);

        return $quote;
    }

    /**
     * Auto login the customer
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return void
     */
    private function autoLogin(\Magento\Quote\Model\Quote $quote)
    {
        if ($customer = $quote->getCustomer()) {
            if (!$customer->getId()) {
                $quote->setCustomer($this->customerRepository->save($customer));
            }

            $this->_customerSession->setCustomerDataAsLoggedIn($quote->getCustomer());
        }
    }

    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     */
    private function addQuotationData()
    {
        $quoteData = $this->quoteSession->getData(
            \Cart2Quote\Quotation\Model\Session::QUOTATION_FIELD_DATA
        );
        $this->updateCustomerNote($quoteData);
    }

    /**
     * Update that customer note on the quote.
     *
     * @param array $quoteData
     *
     * @return void
     */
    private function updateCustomerNote($quoteData)
    {
        if (isset($quoteData[\Cart2Quote\Quotation\Model\Quote::KEY_CUSTOMER_NOTE])) {
            $this->quotationCart->getQuote()->setCustomerNote(
                $quoteData[\Cart2Quote\Quotation\Model\Quote::KEY_CUSTOMER_NOTE]
            );
        }
    }

    /**
     * Save the Quotation Quote.
     *
     * @param \Magento\Quote\Model\Quote $quote
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    private function save(\Magento\Quote\Model\Quote $quote)
    {
        $quoteModel = $this->quoteFactory->create();
        $quotation = $quoteModel->create($quote)->load($quote->getId());

        return $quotation;
    }

    /**
     * Send the quote email to the customer.
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     *
     * @return void
     */
    private function sendEmailToCustomer(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
        $this->sender->send($quotation);
    }
}
