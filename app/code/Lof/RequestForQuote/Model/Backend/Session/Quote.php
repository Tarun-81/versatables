<?php
namespace Lof\RequestForQuote\Model\Backend\Session;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupManagementInterface;

class Quote extends \Magento\Backend\Model\Session\Quote
{
    /**
     * Quote instance
     *
     * @var Quote
     */
    protected $_rfqQuote;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * Quote constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Session\SidResolverInterface $sidResolver
     * @param \Magento\Framework\Session\Config\ConfigInterface $sessionConfig
     * @param \Magento\Framework\Session\SaveHandlerInterface $saveHandler
     * @param \Magento\Framework\Session\ValidatorInterface $validator
     * @param \Magento\Framework\Session\StorageInterface $storage
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\State $appState
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param GroupManagementInterface $groupManagement
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
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
        CustomerRepositoryInterface $customerRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        GroupManagementInterface $groupManagement,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
    ) {
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
            $customerRepository,
            $quoteRepository,
            $orderFactory,
            $storeManager,
            $groupManagement,
            $quoteFactory
        );
        $this->quoteCollectionFactory = $quoteCollectionFactory;
    }

    /**
     * Get checkout quote instance by current session
     *
     * @return Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getRfqQuote()
    {
        if ($this->_rfqQuote === null) {
        	$mageQuote = $this->getQuote();
        	if (!$mageQuote->getId()) {
        		$mageQuote = $this->quoteFactory->create();
        		$mageQuote->setStore($this->_storeManager->getStore());
        		if ($customerId = $this->getCustomerId()) {
        			$mageQuote->setCustomer($this->customerRepository->getById($customerId));
        		}
        		$mageQuote->save();
        		$this->setQuoteId($mageQuote->getId());
        	}

        	$mageQuoteId = $mageQuote->getId();
        	$rfqQuote   = $this->quoteCollectionFactory->create()
        	->addFieldToFilter('rfq_parent_quote_id', $mageQuoteId)
        	->getFirstItem();

        	if (!$rfqQuote->getId()) {
        		$rfqQuote = $this->quoteFactory->create();
        		$rfqQuote->setStore($this->_storeManager->getStore());
        		if ($customerId = $this->getCustomerId()) {
        			$rfqQuote->setCustomer($this->customerRepository->getById($customerId));
        		}
                //$rfqQuote->setData('customer_is_guest', 0);
        		$rfqQuote->setData('rfq_parent_quote_id', $mageQuoteId);
        		$rfqQuote->save();
        		$rfqQuote->setRfqQuoteId($rfqQuote->getId());

                $rfqQuote = $this->quoteRepository->get($rfqQuote->getId());
        	}
            $this->_rfqQuote = $rfqQuote;
        }

    	return $this->_rfqQuote;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @codeCoverageIgnore
     */
    protected function _getRfqQuoteIdKey()
    {
        return 'rfq_quote_id_' . $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @param int $quoteId
     * @return void
     * @codeCoverageIgnore
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setRfqQuoteId($quoteId)
    {
        $this->storage->setData($this->_getRfqQuoteIdKey(), $quoteId);
    }

    /**
     * @return int
     * @codeCoverageIgnore
     */
    public function getRfqQuoteId()
    {
        return $this->getData($this->_getQuoteIdKey());
    }


    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @codeCoverageIgnore
     */
    protected function _getRfqLastQuoteIdKey()
    {
        return 'rfq_last_quote_id_' . $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @param int $quoteId
     * @return void
     * @codeCoverageIgnore
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setRfqLastQuoteId($quoteId)
    {
        $this->storage->setData($this->_getRfqLastQuoteIdKey(), $quoteId);
    }

    /**
     * @return int
     * @codeCoverageIgnore
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRfqLastQuoteId()
    {
        return $this->getData($this->_getRfqLastQuoteIdKey());
    }

    /**
     * Destroy/end a session
     * Unset all data associated with object
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function clearRfqQuote()
    {
        $this->_rfqQuote = null;
        $this->setRfqQuoteId(null);
        $this->setRfqLastQuoteId(null);
        return $this;
    }


    /**
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getQuote()
    {
        if ($this->_quote === null) {
            $this->_quote = $this->quoteFactory->create();
            if ($this->getStoreId()) {
                if (!$this->getQuoteId()) {
                    $this->_quote->setCustomerGroupId($this->groupManagement->getDefaultGroup()->getId());
                    $this->_quote->setIsActive(false);
                    $this->_quote->setStoreId($this->getStoreId());

                    $this->quoteRepository->save($this->_quote);
                    $this->setQuoteId($this->_quote->getId());
                    $this->_quote = $this->quoteRepository->get($this->getQuoteId(), [$this->getStoreId()]);
                } else {
                    $this->_quote = $this->quoteRepository->get($this->getQuoteId(), [$this->getStoreId()]);
                    $this->_quote->setStoreId($this->getStoreId());
                }

                if ($this->getCustomerId() && $this->getCustomerId() != $this->_quote->getCustomerId()) {
                    $customer = $this->customerRepository->getById($this->getCustomerId());
                    $this->_quote->assignCustomer($customer);
                    $this->quoteRepository->save($this->_quote);

                    $childQuote = $this->quoteFactory->create()
                        ->getCollection()
                        ->addFieldToFilter('rfq_parent_quote_id', $this->_quote->getEntityId())
                        ->getFirstItem();

                    $childQuote->assignCustomer($this->_quote->getCustomer());
                    $this->quoteRepository->save($childQuote);

                }
            }
            $this->_quote->setIgnoreOldQty(true);
            $this->_quote->setIsSuperMode(true);
        }

        return $this->_quote;
    }
}