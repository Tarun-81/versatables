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

namespace Cart2Quote\Quotation\Controller\Adminhtml;

/**
 * Adminhtml quotation quotes controller
 */
abstract class Quote extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * Array of actions which can be processed without secret key validation
     * @var string[]
     */
    protected $_publicActions = ['view', 'index'];
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $_translateInline;
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_currentQuote;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;
    /**
     * Quote Create Model
     *
     * @var \Cart2Quote\Quotation\Model\Admin\Quote\Create
     */
    protected $quoteCreate;

    /**
     * Quote constructor.
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->escaper = $escaper;
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->_helperData = $helperData;
        $this->quoteFactory = $quoteFactory;
        $this->_statusCollection = $statusCollection;
        $this->quoteCreate = $quoteCreate;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cart2Quote_Quotation::quotation_quote');
        $resultPage->addBreadcrumb(__('Quotation'), __('Quotation'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));

        return $resultPage;
    }

    /**
     * Initialize quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote|false
     */
    protected function _initQuote()
    {
        $id = $this->getRequest()->getParam('quote_id');
        if (!$id) {
            $id = $this->_getSession()->getQuote()->getId();
        }

        $this->_currentQuote = $this->quoteFactory->create()->load($id);

        if (!$this->_currentQuote->getId()) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentQuote);

        return $this->_currentQuote;
    }

    /**
     * Retrieve session object
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
    }

    /**
     * Acl check for admin
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::quotes');
    }

    /**
     * Quotes grid
     * @return null|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return null;
    }

    /**
     * Initialize quote creation session data
     * @return $this
     */
    protected function _initSession()
    {
        /**
         * Identify quote
         */
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $this->_getSession()->setQuoteId((int)$quoteId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                $this->_getSession()->setQuoteId((int)$quote->getId());
            }
        }

        /**
         * Identify customer
         */
        $this->_getSession()->setCustomerId(null);
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int)$customerId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($customerId = $quote->getCustomerId()) {
                    $this->_getSession()->setCustomerId((int)$customerId);
                }
            }
        }

        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int)$storeId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($storeId = $quote->getStoreId()) {
                    $this->_getSession()->setStoreId((int)$storeId);
                }
            }
        }

        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string)$currencyId);
            $this->getCurrentQuote()->setRecollect(true);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($currencyId = $quote->getCurrencyId()) {
                    $this->_getSession()->setCurrencyId((string)$currencyId);
                    $this->getCurrentQuote()->setRecollect(true);
                }
            }
        }

        return $this;
    }

    /**
     * Retrieve quote create model
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    protected function getCurrentQuote()
    {
        if (!isset($this->_currentQuote)) {
            if ($this->_coreRegistry->registry('current_quote')) {
                return $this->_currentQuote = $this->_coreRegistry->registry('current_quote');
            }

            //if quote isn't set, return new quote model
            return $this->_currentQuote = $this->quoteFactory->create();
        }

        return $this->_currentQuote;
    }

    /**
     * Processing request data
     * @return $this
     */
    protected function _processData()
    {
        return $this->_processActionData();
    }

    /**
     * Process request data with additional logic for saving quote and creating order
     * @param string $action
     * @return $this
     */
    protected function _processActionData($action = null)
    {
        $eventData = [
            'quote_model' => $this->getCurrentQuote(),
            'request_model' => $this->getRequest(),
            'session' => $this->_getSession(),
        ];

        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data_before', $eventData);
        $data = $this->getRequest()->getPost('quote');

        /**
         * Saving order data
         */
        if ($data) {
            $this->getCurrentQuote()->importPostData($data);
            $quote = $this->getRequest()->getParam('quote', false);
            if (!isset($data['expiry_enabled'])) {
                $this->getCurrentQuote()->setExpiryEnabled(false);
            }
            if (!isset($data['reminder_enabled'])) {
                $this->getCurrentQuote()->setReminderEnabled(false);
            }
            if (isset($quote['status'])) {
                $newStatus = $quote['status'];
                $status = $this->_statusCollection->getItemByColumnValue('status', $newStatus);
                $state = $status->getState();
                $this->getCurrentQuote()->setState($state);
            }
        }

        /**
         * Set correct currency
         */
        $this->processCurrency();

        /**
         * Initialize catalog rule data
         */
        $this->getCurrentQuote()->initRuleData();

        /**
         * Process addresses
         */
        $this->_processAddresses();

        /**
         * Process shipping
         */
        $this->_processShipping();

        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->getCurrentQuote()->addProduct($productId, $this->getRequest()->getPostValue());
        }

        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->getCurrentQuote()->addProducts($items);
        }

        /**
         * Set Subtotal Proposal
         */
        $this->_setSubtotalProposal();

        if ($this->isDisabledNegativeProfit()) {
            $this->getItemMargin();
        }

        /**
         * Update quote items
         */
        $this->_updateQuoteItems();

        /**
         * Remove quote item
         */
        $this->_removeQuoteItem();

        /**
         * Save payment data
         */
        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->getCurrentQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Process gift message
         */
        $this->_processGiftMessage();

        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }

        if (!empty($couponCode)) {
            $isApplyDiscount = false;
            foreach ($this->getCurrentQuote()->getAllItems() as $item) {
                if (!$item->getNoDiscount()) {
                    $isApplyDiscount = true;
                    break;
                }
            }
            if (!$isApplyDiscount) {
                $this->messageManager->addError(
                    __(
                        '"%1" coupon code was not applied. Do not apply discount is selected for item(s)',
                        $this->escaper->escapeHtml($couponCode)
                    )
                );
            } else {
                if ($this->getCurrentQuote()->getCouponCode() !== $couponCode) {
                    $this->messageManager->addError(
                        __(
                            '"%1" coupon code is not valid.',
                            $this->escaper->escapeHtml($couponCode)
                        )
                    );
                } else {
                    $this->messageManager->addSuccess(__('The coupon code has been accepted.'));
                }
            }
        }

        $eventData = [
            'quote_model' => $this->getCurrentQuote(),
            'request' => $this->getRequest()->getPostValue(),
        ];
        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data', $eventData);

        $this->getCurrentQuote()->saveQuote();

        return $this;
    }

    /**
     * Function Process the quote addresses
     */
    protected function _processAddresses()
    {
        /**
         * init first billing address, need for virtual products
         */
        $this->getCurrentQuote()->getBillingAddress();

        /**
         * Flag for using billing address for shipping
         */
        if (!$this->getCurrentQuote()->isVirtual()) {
            $syncFlag = $this->getRequest()->getPost('shipping_as_billing');
            $shippingMethod = $this->getCurrentQuote()->getShippingAddress()->getShippingMethod();
            if ($syncFlag === null
                && $this->getCurrentQuote()->getShippingAddress()->getSameAsBilling() && empty($shippingMethod)
            ) {
                $this->getCurrentQuote()->setShippingAsBilling(1);
            } else {
                $this->getCurrentQuote()->setShippingAsBilling((int)$syncFlag);
            }
        }
    }

    /**
     * Function Process the quote shipping method
     */
    protected function _processShipping()
    {
        /**
         * Change shipping address flag
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')) {
            $this->getCurrentQuote()->resetShippingMethod();
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')) {
            $this->getCurrentQuote()->save();
            $this->getCurrentQuote()->collectShippingRates();
        }
    }

    /**
     * Process buyRequest file options of items
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /** @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }

        return $items;
    }

    /**
     * Update the quote items based on the data provided in the post data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _updateQuoteItems()
    {
        if ($this->getRequest()->getPost('update_items')) {
            $this->quoteCreate->setQuote($this->getCurrentQuote());
            $items = $this->getRequest()->getPost('item', []);
            $items = $this->_processFiles($items);

            $this->quoteCreate->updateQuoteItems($items);
            $this->quoteCreate->updateTierItems($items);

            $this->getCurrentQuote()->updateBaseCustomPrice();

            if ($this->getRequest()->getPost('remove_items')) {
                foreach ($items as $key => $item) {
                    if ($item['action'] == 'remove') {
                        $this->getCurrentQuote()->removeItem($key);
                    }
                }
            }
        }
    }


    protected function isDisabledNegativeProfit()
    {
        return $this->scopeConfig->getValue('cart2quote_advanced/negativeprofit/disable_negative_profit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }


    /**
     * Set the currency, collected from the post data, on the quote.
     *
     * @return $this;
     */
    protected function processCurrency()
    {
        if ($currency = $this->getRequest()->getPost('currency_id')) {
            if ($currency != $this->getCurrentQuote()->getQuoteCurrency()->getCode()) {
                if ($currency == "false") {
                    $this->getCurrentQuote()->setQuoteCurrencyCode(
                        $this->getCurrentQuote()->getBaseCurrency()->getCode()
                    );
                } else {
                    $this->getCurrentQuote()->setQuoteCurrencyCode($currency);
                }

                $this->getCurrentQuote()->setBaseToQuoteRate(
                    $this->getCurrentQuote()->getBaseCurrency()->getRate($currency)
                );
                $this->getCurrentQuote()->resetQuoteCurrency();
            }
        }

        return $this;
    }

    /**
     * Remove a quote item based on the post data
     */
    protected function _removeQuoteItem()
    {
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getCurrentQuote()->removeItem($removeItemId);
        }
    }

    /**
     * Sets the proposal subtotal
     */
    protected function _setSubtotalProposal()
    {
        $proposal = $this->getRequest()->getPost('proposal');
        if (isset($proposal) && isset($proposal['subtotal_proposal'])) {
            if (isset($proposal['proposal_is_percentage']) && $proposal['proposal_is_percentage'] === 'true') {
                $isPercentage = true;
            } else {
                $isPercentage = false;
            }
            $amount = (float)$proposal['subtotal_proposal'];
            $this->getCurrentQuote()->setSubtotalProposal($amount, $isPercentage);
        }
    }

    /**
     * Trigers the giftmessage methods
     * @return mixed
     */
    protected function _processGiftMessage()
    {
        /**
         * Saving of giftmessages
         */
        $this->_saveGiftMessage();

        /**
         * Importing gift message allow items from specific product grid
         */
        $data = $this->_importGiftMessageAllowQuoteItemsFromProducts();

        /**
         * Importing gift message allow items on update quote items
         */
        $this->_importGiftMessageAllowQuoteItemsFromItems();

        return $data;
    }

    /**
     * Saves Gift message
     */
    protected function _saveGiftMessage()
    {
        $giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }
    }

    /**
     * Retrieve gift message save model
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get('Magento\GiftMessage\Model\Save');
    }

    /**
     * importAllowQuoteItemsFromProducts
     * @return mixed
     */
    protected function _importGiftMessageAllowQuoteItemsFromProducts()
    {
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonDecode($data)
            );

            return $data;
        }

        return $data;
    }

    /**
     * importAllowQuoteItemsFromItems
     */
    protected function _importGiftMessageAllowQuoteItemsFromItems()
    {
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }
    }

    /**
     * @return $this
     */
    protected function _reloadQuote()
    {
        $this->_currentQuote = $this->quoteFactory->create()->load($this->getCurrentQuote()->getId());
        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentQuote);

        return $this;
    }


    /**
     * @throws \Exception
     */
    public function getItemMargin()
    {
        $items = $this->getRequest()->getPost('item', []);
        $quoteItems = $this->getCurrentQuote()->getAllVisibleItems();
        foreach ($items as $itemId => $item) {
            $price = $item['tier_item'][$item['selected_tier']]['custom_price'];
            $cost = $this->getCurrentQuote()->getItemById($itemId)->getProduct()->getCost();
            if ($price > 0) {
                                /**
                 * If cost is not known, calculate the Gross Profit Margin compared to the original price.
                 */
                if ($cost == null) {
                    $cost = $this->getCurrentQuote()->getItemById($itemId)->getProduct()->getPrice();
                }

            }
            if ($price < $cost) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Negative Profit is not allowed on a Quote'));

            }
        }
    }
}


