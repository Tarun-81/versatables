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

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

use Magento\Backend\App\Action;

/**
 * Class Create
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Create extends \Magento\Sales\Controller\Adminhtml\Order\Create\Save
{
    /**
     * @var \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender
     */
    protected $emailSender;

    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var array
     */
    protected $_errors;

    /**
     * Create constructor.
     * @param Action\Context $context
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender $emailSender
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Framework\Escaper $escaper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Cart2Quote\Quotation\Model\Admin\Quote\EmailSender $emailSender,
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory
    ) {
        $this->emailSender = $emailSender;
        $this->quoteFactory = $quoteFactory;
        $this->_customerFactory = $customerFactory;
        $this->_addressFactory = $addressFactory;

        parent::__construct(
            $context,
            $productHelper,
            $escaper,
            $resultPageFactory,
            $resultForwardFactory
        );
    }

    /**
     * Saving quote and create quotation
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     * Based on: \Magento\Sales\Controller\Adminhtml\Order\Create\Save::execute
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            // check if the creation of a new customer is allowed
            if (!$this->_authorization->isAllowed('Magento_Customer::manage')
                && !$this->_getSession()->getCustomerId()
                && !$this->_getSession()->getQuote()->getCustomerIsGuest()
            ) {
                return $this->resultForwardFactory->create()->forward('denied');
            }

            $this->_getOrderCreateModel()->getQuote()->setCustomerId($this->_getSession()->getCustomerId());
            $this->_processActionData('save');
            $paymentData = $this->getRequest()->getPost('payment');
            if ($paymentData) {
                $paymentData['checks'] = [
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_INTERNAL,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX,
                    \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL,
                ];
                $this->_getOrderCreateModel()->setPaymentData($paymentData);
                $this->_getOrderCreateModel()->getQuote()->getPayment()->addData($paymentData);
            }

            //validate the quotedata
            $this->_validate();

            //prepair the quote
            $quoteCreateModel = $this->_getOrderCreateModel()
                ->setIsValidate(true)
                ->importPostData($this->getRequest()->getPost('order'))
                ->_prepareCustomer();

            //save the customer
            $customer = $quoteCreateModel->getQuote()->getCustomer();
            if ($customer) {
                if ($customer->getId() == null) {
                    //new customer
                    $customerModel = $this->_customerFactory->create()->updateData($customer)->save();

                    //add address data
                    if ($quoteCreateModel->getBillingAddress()->getSaveInAddressBook()) {
                        $billingQuoteAddress = $quoteCreateModel->getBillingAddress();
                        if ($billingQuoteAddress) {
                            $billingAddress = $this->_addressFactory
                                ->create()
                                ->addData($billingQuoteAddress->getData())
                                ->setIsDefaultBilling(true);
                            $customerModel->addAddress($billingAddress);
                        }
                    }
                    if (!$quoteCreateModel->getQuote()->isVirtual() && $quoteCreateModel->getShippingAddress()->getSaveInAddressBook()) {
                        $shippingQuoteAddress = $quoteCreateModel->getShippingAddress();
                        if ($shippingQuoteAddress) {
                            $shippingAddress = $this->_addressFactory
                                ->create()
                                ->addData($shippingQuoteAddress->getData())
                                ->setIsDefaultShipping(true);
                            $customerModel->addAddress($shippingAddress);
                        }
                    }

                    //save customer
                    $customerModel->save();
                    $customerModel->sendNewAccountEmail('registered', '', $customerModel->getStoreId());

                    //update quote
                    $customer->setId($customerModel->getId());
                    $quoteCreateModel->getQuote()->setCustomer($customer);
                    $quoteCreateModel->getQuote()->updateCustomerData($quoteCreateModel->getQuote()->getCustomer());
                } else {
                    //update existing account data?
                }
            }

            //save the quote
            $quote = $quoteCreateModel->saveQuote();

            //get quote id
            $quoteId = $quote->getQuote()->getId();

            //load quote based on quote id (to check later if it already exists)
            $quotation = $this->quoteFactory->create()->load($quoteId);

            //create the quotation quote if it doesn't already exist
            if (!$quotation->getId()) {
                $quotation = $this->quoteFactory->create()->create($quote->getQuote())->load($quoteId);
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(__('You created the quote.'));
            } else {
                $this->_getSession()->clearStorage();
                $this->messageManager->addSuccess(__('You updated the quote.'));
            }

            $this->_getSession()->clearStorage();

            if ($this->_authorization->isAllowed('Cart2Quote_Quotation::actions_view')) {
                $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quotation->getId()]);
            } else {
                $resultRedirect->setPath('quotation/quote/index');
            }
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->_getOrderCreateModel()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote_create');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote_create');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote_create');
        }

        return $resultRedirect;
    }

    /**
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _validate()
    {
        $customerId = $this->_getOrderCreateModel()->getSession()->getCustomerId();
        if ($customerId === null) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a customer'));
        }

        if (!$this->_getOrderCreateModel()->getSession()->getStore()->getId()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Please select a store'));
        }

        if (!empty($this->_errors)) {
            foreach ($this->_errors as $error) {
                $this->messageManager->addError($error);
            }
            throw new \Magento\Framework\Exception\LocalizedException(__('Validation is failed.'));
        }

        return $this;
    }
}
