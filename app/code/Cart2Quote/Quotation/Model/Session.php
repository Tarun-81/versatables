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

namespace Cart2Quote\Quotation\Model;

/**
 * Class Session
 * @package Cart2Quote\Quotation\Model
 */
class Session extends \Magento\Checkout\Model\Session
{

    const QUOTATION_GUEST_FIELD_DATA = 'quotation_guest_field_data';
    const QUOTATION_FIELD_DATA = 'quotation_field_data';
    const QUOTATION_PRODUCT_DATA = 'quotation_product_data';
    const QUOTATION_STORE_CONFIG_DATA = 'quotation_store_config_data';

    protected $quoteResourceModel;

    /**
     * Session constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote $quoteResourceModel
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState,
            $orderFactory,
            $customerSession,
            $quoteRepository,
            $remoteAddress,
            $eventManager,
            $storeManager,
            $customerRepository,
            $quoteIdMaskFactory,
            $quoteFactory
        );
    }

    /**
     * Load data for customer quote and merge with current quote
     * @return $this
     */
    public function loadCustomerQuote()
    {
        if (!$this->_customerSession->getCustomerId()) {
            return $this;
        }

        $this->setQuotationQuote(true);
        $this->_eventManager->dispatch('load_customer_quote_before', ['quotation_session' => $this]);

        try {
            /** @var Quote $quote */
            $quote = $this->quoteFactory->create();
            $quote->setStoreId($this->_storeManager->getStore()->getId());
            $customerQuote = $this->quoteResourceModel->loadByCustomerId(
                $quote,
                $this->_customerSession->getCustomerId()
            );
        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
            $customerQuote = $this->quoteFactory->create();
        }
        if (!isset($customerQuote)) {
            $customerQuote = $this->quoteFactory->create();
        }
        $customerQuote->setStoreId($this->_storeManager->getStore()->getId());

        if ($customerQuote->getId() && $this->getQuoteId() != $customerQuote->getId()) {
            if ($this->getQuoteId()) {
                $this->quoteRepository->save(
                    $customerQuote->merge($this->getQuote())->collectTotals()
                );
            }

            $this->setQuoteId($customerQuote->getId());

            if ($this->_quote) {
                $this->quoteRepository->delete($this->_quote);
            }
            $this->_quote = $customerQuote;
        } else {
            $this->getQuote()->getBillingAddress();
            $this->getQuote()->getShippingAddress();
            $this->getQuote()->setCustomer($this->_customerSession->getCustomerDataObject())
                ->setTotalsCollectedFlag(false)
                ->collectTotals();
            $this->getQuote()->setIsQuotationQuote(true);
            $this->quoteRepository->save($this->getQuote());
            $this->setQuoteId($this->getQuote()->getId());
        }

        $this->setQuotationQuote(false);

        return $this;
    }

    /**
     * Get quotation quote instance by current session
     * @return Quote
     */
    public function getQuote()
    {
        $this->_eventManager->dispatch('quotation_quote_process', ['quotation_session' => $this]);

        if ($this->_quote === null) {
            $quote = $this->quoteFactory->create();
            if ($this->getQuoteId()) {
                try {
                    $quote = $this->quoteRepository->get($this->getQuoteId());

                    /**
                     * If current currency code of quote is not equal current currency code of store,
                     * need recalculate totals of quote. It is possible if customer use currency switcher or
                     * store switcher.
                     */
                    if ($quote->getQuoteCurrencyCode() != $this->_storeManager->getStore()->getCurrentCurrencyCode()) {
                        $quote->setStore($this->_storeManager->getStore());
                        $this->quoteRepository->save($quote->collectTotals());
                        /*
                         * We mast to create new quote object, because collectTotals()
                         * can to create links with other objects.
                         */
                        $quote = $this->quoteRepository->get($this->getQuoteId());
                    }
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $this->setQuoteId(null);
                }
            }

            if (!$this->getQuoteId()) {
                if ($this->_customerSession->isLoggedIn() || $this->_customer) {
                    $this->setQuoteId($quote->getId());
                } else {
                    $quote->setIsQuotationCart(true);
                    $this->_eventManager->dispatch('quotation_quote_init', ['quote' => $quote]);
                }
            }

            if ($this->_customer) {
                $quote->setCustomer($this->_customer);
            } elseif ($this->_customerSession->isLoggedIn()) {
                $quote->setCustomer($this->customerRepository->getById($this->_customerSession->getCustomerId()));
            }

            $quote->setStore($this->_storeManager->getStore());
            $this->_quote = $quote;
        }

        if (!$this->getIsQuoteMasked() && !$this->_customerSession->isLoggedIn() && $this->getQuoteId()) {
            $quoteId = $this->getQuoteId();
            /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($quoteId, 'quote_id');
            if ($quoteIdMask->getMaskedId() === null) {
                $quoteIdMask->setQuoteId($quoteId)->save();
            }
            $this->setIsQuoteMasked(true);
        }

        $remoteAddress = $this->_remoteAddress->getRemoteAddress();
        if ($remoteAddress) {
            $this->_quote->setRemoteIp($remoteAddress);
            $xForwardIp = $this->request->getServer('HTTP_X_FORWARDED_FOR');
            $this->_quote->setXForwardedFor($xForwardIp);
        }

        return $this->_quote;
    }

    /**
     * Clear the Quotation Quote from the session.
     * @return $this
     */
    public function fullSessionClear()
    {
        $this->clearQuote()->clearStorage();

        return $this;
    }

    /**
     * Destroy/end a session
     * Unset all data associated with object
     * @return $this
     */
    public function clearQuote()
    {
        $this->_eventManager->dispatch('quotation_quote_destroy', ['quote' => $this->getQuote()]);
        $this->_quote = null;
        $this->setQuoteId(null);

        return $this;
    }

    /**
     * Update the quote ID's and status on the session.
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     * @return $this
     */
    public function updateLastQuote(\Cart2Quote\Quotation\Model\Quote $quotation)
    {
        $this->setLastQuoteId($quotation->getId())
            ->setLastRealQuoteId($quotation->getIncrementId())
            ->setLastQuoteStatus($quotation->getStatus());

        return $this;
    }

    /**
     * Add config to the session
     * Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addGuestFieldData(array $config)
    {
        $this->addMergeData(self::QUOTATION_GUEST_FIELD_DATA, $config);

        return $this;
    }

    /**
     * Merge Data
     *
     * @param $type
     * @param $newConfig
     * @return $this
     */
    public function addMergeData($type, $newConfig)
    {
        $initialData = $this->getData($type);
        if (is_array($initialData)) {
            $newConfig = $newConfig + $initialData;
        }

        $this->storage->setData($type, $newConfig);

        return $this;
    }

    /**
     * Add config to the session
     * Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addConfigData(array $config)
    {
        $this->addMergeData(self::QUOTATION_STORE_CONFIG_DATA, $config);

        return $this;
    }

    /**
     * Add product data to the session
     * Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addProductData(array $config)
    {
        $this->addMergeData(self::QUOTATION_PRODUCT_DATA, $config);

        return $this;
    }

    /**
     * Add field data to the session
     * Note: This data will be available on the RFQ page in as JSON data
     *
     * @param array $config
     * @return $this
     */
    public function addFieldData(array $config)
    {
        $this->addMergeData(self::QUOTATION_FIELD_DATA, $config);

        return $this;
    }

    /**
     * Set quotation quote to quotation session
     *
     * @param boolean $isQuotationQuote
     */
    public function setQuotationQuote($isQuotationQuote)
    {
        $this->setData("quotation_quote", $isQuotationQuote);
    }

    /**
     * Get quotation quote from quotation session
     *
     * @return boolean|null
     */
    public function getQuotationQuote()
    {
        return $this->getData("quotation_quote");
    }
}
