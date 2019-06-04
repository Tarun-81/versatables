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

namespace Cart2Quote\Quotation\Controller\Quote\Checkout;

/**
 * Class DefaultCheckout
 * @package Cart2Quote\Quotation\Controller\Quote\Checkout
 */
abstract class DefaultCheckout extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;
    /**
     * Mage Quote Factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $mageQuoteFactory;
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;
    /**
     * Customer Session
     *
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * Quotation Quote
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $quote;
    /**
     * Checkout Quote
     *
     * @var \Magento\Quote\Model\Quote
     */
    protected $checkoutQuote;
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender
     */
    protected $quoteProposalAcceptedSender;

    /**
     * DefaultCheckout constructor.
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender $quoteProposalAcceptedSender
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender $quoteProposalAcceptedSender,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory,
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->quoteRepository = $quoteRepository;
        $this->checkoutSession = $checkoutSession;
        $this->mageQuoteFactory = $mageQuoteFactory;

        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory
        );
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteProposalAcceptedSender = $quoteProposalAcceptedSender;
    }

    /**
     * Checks if auto login is allowed
     *
     * @return bool
     */
    public function isAutoLogin()
    {
        return $this->helper->isAutoLoginEnabled();
    }

    /**
     * Checks if auto login is allowed
     *
     * @return bool
     */
    public function isAutoConfirm()
    {
        return $this->helper->isAutoConfirmProposalEnabled();
    }

    /**
     * Proceed to checkout
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function proceedToCheckout($guest = false)
    {
        $this->deletePreviousAcceptedQuotes();
        $this->initCheckoutQuote();
        $this->prepareQuotationQuote();
        $this->beforeSetCheckoutQuote();
        $this->saveCheckoutQuoteAsQuotationQuote();
        $this->quoteProposalAcceptedSender->send($this->quote);

        if ($guest) {
            $this->useGuestCheckout();
        }

        $this->processShipping();
        $this->deleteCurrentCheckoutSessionQuote();
        $this->placeCheckoutQuote();
        $this->helper->setActiveConfirmMode(true);

        return $this->redirectToCheckout();
    }

    /**
     * Proceed to checkout
     *
     * @param bool $guest
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function proceedToAcceptQuotation($guest = false)
    {
        $this->prepareQuotationQuote();
        $this->quoteProposalAcceptedSender->send($this->quote);
        $quoteAcceptMessage = __('Thank you for accepting our offer. We will contact you shortly.');
        $this->messageManager->addSuccessMessage($quoteAcceptMessage);
        $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);

        return $this->resultRedirectFactory->create()->setUrl($url);
    }

    /**
     * Initialize the checkout quote
     *
     * @return $this
     */
    protected function initCheckoutQuote()
    {
        if (!$this->checkoutQuote instanceof \Magento\Quote\Model\Quote) {
            $this->checkoutQuote = $this->mageQuoteFactory->create();
            $this->checkoutQuote->setLinkedQuotationId($this->quote->getId());
            $this->checkoutQuote->setStoreId($this->getStoreId());
            $this->checkoutQuote->save();
        }

        return $this;
    }

    /**
     * Get store id
     *
     * @return int
     */
    protected function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * Prepare the quotation quote:
     *  Set state to complete
     *  Set status to accepted
     *  Set link to order
     *
     * @return $this
     */
    protected function prepareQuotationQuote()
    {
        $customerId = $this->quote->getCustomerId();
        if ($customerId) {
            $quotes = $this->quoteCollectionFactory->create();

            $quotes->addFieldToFilter('customer_id', $customerId);
            $quotes->addFieldToFilter('is_active', 1);
            $quotes->addFieldToFilter('is_quotation_quote', 0);
            foreach ($quotes as $quote) {
                $quote->setIsActive(false)->save();
            }
        }
        $this->quote
            ->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_COMPLETED)
            ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_ACCEPTED)
            ->save();

        return $this;
    }

    /**
     * Delete previously accepted quotes if they have same linked quotation id
     *
     */
    protected function deletePreviousAcceptedQuotes()
    {
        $quotationId = $this->quote->getId();
        $customerId = $this->quote->getCustomerId();
        if ($customerId && $quotationId > 0) {
            $quotes = $this->quoteCollectionFactory->create();

            $quotes->addFieldToFilter('customer_id', $customerId);
            $quotes->addFieldToFilter('linked_quotation_id', $quotationId);
            foreach ($quotes as $quote) {
                $this->quoteRepository->delete($quote);
            }
        }
    }

    /**
     * Prepare the checkout quote
     * Further configuration of a new Quote and making it as a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    protected function beforeSetCheckoutQuote()
    {
        $this->checkoutQuote->setIsActive(true);
        $this->checkoutQuote->getBillingAddress();
        $this->checkoutQuote->getShippingAddress();
        $this->checkoutQuote->setCustomer($this->quote->getCustomer());
        $this->checkoutQuote->setTotalsCollectedFlag(false);

        return $this;
    }

    /**
     * Prepare the checkout quote and save it as quotation quote.
     * Transform a new Quote object into a copy of approved & accepted C2Q_Quote object
     *
     * @return $this
     */
    protected function saveCheckoutQuoteAsQuotationQuote()
    {
        $this->checkoutQuote->merge($this->quote)->collectTotals();
        $this->quoteRepository->save($this->checkoutQuote);

        return $this;
    }

    /**
     * Use the checkout quote as guest checkout
     *
     * @return $this
     */
    protected function useGuestCheckout()
    {
        $this->checkoutQuote->setCheckoutMethod(\Magento\Checkout\Model\Type\Onepage::METHOD_GUEST);
        $this->processGuestCustomerData();

        return $this;
    }

    /**
     * Process the customer data from the quotation quote to the checkout quote.
     * Default copy functions do not copy this data.
     *
     * @return $this
     */
    protected function processGuestCustomerData()
    {
        if ($this->quote->getData()) {
            foreach ($this->quote->getData() as $key => $value) {
                $keyExploded = explode('_', $key);

                if ($keyExploded[0] == 'customer') {
                    $this->checkoutQuote->setData($key, $value);
                }
            }
        }

        $this->checkoutQuote->save();

        return $this;
    }

    /**
     * Process shipping
     *
     * @return void
     */
    protected function processShipping()
    {
        if ($this->quote->getShippingAddress()->getShippingMethod() ==
            \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE . '_' .
            \Cart2Quote\Quotation\Model\Carrier\QuotationShipping::CODE
        ) {
            $this->_quotationSession->addConfigData([
                $this->checkoutQuote->getId() => [
                    'fixed_shipping_price' => $this->quote->getFixedShippingPrice()
                ]
            ]);
        }
    }

    /**
     * Replace a current customer quote with it and remove the old one
     * so customer will be able to place an Order with a new one
     *
     * @return $this
     */
    protected function deleteCurrentCheckoutSessionQuote()
    {
        $oldCustomerQuote = $this->checkoutSession->getQuote();
        if ($oldCustomerQuote &&
            $oldCustomerQuote->getId() != $this->quote->getId() &&
            $oldCustomerQuote->getId() != $this->checkoutQuote->getId()
        ) {
            $oldCustomerQuote->setIsActive(false)->save();
            $this->quoteRepository->delete($oldCustomerQuote);
        }

        return $this;
    }

    /**
     * Place the checkout quote in the checkout session
     *
     * @return $this
     */
    protected function placeCheckoutQuote()
    {
        $this->checkoutSession->setQuoteId($this->checkoutQuote->getId());
        $this->checkoutSession->replaceQuote($this->checkoutQuote);
        $this->checkoutSession->setQuotationQuoteId($this->checkoutQuote->getId());

        return $this;
    }

    /**
     * Redirect to checkout
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function redirectToCheckout()
    {
        return $this->resultRedirectFactory->create()->setPath('checkout');
    }

    /**
     * Initialize the quotation quote
     *
     * @return $this
     */
    protected function initQuote()
    {
        if (!$this->quote instanceof \Cart2Quote\Quotation\Model\Quote) {
            $quoteId = (int)$this->getRequest()->getParam('quote_id', false);
            $this->quote = $this->_quoteFactory->create()->load($quoteId);
        }

        return $this;
    }

    /**
     * Check if the hash is valid
     *
     * @return bool
     */
    protected function hasValidHash()
    {
        $validHash = false;
        $hash = $this->getRequest()->getParam('hash', false);

        if ($hash) {
            $validHash = $this->quote->getUrlHash() == $hash;
        }

        return $validHash;
    }

    /**
     * Login by customer id set on the quote
     *
     * @return $this
     */
    protected function autoLogin()
    {
        if ($this->quote->getCustomerId() > 0) {
            $this->customerSession->loginById($this->quote->getCustomerId());
        }

        return $this;
    }

    /**
     * Redirect to quote page if the quote exists
     * Redirect to index page if the quote does not exists
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    protected function defaultRedirect()
    {
        if ($this->quote) {
            $this->quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PENDING)->save();

            $url = $this->_url->getUrl('quotation/quote/view', ['quote_id' => $this->quote->getId()]);
        } else {
            $url = $this->_url->getUrl('quotation/quote/index');
        }

        return $this->resultRedirectFactory->create()->setUrl($url);
    }

    /**
     * Checks if a customer is a guest
     *
     * @return bool
     */
    protected function isGuest()
    {
        return (bool)$this->quote->getCustomerIsGuest();
    }

    /**
     * Checks if the customer is the same
     *
     * @return bool
     */
    protected function isSameCustomer()
    {
        return $this->quote->getCustomerId() == $this->cart->getCustomerSession()->getCustomerId();
    }
}
