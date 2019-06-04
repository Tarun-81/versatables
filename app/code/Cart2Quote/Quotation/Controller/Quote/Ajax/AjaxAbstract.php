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
use Cart2Quote\Quotation\Model\Quote;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class AjaxAbstract
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
abstract class AjaxAbstract extends \Magento\Checkout\Controller\Onepage
{
    const EVENT_PREFIX = 'Default';

    /**
     * Sender
     *
     * @var \Cart2Quote\Quotation\Model\Quote\Email\AbstractSender
     */
    protected $sender;

    /**
     * Quote Proposal Accepted Sender
     *
     * @var Quote\Email\Sender\QuoteProposalAcceptedSender
     */
    protected $_quoteProposalAcceptedSender;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $_quoteRepository;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Data Object Factory
     *
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * Quote Factory
     *
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Customer Cart
     *
     * @var CustomerCart
     */
    protected $quotationCart;

    /**
     * Create Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    protected $createQuote;

    /**
     * Data Object
     *
     * @var \Magento\Framework\DataObject
     */
    protected $result;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $statusCollection;

    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * AjaxAbstract constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param AccountManagementInterface $accountManagement
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param Quote\Email\Sender\QuoteRequestSender $sender
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param Quote\CreateQuote $createQuote
     * @param CustomerCart $quotationCart
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepositoryInterface $customerRepository,
        AccountManagementInterface $accountManagement,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\DataObjectFactory $dataObjectFactory,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $sender,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Model\Quote\CreateQuote $createQuote,
        \Cart2Quote\Quotation\Model\QuotationCart $quotationCart,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Helper\Data $helper
    ) {
        $this->quotationCart = $quotationCart;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->quoteFactory = $quoteFactory;
        $this->sender = $sender;
        $this->quoteSession = $quoteSession;
        $this->createQuote = $createQuote;
        $this->statusCollection = $statusCollection;
        $this->helper = $helper;

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
            $quoteRepository,
            $resultPageFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $resultJsonFactory
        );
    }

    /**
     * Update quote data action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        if ($this->_expireAjax()) {
            $response = $this->_ajaxRedirectResponse();
            return $response->setContents(json_encode('Session expired. Please submit your quote again.'));
        }

        $this->result = $this->dataObjectFactory->create();

        $this->_eventManager->dispatch(
            'quotation_controller_frontend_default_before',
            [
                'result' => $this->result,
                'action' => $this
            ]
        );

        $this->_eventManager->dispatch(
            sprintf('quotation_controller_frontend_%s_before', $this->getEventPrefix()),
            [
                'result' => $this->result,
                'action' => $this
            ]
        );

        $this->result->setData('success', true);
        $this->result->setData('error', false);

        try {
            $this->processAction();
        } catch (LocalizedException $e) {

            $this->_eventManager->dispatch(
                'quotation_controller_frontend_default_localized_exception',
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );

            $this->_eventManager->dispatch(
                sprintf('quotation_controller_frontend_%s_localized_exception', $this->getEventPrefix()),
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );

            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            $this->result->setData('success', false);
            $this->result->setData('error', true);
            $this->result->setData('message', $e->getMessage());
            $gotoSection = $this->getOnepage()->getCheckout()->getGotoSection();
            if ($gotoSection) {
                $this->result->setData('goto_section', $gotoSection);
                $this->getOnepage()->getCheckout()->setGotoSection(null);
            }

            $updateSection = $this->getOnepage()->getCheckout()->getUpdateSection();
            if ($updateSection) {
                if (isset($this->_sectionUpdateFunctions[$updateSection])) {
                    $updateSectionFunction = $this->_sectionUpdateFunctions[$updateSection];
                    $this->result->setData(
                        'update_section',
                        [
                            'name' => $updateSection,
                            'html' => $this->{$updateSectionFunction}(),
                        ]
                    );
                }

                $this->getOnepage()->getCheckout()->setUpdateSection(null);
            }

        } catch (\Exception $e) {

            $this->_eventManager->dispatch(
                'quotation_controller_frontend_default_exception',
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );

            $this->_eventManager->dispatch(
                sprintf('quotation_controller_frontend_%s_exception', $this->getEventPrefix()),
                [
                    'result' => $this->result,
                    'action' => $this
                ]
            );

            $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);

            try {
                $this->_objectManager->get('Magento\Checkout\Helper\Data')->sendPaymentFailedEmail(
                    $this->getOnepage()->getQuote(),
                    $e->getMessage()
                );
            } catch (\Zend_Mail_Transport_Exception $emailException) {
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($emailException);
            }

            $this->result->setData('success', false);
            $this->result->setData('error', true);
            $this->result->setData(
                'message',
                __('Something went wrong while processing your quote. Please try again later.')
            );
        }

        $this->_eventManager->dispatch(
            sprintf('quotation_controller_frontend_%s_after', $this->getEventPrefix()),
            [
                'result' => $this->result,
                'action' => $this
            ]
        );

        $this->_eventManager->dispatch(
            'quotation_controller_frontend_default_after',
            [
                'result' => $this->result,
                'action' => $this
            ]
        );

        return $this->resultJsonFactory->create()->setData($this->result->getData());
    }

    /**
     * Validate ajax request and redirect on failure
     *
     * @return bool
     */
    protected function _expireAjax()
    {
        $quote = $this->getOnepage()->getQuote();
        if (!$quote->hasItems() || $quote->getHasError()) {
            return true;
        }
        $action = $this->getRequest()->getActionName();
        if ($this->_objectManager->get(\Magento\Checkout\Model\Session::class)->getCartWasUpdated(true) &&
            !in_array($action, ['index', 'createQuote', 'updateQuote'])) {
            return true;
        }

        return false;
    }

    /**
     * Get one page checkout model
     *
     * @return \Cart2Quote\Quotation\Model\Quote\CreateQuote
     */
    public function getOnepage()
    {
        return $this->createQuote;
    }

    /**
     * Get event prefix
     *
     * @return string
     */
    public function getEventPrefix()
    {
        return self::EVENT_PREFIX;
    }

    /**
     * Overwrite this function to perform an ajax action on the RFQ page.
     *
     * @return bool
     */
    public function processAction()
    {
        return false;
    }

    /**
     * Checking customer login status
     *
     * @return bool
     */
    public function isCustomerLoggedIn()
    {
        return $this->_customerSession->isLoggedIn();
    }

    /**
     * Update the fields from the quotation data on the session.
     *
     * @return void
     */
    protected function updateQuotationProductData()
    {
        $quoteProductData = $this->quoteSession->getData(
            \Cart2Quote\Quotation\Model\Session::QUOTATION_PRODUCT_DATA
        );

        if (is_array($quoteProductData)) {
            $quoteItems = $this->quotationCart->getQuote()->getItemsCollection();
            foreach ($quoteProductData as $fieldName => $productData) {
                foreach ($productData as $id => $value) {
                    $quoteItem = $quoteItems->getItemById($id);
                    if (isset($quoteItem)) {
                        $quoteItem->setData($fieldName, $value);
                    }
                }
            }

            $quoteItems->save();
        }
    }
}
