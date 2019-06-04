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
 * Quote model
 * Supported events:
 *  sales_quote_load_after
 *  sales_quote_save_before
 *  sales_quote_save_after
 *  sales_quote_delete_before
 *  sales_quote_delete_after
 * Class Quote
 * @package Cart2Quote\Quotation\Model
 */
class Quote extends \Magento\Quote\Model\Quote implements
    \Cart2Quote\Quotation\Model\EntityInterface,
    \Magento\Sales\Model\EntityInterface,
    \Cart2Quote\Quotation\Api\Data\QuoteInterface
{
    const ENTITY = 'quote';
    const DEFAULT_EXPIRATION_TIME = 'cart2quote_quotation/global/default_expiration_time';
    const DEFAULT_REMINDER_TIME = 'cart2quote_quotation/global/default_reminder_time';
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config
     */
    protected $_quoteConfig;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory
     */
    protected $_quoteHistoryFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory
     */
    protected $_historyCollectionFactory;
    /**
     * Re-collect quote flag
     * @var boolean
     */
    protected $_needCollect;
    /**
     * Quote session object
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_session;
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var \Magento\Quote\Model\Quote\Item\Updater
     */
    protected $quoteItemUpdater;
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_quoteCurrency;
    /**
     * @var \Magento\Directory\Model\Currency
     */
    protected $_baseCurrency;
    /**
     * @var \Magento\Quote\Model\Cart\CurrencyFactory
     */
    protected $_currencyFactory;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;
    /**
     * @var \Magento\Framework\Stdlib\DateTime
     */
    protected $datetime;
    /**
     * @var Quote\TierItemFactory
     */
    protected $_tierItemFactory;
    /**
     * @var ResourceModel\Quote\TierItem\CollectionFactory
     */
    protected $tierItemCollectionFactory;
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $_quoteFactory;
    /**
     * @var \Magento\Sales\Model\Status
     */
    protected $_statusObject;
    /**
     * @var Quote\Item\Section\Provider
     */
    private $itemSectionProvider;
    /**
     * @var ResourceModel\Quote\Item\Section
     */
    private $itemSectionResourceModel;
    /**
     * @var ResourceModel\Quote\TierItem
     */
    private $tierItemResourceModel;
    /**
     * @var Quote\SectionFactory
     */
    private $sectionFactory;

    /**
     * @var \Magento\CatalogInventory\Model\StockStateProvider
     */
    protected $stockStateProvider;

    /**
     * Quote constructor.
     * @param Quote\SectionFactory $sectionFactory
     * @param ResourceModel\Quote\Item\Section $itemSectionResourceModel
     * @param Quote\Item\Section\Provider $itemSectionProvider
     * @param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
     * @param ResourceModel\Quote\TierItem $tierItemResourceModel
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory $quoteHistoryFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory $historyCollectionFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Directory\Model\CurrencyFactory $directoryCurrencyFactory
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magento\Framework\Stdlib\DateTime $dateTime
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Quote\Model\QuoteValidator $quoteValidator
     * @param \Magento\Catalog\Helper\Product $catalogProduct
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Framework\Message\Factory $messageFactory
     * @param \Magento\Sales\Model\Status\ListFactory $statusListFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory
     * @param \Magento\Framework\DataObject\Copy $objectCopyService
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Quote\Model\Quote\Item\Processor $itemProcessor
     * @param \Magento\Framework\DataObject\Factory $objectFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory
     * @param \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Api\DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param \Magento\Quote\Model\Cart\CurrencyFactory $currencyFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector
     * @param \Magento\Quote\Model\Quote\TotalsReader $totalsReader
     * @param \Magento\Quote\Model\ShippingFactory $shippingFactory
     * @param \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Status $statusObject
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider
     * @param array $data
     * @internal param ResourceModel\Quote\TierItem $tierItem
     */
    public function __construct(
        \Magento\CatalogInventory\Model\StockStateProvider $stockStateProvider,
        \Cart2Quote\Quotation\Model\Quote\SectionFactory $sectionFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Item\Section $itemSectionResourceModel,
        \Cart2Quote\Quotation\Model\Quote\Item\Section\Provider $itemSectionProvider,
        \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem $tierItemResourceModel,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\TierItem\CollectionFactory $tierItemCollectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        \Cart2Quote\Quotation\Model\Quote\Status\HistoryFactory $quoteHistoryFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History\CollectionFactory $historyCollectionFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Quote\Model\Quote\Item\Updater $quoteItemUpdater,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Directory\Model\CurrencyFactory $directoryCurrencyFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magento\Framework\Stdlib\DateTime $dateTime,
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Quote\Model\QuoteValidator $quoteValidator,
        \Magento\Catalog\Helper\Product $catalogProduct,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Quote\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Quote\Model\ResourceModel\Quote\Item\CollectionFactory $quoteItemCollectionFactory,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Framework\Message\Factory $messageFactory,
        \Magento\Sales\Model\Status\ListFactory $statusListFactory,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Quote\Model\Quote\PaymentFactory $quotePaymentFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Payment\CollectionFactory $quotePaymentCollectionFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Quote\Model\Quote\Item\Processor $itemProcessor,
        \Magento\Framework\DataObject\Factory $objectFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Customer\Api\Data\AddressInterfaceFactory $addressDataFactory,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerDataFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\Api\ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        \Magento\Quote\Model\Cart\CurrencyFactory $currencyFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader $totalsReader,
        \Magento\Quote\Model\ShippingFactory $shippingFactory,
        \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory,
        \Cart2Quote\Quotation\Model\Quote\Status $statusObject,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $quoteValidator,
            $catalogProduct,
            $scopeConfig,
            $storeManager,
            $config,
            $quoteAddressFactory,
            $customerFactory,
            $groupRepository,
            $quoteItemCollectionFactory,
            $quoteItemFactory,
            $messageFactory,
            $statusListFactory,
            $productRepository,
            $quotePaymentFactory,
            $quotePaymentCollectionFactory,
            $objectCopyService,
            $stockRegistry,
            $itemProcessor,
            $objectFactory,
            $addressRepository,
            $criteriaBuilder,
            $filterBuilder,
            $addressDataFactory,
            $customerDataFactory,
            $customerRepository,
            $dataObjectHelper,
            $extensibleDataObjectConverter,
            $currencyFactory,
            $extensionAttributesJoinProcessor,
            $totalsCollector,
            $totalsReader,
            $shippingFactory,
            $shippingAssignmentFactory,
            $resource,
            $resourceCollection,
            $data
        );

        $this->timezone = $timezone;
        $this->datetime = $dateTime;
        $this->_quoteConfig = $quoteConfig;
        $this->_quoteHistoryFactory = $quoteHistoryFactory;
        $this->_historyCollectionFactory = $historyCollectionFactory;
        $this->_objectManager = $objectManager;
        $this->_session = $quoteSession;
        $this->_coreRegistry = $coreRegistry;
        $this->messageManager = $messageManager;
        $this->quoteItemUpdater = $quoteItemUpdater;
        $this->quoteRepository = $quoteRepository;
        $this->_quoteFactory = $quoteFactory;
        $this->_currencyFactory = $directoryCurrencyFactory;
        $this->_tierItemFactory = $tierItemFactory;
        $this->_statusObject = $statusObject;
        $this->tierItemCollectionFactory = $tierItemCollectionFactory;
        $this->itemSectionProvider = $itemSectionProvider;
        $this->itemSectionResourceModel = $itemSectionResourceModel;
        $this->tierItemResourceModel = $tierItemResourceModel;
        $this->sectionFactory = $sectionFactory;
        $this->stockStateProvider = $stockStateProvider;
    }

    /**
     * @param bool $sendRequestEmail
     * @return $this
     */
    public function setSendRequestEmail($sendRequestEmail)
    {
        return $this->setData(self::SEND_REQUEST_EMAIL, $sendRequestEmail);
    }

    /**
     * @return $this
     */
    public function getSendRequestEmail()
    {
        return $this->getData(self::SEND_REQUEST_EMAIL);
    }

    /**
     * @param bool $requestEmailSent
     * @return $this
     */
    public function setRequestEmailSent($requestEmailSent)
    {
        return $this->setData(self::REQUEST_EMAIL_SENT, $requestEmailSent);
    }

    /**
     * @return $this
     */
    public function getRequestEmailSent()
    {
        return $this->getData(self::REQUEST_EMAIL_SENT);
    }

    /**
     * @param bool $sendQuoteCanceledEmail
     * @return $this
     */
    public function setSendQuoteCanceledEmail($sendQuoteCanceledEmail)
    {
        return $this->setData(self::SEND_QUOTE_CANCELED_EMAIL, $sendQuoteCanceledEmail);
    }

    /**
     * @return $this
     */
    public function getSendQuoteCanceledEmail()
    {
        return $this->getData(self::SEND_QUOTE_CANCELED_EMAIL);
    }

    /**
     * @param bool $quoteCanceledEmailSent
     * @return $this
     */
    public function setQuoteCanceledEmailSent($quoteCanceledEmailSent)
    {
        return $this->setData(self::QUOTE_CANCELED_EMAIL_SENT, $quoteCanceledEmailSent);
    }

    /**
     * @return $this
     */
    public function getQuoteCanceledEmailSent()
    {
        return $this->getData(self::QUOTE_CANCELED_EMAIL_SENT);
    }

    /**
     * @param bool $sendProposalAcceptedEmail
     * @return $this
     */
    public function setSendProposalAcceptedEmail($sendProposalAcceptedEmail)
    {
        return $this->setData(self::SEND_PROPOSAL_ACCEPTED_EMAIL, $sendProposalAcceptedEmail);
    }

    /**
     * @return $this
     */
    public function getSendProposalAcceptedEmail()
    {
        return $this->getData(self::SEND_PROPOSAL_ACCEPTED_EMAIL);
    }

    /**
     * @param bool $proposalAcceptedEmailSent
     * @return $this
     */
    public function setProposalAcceptedEmailSent($proposalAcceptedEmailSent)
    {
        return $this->setData(self::PROPOSAL_ACCEPTED_EMAIL_SENT, $proposalAcceptedEmailSent);
    }

    /**
     * @return $this
     */
    public function getProposalAcceptedEmailSent()
    {
        return $this->getData(self::PROPOSAL_ACCEPTED_EMAIL_SENT);
    }

    /**
     * @param bool $sendProposalExpiredEmail
     * @return $this
     */
    public function setSendProposalExpiredEmail($sendProposalExpiredEmail)
    {
        return $this->setData(self::SEND_PROPOSAL_EXPIRED_EMAIL, $sendProposalExpiredEmail);
    }

    /**
     * @return $this
     */
    public function getSendProposalExpiredEmail()
    {
        return $this->getData(self::SEND_PROPOSAL_EXPIRED_EMAIL);
    }

    /**
     * @param bool $proposalExpiredEmailSent
     * @return $this
     */
    public function setProposalExpiredEmailSent($proposalExpiredEmailSent)
    {
        return $this->setData(self::PROPOSAL_EXPIRED_EMAIL_SENT, $proposalExpiredEmailSent);
    }

    /**
     * @return $this
     */
    public function getProposalExpiredEmailSent()
    {
        return $this->getData(self::PROPOSAL_EXPIRED_EMAIL_SENT);
    }

    /**
     * @param bool $sendProposalEmail
     * @return $this
     */
    public function setSendProposalEmail($sendProposalEmail)
    {
        return $this->setData(self::SEND_PROPOSAL_EMAIL, $sendProposalEmail);
    }

    /**
     * @return $this
     */
    public function getSendProposalEmail()
    {
        return $this->getData(self::SEND_PROPOSAL_EMAIL);
    }

    /**
     * @param bool $proposalEmailSent
     * @return $this
     */
    public function setProposalEmailSent($proposalEmailSent)
    {
        return $this->setData(self::PROPOSAL_EMAIL_SENT, $proposalEmailSent);
    }

    /**
     * @return $this
     */
    public function getProposalEmailSent()
    {
        return $this->getData(self::PROPOSAL_EMAIL_SENT);
    }

    /**
     * @param bool $sendReminderEmail
     * @return $this
     */
    public function setSendReminderEmail($sendReminderEmail)
    {
        return $this->setData(self::SEND_REMINDER_EMAIL, $sendReminderEmail);
    }

    /**
     * @return $this
     */
    public function getSendReminderEmail()
    {
        return $this->getData(self::SEND_REMINDER_EMAIL);
    }

    /**
     * @param bool $reminderEmailSent
     * @return $this
     */
    public function setReminderEmailSent($reminderEmailSent)
    {
        return $this->setData(self::REMINDER_EMAIL_SENT, $reminderEmailSent);
    }

    /**
     * @return $this
     */
    public function getReminderEmailSent()
    {
        return $this->getData(self::REMINDER_EMAIL_SENT);
    }

    /**
     * @return string
     */
    public function getProposalSent()
    {
        return $this->getData(self::PROPOSAL_SENT);
    }

    /**
     * Get fixed shipping price
     *
     * @return float
     */
    public function getFixedShippingPrice()
    {
        return $this->getData(self::FIXED_SHIPPING_PRICE);
    }

    /**
     * Set fixed shipping price
     *
     * @param float $fixedShippingPrice
     * @return $this
     */
    public function setFixedShippingPrice($fixedShippingPrice)
    {
        return $this->setData(self::FIXED_SHIPPING_PRICE, $fixedShippingPrice);
    }


    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @return $this
     */
    public function create(\Magento\Quote\Model\Quote $quote)
    {
        if (!$quote->getId()) {
            $quote->getResource()->save($quote);
            $this->isObjectNew(true);
        }

        $this->setQuoteId($quote->getId());
        $this->addData($quote->getData());
        $this->setIsQuotationQuote(true);
        $this->setStoreId($quote->getStoreId());
        $this->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN)->setStatus($this->getConfig()->getStateDefaultStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN));
        $this->setOriginalSubtotal($this->getSubtotal());
        $this->setBaseOriginalSubtotal($this->getBaseSubtotal());

        if (!$quote->getCustomerIsGuest()) {
            $this->setCustomer($quote->getCustomer());
        }
        $this->setBillingAddress($quote->getBillingAddress());
        $this->setShippingAddress($quote->getShippingAddress());

        $this->setExpiryDate($this->getDefaultExpiryDate());
        $this->setExpiryEmail(true);
        $this->setReminderDate($this->getDefaultReminderDate());

        $defaultReminderTime = $this->_scopeConfig->getValue(
            self::DEFAULT_REMINDER_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );

        if ($defaultReminderTime == 0) {
            $this->setReminderEnabled(false);
        } else {
            $this->setReminderEnabled(true);
        }

        /**
         * The first save is needed to create tier items to the database.
         * RecollectQuote function needs these tier items to calculate the totals.
         * We need the second save to save the totals to database
         */
        $this->save();
        $this->setRecollect(true);
        $this->recollectQuote();
        $this->save();

        return $this;
    }

    /**
     * Sets the status for the quote.
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * Sets the state for the quote.
     * @param string $state
     * @return $this
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * Retrieve quote configuration model
     * @return Quote\Config
     */
    public function getConfig()
    {
        return $this->_quoteConfig;
    }

    /**
     * @param $originalSubtotal
     * @return $this
     */
    public function setOriginalSubtotal($originalSubtotal)
    {
        $this->setData(self::ORIGINAL_SUBTOTAL, $originalSubtotal);

        return $this;
    }

    /**
     * Set Base Original Subtotal
     *
     * @param float $originalBaseSubtotal
     * @return $this
     */
    public function setBaseOriginalSubtotal($originalBaseSubtotal)
    {
        $this->setData(self::ORIGINAL_BASE_SUBTOTAL, $originalBaseSubtotal);

        return $this;
    }

    /**
     * Get default expiry date of quote
     * @return date
     */
    public function getDefaultExpiryDate()
    {
        $defaultExpiryTime = $this->_scopeConfig->getValue(
            self::DEFAULT_EXPIRATION_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );

        if ($defaultExpiryTime == null) {
            $defaultExpiryTime = 7; // days
        }

        $expiryDate = strtotime("+" . $defaultExpiryTime . " day");

        return $this->datetime->gmDate('Y-m-d', $expiryDate);
    }

    /**
     * Get default reminder date of quote
     * @return date
     */
    public function getDefaultReminderDate()
    {
        $defaultReminderTime = $this->_scopeConfig->getValue(
            self::DEFAULT_REMINDER_TIME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $this->getStore()
        );

        if ($defaultReminderTime == null) {
            $defaultReminderTime = 3; // days
        }

        $reminderDate = strtotime("+" . $defaultReminderTime . " day");

        return $this->datetime->gmDate('Y-m-d', $reminderDate);
    }

    /**
     * Save quote data
     * @return $this
     * @throws \Exception
     */
    public function save()
    {
        /** @var \Magento\Quote\Model\Quote $quote */

        //save quotation quote
        $this->_getResource()->save($this);

        //save quote quote
        $quote = $this->_objectManager->create('Magento\Quote\Model\Quote')->load($this->getId());
        $quote->addData($this->getData());
        $quote->_getResource()->save($quote);

        return $this;
    }

    /**
     * Set collect totals flag for quote
     * @param   bool $flag
     * @return $this
     */
    public function setRecollect($flag)
    {
        $this->_needCollect = $flag;

        return $this;
    }

    /**
     * Recollect totals for customer cart.
     * Set recollect totals flag for quote
     * @return $this
     */
    public function recollectQuote()
    {
        if ($this->_needCollect === true) {
            $this->collectTotals();

            /**
             * Set Original Subtotal
             */
            $this->recalculateOriginalSubtotal();

            /**
             * Set Custom Price Total
             */
            $this->recalculateCustomPriceTotal();

            /**
             * Set Quote adjustment total
             */
            $this->recalculateQuoteAdjustmentTotal();
        }
        $this->setRecollect(false);

        return $this;
    }

    /**
     * Function that recalculates the new original subtotal
     *
     * @return $this
     */
    public function recalculateOriginalSubtotal()
    {
        $newOriginalSubtotal = 0;
        $newBaseOriginalSubtotal = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getCurrentTierItem()) {
                $price = $item->getCurrentTierItem()->getOriginalPrice();
                $basePrice = $item->getCurrentTierItem()->getBaseOriginalPrice();
            } else {
                $price = $this->convertPriceToQuoteCurrency($item->getProduct()->getPrice());
                $basePrice = $item->getProduct()->getPrice();
            }

            $newOriginalSubtotal += (($item->getQty() * $price) * 1);
            $newBaseOriginalSubtotal += (($item->getQty() * $basePrice) * 1);
        }

        $this->setOriginalSubtotal($newOriginalSubtotal);
        $this->setBaseOriginalSubtotal($newBaseOriginalSubtotal);

        return $this;
    }

    /**
     * Concert a price to the quote rate price
     * Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param $price
     * @return double
     */
    public function convertPriceToQuoteCurrency($price)
    {
        if ($this->isCurrencyDifferent()) {
            $price = $this->convertRate($price, $this->getBaseToQuoteRate(), false);
        }

        return $price;
    }

    /**
     * @return bool
     */
    public function isCurrencyDifferent()
    {
        $quoteCurrency = $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_QUOTE_CURRENCY_CODE);
        $baseCurrency = $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_BASE_CURRENCY_CODE);

        return $quoteCurrency != $baseCurrency;
    }

    /**
     * Convert the rate of a price
     *
     * Todo: consider refactoring this to a helper
     * @param $price
     * @param $rate
     * @param bool $base
     * @return double
     */
    public static function convertRate($price, $rate, $base = false)
    {
        if ($base) {
            $price = (double)$price / $rate;
        } elseif (!$base) {
            $price = (double)$price * $rate;
        }

        return $price;
    }

    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    public function recalculateCustomPriceTotal()
    {
        $customPriceTotal = 0;
        $baseCustomPriceTotal = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $itemCustomPrice = $item->getCustomPrice();
            $itemBaseCustomPrice = $item->getBaseCustomPrice();

            if ($itemCustomPrice <= 0) {
                $itemCustomPrice = $this->convertPriceToQuoteCurrency($item->getProduct()->getPrice());
            }

            if ($itemBaseCustomPrice <= 0) {
                $itemBaseCustomPrice = $item->getPrice();
            }

            $customPriceTotal += $itemCustomPrice * $item->getQty();
            $baseCustomPriceTotal += $itemBaseCustomPrice * $item->getQty();
        }

        $this->setCustomPriceTotal($customPriceTotal);
        $this->setBaseCustomPriceTotal($baseCustomPriceTotal);

        return $this;
    }

    /**
     * Set Custom Price Total
     *
     * @param float $customPriceTotal
     * @return $this
     */
    public function setCustomPriceTotal($customPriceTotal)
    {
        $this->setData(self::CUSTOM_PRICE_TOTAL, $customPriceTotal);

        return $this;
    }

    /**
     * Set Base Custom Price Total
     *
     * @param float $baseCustomPriceTotal
     * @return $this
     */
    public function setBaseCustomPriceTotal($baseCustomPriceTotal)
    {
        $this->setData(self::BASE_CUSTOM_PRICE_TOTAL, $baseCustomPriceTotal);

        return $this;
    }

    /**
     * Function that recalculates the new custom price total
     *
     * @return $this
     */
    public function recalculateQuoteAdjustmentTotal()
    {
        $quoteAdjustment = (double)$this->getBaseSubtotal() - (double)$this->getBaseOriginalSubtotal();
        $baseQuoteAdjustment = (double)$this->getSubtotal() - (double)$this->getOriginalSubtotal();

        $this->setQuoteAdjustment($quoteAdjustment);
        $this->setBaseQuoteAdjustment($baseQuoteAdjustment);

        return $this;
    }

    /**
     * Get Base Original Subtotal
     *
     * @return float
     */
    public function getBaseOriginalSubtotal()
    {
        return $this->getData(self::ORIGINAL_BASE_SUBTOTAL);
    }

    /**
     * @return mixed
     */
    public function getOriginalSubtotal()
    {
        return $this->getData(self::ORIGINAL_SUBTOTAL);
    }

    /**
     * Set Quote Adjustment
     *
     * @param float $quoteAdjustment
     * @return $this
     */
    public function setQuoteAdjustment($quoteAdjustment)
    {
        $this->setData(self::QUOTE_ADJUSTMENT, $quoteAdjustment);

        return $this;
    }

    /**
     * Set Base Quote Adjustment
     *
     * @param float $baseQuoteAdjustment
     * @return $this
     */
    public function setBaseQuoteAdjustment($baseQuoteAdjustment)
    {
        $this->setData(self::BASE_QUOTE_ADJUSTMENT, $baseQuoteAdjustment);

        return $this;
    }

    /**
     * Remove tier item
     * @param \Magento\Quote\Model\Quote\Item $item
     * @param $qty
     * @return $this
     */
    public function removeTier(\Magento\Quote\Model\Quote\Item $item, $qty)
    {
        //Cannot remove current tier(qty)
        if (!$item->getCurrentTier()->isSelected()
            && $this->tierItemCollectionFactory->create()->tierExists(
                $item->getQty(),
                $qty
            )
        ) {
            return $this->getTier($item, $qty)->delete();
        }

        return $this;
    }

    /**
     * Retrieve quote edit availability
     * @return bool
     */
    public function canEdit()
    {
        return true;
    }

    /**
     * Retrieve quote cancel availability
     * @return bool
     */
    public function canCancel()
    {
        return true;
    }

    /**
     * Check whether quote is canceled
     * @return bool
     */
    public function isCanceled()
    {
        return false;
    }

    /**
     * Retrieve quote hold availability
     * @return bool
     */
    public function canHold()
    {
        return true;
    }

    /**
     * Retrieve quote unhold availability
     * @return bool
     */
    public function canUnhold()
    {
        return false;
    }

    /**
     * Check if comment can be added to quote history
     * @return bool
     */
    public function canComment()
    {
        return true;
    }

    /*********************** STATUSES ***************************/

    public function canChangeRequest()
    {
        return true;
    }

    /**
     * Return array of quote status history items without deleted.
     * @return array
     */
    public function getAllStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] = $status;
            }
        }

        return $history;
    }

    /**
     * Return collection of quote status history items.
     * @return HistoryCollection
     */
    public function getStatusHistoryCollection()
    {
        $collection = $this->_historyCollectionFactory->create()->setQuoteFilter($this)
            ->setOrder('created_at', 'desc')
            ->setOrder('entity_id', 'desc');
        if ($this->getId()) {
            foreach ($collection as $status) {
                $status->setQuote($this);
            }
        }

        return $collection;
    }

    /**
     * Return collection of visible on frontend quote status history items.
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = [];
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] = $status;
            }
        }

        return $history;
    }

    /**
     * @param mixed $statusId
     * @return string|false
     */
    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId() == $statusId) {
                return $status;
            }
        }

        return false;
    }

    /**
     * Set the quote status history object and the quote object to each other
     * Adds the object to the status history collection, which is automatically saved when the quote is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     * @param \Cart2Quote\Quotation\Model\Quote\Status\History $history
     * @return $this
     */
    public function addStatusHistory(\Cart2Quote\Quotation\Model\Quote\Status\History $history)
    {
        $history->setQuote($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->setStatusHistories(array_merge($this->getStatusHistories(), [$history]));
            $this->setDataChanges(true);
        }

        return $this;
    }

    /**
     * Quote saving
     * @return $this
     */
    public function saveQuote()
    {
        if (!$this->getId()) {
            return $this;
        }

        $this->recollectQuote();
        $this->save();
        $this->quoteRepository->save($this);

        return $this;
    }

    /**
     * Parse data retrieved from request
     * @param   array $data
     * @return  $this
     */
    public function importPostData($data)
    {
        if (is_array($data)) {
            $this->addData($data);
        } else {
            return $this;
        }

        if (isset($data['comment'])) {
            $this->addData($data['comment']);
            if (empty($data['comment']['customer_note_notify'])) {
                $this->setCustomerNoteNotify(false);
            } else {
                $this->setCustomerNoteNotify(true);
            }
        }

        if (isset($data['billing_address'])) {
            $this->setBillingAddress($data['billing_address']);
        }

        if (isset($data['shipping_address'])) {
            $this->setShippingAddress($data['shipping_address']);
        }

        if (isset($data['shipping_method'])) {
            $this->setShippingMethod($data['shipping_method']);
        }

        if (isset($data['payment_method'])) {
            $this->setPaymentMethod($data['payment_method']);
        }

        if (isset($data['coupon']['code'])) {
            $this->applyCoupon($data['coupon']['code']);
        }

        return $this;
    }

    /**
     * Set shipping method
     * @param string $method
     * @return $this
     */
    public function setShippingMethod($method)
    {
        $this->getShippingAddress()->setShippingMethod($method);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Set payment method into quote
     * @param string $method
     * @return $this
     */
    public function setPaymentMethod($method)
    {
        $this->getPayment()->setMethod($method);

        return $this;
    }

    /**
     * Add coupon code to the quote
     * @param string $code
     * @return $this
     */
    public function applyCoupon($code)
    {
        $code = trim((string)$code);
        $this->setCouponCode($code);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Empty shipping method and clear shipping rates
     * @return $this
     */
    public function resetShippingMethod()
    {
        $this->getShippingAddress()->setShippingMethod(false);
        $this->getShippingAddress()->removeAllShippingRates();

        return $this;
    }

    /**
     * Collect shipping data for quote shipping address
     * @return $this
     */
    public function collectShippingRates()
    {
        $this->getShippingAddress()->setCollectShippingRates(true);

        //make sure that the country id is set before the collection, so that we always have a shipping rate
        if (!$this->getShippingAddress()->getCountryId()) {
            $this->getShippingAddress()->setCountryId(
                $this->_scopeConfig->getValue(
                    \Magento\Sales\Model\Order\Shipment::XML_PATH_STORE_COUNTRY_ID,
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                    $this->getStore()
                )
            );
        }

        $this->collectRates();

        return $this;
    }

    /**
     * Calculate totals
     * @return void
     */
    public function collectRates()
    {
        $this->collectTotals();
    }

    /**
     * Set payment data into quote
     * @param array $data
     * @return $this
     */
    public function setPaymentData($data)
    {
        if (!isset($data['method'])) {
            $data['method'] = $this->getPayment()->getMethod();
        }
        $this->getPayment()->importData($data);

        return $this;
    }

    /**
     * Initialize data for price rules
     * @return $this
     */
    public function initRuleData()
    {
        $this->_coreRegistry->register(
            'rule_data',
            new \Magento\Framework\DataObject(
                [
                    'store_id' => $this->_session->getStore()->getId(),
                    'website_id' => $this->_session->getStore()->getWebsiteId(),
                    'customer_group_id' => $this->getCustomerGroupId(),
                ]
            )
        );

        return $this;
    }

    /**
     * Set shipping anddress to be same as billing
     * @param bool $flag If true - don't save in address book and actually copy data across billing and shipping
     *                   addresses
     * @return $this
     */
    public function setShippingAsBilling($flag)
    {
        if ($flag) {
            $tmpAddress = clone $this->getBillingAddress();
            $tmpAddress->unsAddressId()->unsAddressType();
            $data = $tmpAddress->getData();
            $data['save_in_address_book'] = 0;
            // Do not duplicate address (billing address will do saving too)
            $this->getShippingAddress()->addData($data);
        }
        $this->getShippingAddress()->setSameAsBilling($flag);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Add multiple products to current quotation quote
     *
     * @param array $products
     * @return $this
     */
    public function addProducts(array $products)
    {
        foreach ($products as $productId => $config) {
            $config['qty'] = isset($config['qty']) ? (double)$config['qty'] : 1;
            try {
                if (is_numeric($config)) {
                    $config = $this->objectFactory->create(['qty' => $config]);
                }
                if (is_array($config)) {
                    $config = $this->objectFactory->create($config);
                }
                $product = $this->productRepository->getById($productId);
                $this->checkProduct($product, $config);
                $quoteItem = $this->addProduct($product, $config);
                $quoteItem->getResource()->save($quoteItem);
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                return $e;
            }
        }

        return $this;
    }

    /**
     * Check configurable product type stock quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkConfigurableProduct($product, $config)
    {
        if (isset($config['super_attribute'])) {
            $childProduct = $product->getTypeInstance(true)->getProductByAttributes($config['super_attribute'], $product);
            $this->checkQuantity($childProduct, $config['qty']);
        }
    }

    /**
     * Check bundle product type stock quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkBundleProduct($product, $config)
    {
        $selectionCollection = $product->getTypeInstance(true)->getSelectionsCollection(
            $config['bundle_option'],
            $product
        );

        $bundleOptions = array_flip($config['bundle_option']);
        $bundleQtyOptions = $config['bundle_option_qty'];
        foreach ($selectionCollection as $item) {
            if (isset($bundleOptions[$item->getOptionId()])) {
                $optionId = $bundleOptions[$item->getOptionId()];
                if (isset($bundleQtyOptions[$optionId])) {
                    $qty = $bundleQtyOptions[$optionId];
                    $quantity = $qty * $config['qty'];
                    $this->checkQuantity($item, $quantity);
                }
            }
        }
    }

    /**
     * Check grouped product type stock quantity
     *
     * @param \Magento\Framework\DataObject $config
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkGroupedProduct($config)
    {
        if (isset($config['super_group'])) {
            foreach ($config['super_group'] as $productId => $qty) {
                if ($qty > 0) {
                    $product = $this->productRepository->getById($productId);
                    $this->checkQuantity($product, $qty);
                }
            }
        }
    }

    /**
     * Check different product types stock quantities
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Framework\DataObject $config
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkProduct($product, $config)
    {
        switch ($product->getTypeId()) {
            case \Magento\Bundle\Model\Product\Type::TYPE_CODE:
                $this->checkBundleProduct($product, $config);
                break;
            case \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE:
                $this->checkConfigurableProduct($product, $config);
                break;
            case \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE:
                $this->checkGroupedProduct($config);
                break;
            default:
                $this->checkQuantity($product, $config['qty']);
                break;
        }
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param null $request
     * @param null|string $processMode
     * @return \Magento\Quote\Model\Quote\Item|string
     */
    public function addProduct(
        \Magento\Catalog\Model\Product $product,
        $request = null,
        $processMode = \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
    ) {
        $parentItem = parent::addProduct($product, $request, $processMode);
        if ($item = $this->getItemByProduct($product)) {
            if ($tierItem = $item->getCurrentTierItem()) {
                $tierItem->setQty($item->getQty());
                $this->tierItemResourceModel->save($tierItem);
            }
        }
        return $parentItem;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|mixed
     */
    public function getItemByProduct($product)
    {
        foreach ($this->getItemsByProduct($product) as $item) {
            if (!$item->getExtensionAttributes()->getSection()->getSectionId()) {
                return $item;
            }
        }
        return false;
    }

    /**
     * @param $product
     * @return array
     */
    public function getItemsByProduct($product)
    {
        $items = [];
        foreach ($this->getAllItems() as $item) {
            if ($item->representProduct($product)) {
                $items[] = $item;
            }
        }
        return $items;
    }

    /**
     * Update base custom price
     *
     * @return $this
     */
    public function updateBaseCustomPrice()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getCurrentTierItem() instanceof \Cart2Quote\Quotation\Model\Quote\TierItem
                && $item->getCurrentTierItem()->getId()
            ) {
                $baseCalculatedPrice = $item->getBaseCalculationPrice();
                $item->getCurrentTierItem()->setBaseCustomPrice($baseCalculatedPrice)->save();
            }
        }

        return $this;
    }

    /**
     * @param $amount
     * @param $isPercentage
     * @return $this
     */
    public function setSubtotalProposal($amount, $isPercentage)
    {
        $baseSubtotal = (float)$this->getOriginalSubtotal();
        foreach ($this->getAllVisibleItems() as $item) {
            /**
             * @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem
             */
            $tierItem = $item->getCurrentTierItem();
            if ($isPercentage) {
                $proposalBaseSubtotal = $tierItem->calculatePrice($baseSubtotal, $amount);
            } else {
                $proposalBaseSubtotal = $amount;
            }

            $customPrice = null;
            if ($amount > 0) {
                //Calculate item price percentage of original sub-total per item
                $percentage = $tierItem->calculatePercentage($baseSubtotal, $tierItem->getOriginalPrice());
                $customPrice = $tierItem->calculatePrice($proposalBaseSubtotal, $percentage);
            }

            $tierItem->setCustomPrice($customPrice);
            $tierItem->setSelected();
            $tierItem->save();
            $this->setRecollect(true);
        }

        return $this;
    }

    /**
     * Remove quote item
     * @param int $item
     * @return $this
     */
    public function removeQuoteItem($item)
    {
        $this->removeItem($item);
        $this->setRecollect(true);

        return $this;
    }

    /**
     * Remove quote item by item identifier
     * @param   int $itemId
     * @return $this
     */
    public function removeItem($itemId)
    {
        foreach ($this->tierItemCollectionFactory->create()->getTierItemsByItemId($itemId) as $tierItem) {
            $tierItem->delete();
        }

        $this->itemSectionResourceModel->delete($this->itemSectionProvider->getSection($itemId));

        return parent::removeItem($itemId);
    }

    /**
     * @param bool $useCache
     * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
     */
    public function getItemsCollection($useCache = true)
    {
        if (null === $this->_items) {
            $this->_items = $this->_quoteItemCollectionFactory->create();
            $this->_items->getSelect()->joinLeft(
                ['quotation_quote_section_items' => $this->_items->getTable('quotation_quote_section_items')],
                'main_table.item_id=quotation_quote_section_items.item_id',
                'sort_order'
            );
            $this->_items->setOrder('quotation_quote_section_items.sort_order',
                \Magento\Framework\Data\Collection::SORT_ORDER_ASC);
            $this->extensionAttributesJoinProcessor->process($this->_items);
            $this->_items->setQuote($this);
        }

        return parent::getItemsCollection($useCache);
    }

    /**
     * Rewrite clone method
     * @param \Magento\Quote\Model\Quote $quote
     * @return \Magento\Quote\Model\Quote
     */
    public function copy(\Magento\Quote\Model\Quote $quote)
    {

        $clone = $this->_quoteFactory->create();
        $clone->setData($quote->getData())
            ->setCreatedAt(null)
            ->setId(null)
            ->setIsActive(false);

        $items = $quote->getAllVisibleItems();
        foreach ($items as $item) {
            $clonedItem = $clone->addProduct($item->getProduct(), $item->getBuyRequest());
            $this->applyChildAttributes($item, $clonedItem);

            foreach ($item->getChildren() as $child) {
                foreach ($clonedItem->getChildren() as &$clonedChild) {
                    if ($child->getProduct()->getId() == $clonedChild->getProduct()->getId()) {
                        $this->applyChildAttributes($child, $clonedChild);
                    }
                }
            }
        }

        $addresses = $quote->getAllAddresses();
        foreach ($addresses as $address) {
            $clonedAddress = clone $address;
            $clonedAddress->setQuoteId(null)
                ->setPreviousId($address->getId())
                ->setPreviousQuoteId($address->getQuoteId());

            $clone->addAddress($clonedAddress);
        }

        foreach ($quote->getPaymentsCollection() as $payment) {
            $clonedPayment = clone $payment;
            $clonedPayment->setQuoteId(null)
                ->setPreviousId($payment->getId())
                ->setPreviousQuoteId($payment->getQuoteId());

            $clone->addPayment($clonedPayment);
        }

        return $clone;
    }

    /**
     * Apply child attributes to the cloned item
     *
     * @param $originalItem
     * @param $clonedItem
     */
    protected function applyChildAttributes($originalItem, &$clonedItem)
    {
        $clonedItem->setPreviousId($originalItem->getId())
            ->setPreviousQuoteId($originalItem->getQuoteId())
            ->setOriginalPrice($originalItem->getOriginalPrice())
            ->setBaseOriginalPrice($originalItem->getBaseOriginalPrice())
            ->setCustomPrice($originalItem->getCustomPrice())
            ->setPrice($originalItem->getPrice())
            ->setBasePrice($originalItem->getBasePrice())
            ->setDescription($originalItem->getDescription())
            ->setTierItems($originalItem->getTierItems())
            ->setCurrentTierItem($originalItem->getCurrentTierItem());
    }

    /**
     * Retrieve label of quote status
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * Retrieve label of quote status
     * @return string
     */
    public function getStateLabel()
    {
        return $this->getConfig()->getStateLabel($this->getState());
    }

    /**
     * @return string
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * Get formatted price value including quote currency rate to quote website currency
     * @param   float $price
     * @param   bool $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    /**
     * @param float $price
     * @param int $precision
     * @param bool $addBrackets
     * @return string
     */
    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getQuoteCurrency()->formatPrecision($price, $precision, [], true, $addBrackets);
    }

    /**
     * Get currency model instance. Will be used currency with which quote placed
     * @return \Magento\Directory\Model\Currency
     */
    public function getQuoteCurrency()
    {
        if ($this->_quoteCurrency === null) {
            $this->_quoteCurrency = $this->_currencyFactory->create();
            $this->_quoteCurrency->load($this->getQuoteCurrencyCode());
        }

        return $this->_quoteCurrency;
    }

    /**
     * Returns quote_currency_code
     * @return string
     */
    public function getQuoteCurrencyCode()
    {
        return $this->getData(\Magento\Quote\Api\Data\CurrencyInterface::KEY_QUOTE_CURRENCY_CODE);
    }

    /**
     * @param float $price
     * @return string
     */
    public function formatBasePrice($price)
    {
        return $this->formatBasePricePrecision($price, 2);
    }

    /**
     * @param float $price
     * @param int $precision
     * @return string
     */
    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        if ($this->_baseCurrency === null) {
            $this->_baseCurrency = $this->_currencyFactory->create()->load($this->getBaseCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    /**
     * Reset the quote currency to the current quote currency
     *
     * @return $this
     */
    public function resetQuoteCurrency()
    {
        $this->_quoteCurrency = $this->_currencyFactory->create();
        $this->_quoteCurrency->load($this->getQuoteCurrencyCode());

        return $this;
    }

    /**
     * Retrieve text formatted price value including quote rate
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->getQuoteCurrency()->formatTxt($price);
    }

    /**
     * @return string
     */
    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = $this->getCustomerFirstname() . ' ' . $this->getCustomerLastname();
        } else {
            $customerName = (string)__('Guest');
        }

        return $customerName;
    }

    /**
     * Get formatted quote created date in store timezone
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormatted($format)
    {
        return $this->timezone->formatDate(
            $this->timezone->scopeDate(
                $this->getStore(),
                $this->getQuotationCreatedAt(),
                true
            ),
            $format,
            true
        );
    }

    /**
     * @return string
     */
    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }

        return '';
    }

    /**
     * @return string
     */
    public function getExpiryDateString()
    {
        if ($this->getExpiryEnabled()) {
            $expiryDateString = '(' . __('valid until ') . $this->getExpiryDateFormatted(2) . ')';
            return $expiryDateString;
        }

        return '';
    }

    /**
     * Get formatted quote expiry date in store timezone
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getExpiryDateFormatted($format)
    {
        return $this->timezone->formatDate(
            $this->timezone->scopeDate(
                $this->getStore(),
                $this->getExpiryDate(),
                false
            ),
            $format,
            false
        );
    }

    /**
     * Sets the increment ID for the quote.
     * @param string $id
     * @return $this
     */
    public function setIncrementId($id)
    {
        return $this->setData(self::INCREMENT_ID, $id);
    }

    /**
     * Sets the proposal sent for the quote.
     * @param string $timestamp
     * @return $this
     */
    public function setProposalSent($timestamp)
    {
        return $this->setData(self::PROPOSAL_SENT, $timestamp);
    }

    /**
     * function to check whether the quote can be accepted based on its state and status
     *
     * @return bool
     */
    public function canAccept()
    {
        $state = $this->getState();
        $status = $this->getStatus();
        return $this->_statusObject->canAccept($state, $status);
    }

    /**
     * function to check whether the quote can show prices based on its state and status
     *
     * @return bool
     */
    public function showPrices()
    {
        $state = $this->getState();
        $status = $this->getStatus();
        return $this->_statusObject->showPrices($state, $status);
    }

    /**
     * Get Base Customer Price Total
     *
     * @return float
     */
    public function getBaseCustomPriceTotal()
    {
        return $this->getData(self::BASE_CUSTOM_PRICE_TOTAL);
    }

    /**
     * Get Customer Price Total
     *
     * @return float
     */
    public function getCustomPriceTotal()
    {
        return $this->getData(self::CUSTOM_PRICE_TOTAL);
    }

    /**
     * Get Quote Adjustment
     *
     * @return float
     */
    public function getQuoteAdjustment()
    {
        return $this->getData(self::QUOTE_ADJUSTMENT);
    }

    /**
     * Get Base Quote Adjustment
     *
     * @return float
     */
    public function getBaseQuoteAdjustment()
    {
        return $this->getData(self::BASE_QUOTE_ADJUSTMENT);
    }

    /**
     * Return quote entity type
     * @return string
     */
    public function getEntityType()
    {
        return self::ENTITY;
    }

    /**
     * @return string
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * Function that gets a hash to use in a url (for autologin urls)
     *
     * @return string
     */
    public function getUrlHash()
    {
        if ($this->getHash() == "") {
            $hash = $this->getRandomHash();
            $this->setHash($hash);
            $this->save();
        }

        $hash = sha1($this->getCustomerEmail() . $this->getHash() . $this->getPasswordHash());

        return $hash;
    }

    /**
     * Function that generates a random hash of a given length
     *
     * @param int $length
     * @return string
     */
    public function getRandomHash($length = 40)
    {

        $max = ceil($length / 40);
        $random = '';
        for ($i = 0; $i < $max; $i++) {
            $random .= sha1(microtime(true) . mt_rand(10000, 90000));
        }

        return substr($random, 0, $length);
    }

    /**
     * Concert a price to the quote base rate price
     * Magento does not come with a currency conversion via the quote rates, only the active rates.
     *
     * @param $price
     * @return double
     */
    public function convertPriceToQuoteBaseCurrency($price)
    {
        if ($this->isCurrencyDifferent()) {
            $price = $this->convertRate($price, $this->getBaseToQuoteRate(), true);
        }

        return $price;
    }

    /**
     * Get total item qty
     *
     * @return int
     */
    public function getTotalItemQty()
    {
        $itemsQty = 0;
        foreach ($this->getAllVisibleItems() as $item) {
            $itemQty = $item->getQty();
            $itemsQty += $itemQty;
        }

        return $itemsQty;
    }

    /**
     * @param bool $includeUnassigned
     * @param array $unassignedData
     * @return array
     */
    public function getSections($includeUnassigned = true, $unassignedData = [])
    {
        $sections = $this->getExtensionAttributes()->getSections();
        if ($includeUnassigned) {
            $sections[] = $this->sectionFactory->create(
                ['data' => array_merge(['label' => '', 'sort_order' => -1], $unassignedData)]
            );
        }
        usort($sections, [$this, 'sort']);
        return $sections;
    }

    /**
     * @param $sectionId
     * @return array
     */
    public function getSectionItems($sectionId)
    {
        $items = [];
        /**
         * @var \Magento\Quote\Api\Data\CartItemInterface $item
         */
        foreach ($this->getAllVisibleItems() as $item) {
            if ($item->getExtensionAttributes()->getSection()->getSectionId() == $sectionId) {
                $items[] = $item;
            }
        }
        usort($items, [$this, 'sort']);
        return $items;
    }

    /**
     * Check stock settings for quantity
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $qty
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function checkQuantity($product, $qty)
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        $stockItem->setProductName($product->getName());
        $result = $this->stockStateProvider->checkQuoteItemQty(
            $stockItem,
            $qty,
            $qty,
            $qty
        );

        if ($result->getHasError()
            || $result->getMessage()
            && $stockItem->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
        ) {
            throw new \Magento\Framework\Exception\LocalizedException(__($result->getMessage()));
        }
    }

    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Cart2Quote\Quotation\Model\ResourceModel\Quote');
    }

    /**
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $compare
     * @param \Cart2Quote\Quotation\Model\Quote\Section|\Magento\Quote\Api\Data\CartItemInterface $to
     * @return int
     */
    private function sort($compare, $to)
    {
        if ($compare instanceof \Magento\Quote\Api\Data\CartItemInterface && $to instanceof \Magento\Quote\Api\Data\CartItemInterface) {
            $compareSortOrder = $compare->getExtensionAttributes()->getSection()->getSortOrder();
            $toSortOrder = $to->getExtensionAttributes()->getSection()->getSortOrder();
        } else {
            $compareSortOrder = $compare->getSortOrder();
            $toSortOrder = $to->getSortOrder();
        }

        if ($compareSortOrder == $toSortOrder) {
            return 0;
        }
        return ($compareSortOrder < $toSortOrder) ? -1 : 1;
    }
}
