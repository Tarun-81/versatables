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

/**
 * Class Convert
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Convert extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Search Criteria
     *
     * @var \Magento\Framework\Api\SearchCriteria
     */
    protected $searchCriteria;

    /**
     * Filter Group Factory
     *
     * @var \Magento\Framework\Api\Search\FilterGroupFactory
     */
    protected $filterGroupFactory;

    /**
     * Filter Factory
     *
     * @var \Magento\Framework\Api\FilterFactory
     */
    protected $filterFactory;

    /**
     * Order repository
     *
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $orderRepository;

    /**
     * Quote Session
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $quoteSession;

    /**
     * Convert constructor.
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
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria
     * @param \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory
     * @param \Magento\Framework\Api\FilterFactory $filterFactory
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Backend\Model\Session\Quote $quoteSession
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
     * @internal param \Cart2Quote\Quotation\Model\Quote\TierItemFactory $tierItemFactory
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
        \Magento\Framework\Api\SearchCriteria $searchCriteria,
        \Magento\Framework\Api\Search\FilterGroupFactory $filterGroupFactory,
        \Magento\Framework\Api\FilterFactory $filterFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Backend\Model\Session\Quote $quoteSession,
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->searchCriteria = $searchCriteria;
        $this->filterGroupFactory = $filterGroupFactory;
        $this->filterFactory = $filterFactory;
        $this->orderRepository = $orderRepository;
        $this->quoteSession = $quoteSession;
        $this->quoteFactory = $quoteFactory;

        parent::__construct(
            $escaper,
            $context,
            $coreRegistry,
            $fileFactory,
            $translateInline,
            $resultPageFactory,
            $resultJsonFactory,
            $resultLayoutFactory,
            $resultRawFactory,
            $helperData,
            $quoteFactory,
            $statusCollection,
            $quoteCreate,
            $scopeConfig
        );
    }

    /**
     * Saving quote quotation
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        /** @var \Cart2Quote\Quotation\Model\Quote $quotation */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_processActionData('save');

            //load existing quote
            $id = $this->getRequest()->getPost('quote_id');
            $quotation = $this->quoteFactory->create()->load($id)
                ->importPostData($this->getRequest()->getPost('quote'));

            if ($this->getCountOrders($quotation->getReservedOrderId()) > 0) {
                $quotation->setReservedOrderId(null);
                $quotation->reserveOrderId();
            }

            $quotation->saveQuote();
            $this->saveToQuoteSession($quotation);

            //redirect to order_create page
            $resultRedirect->setPath('sales/order_create');
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->getCurrentQuote()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/*/');
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/*/');
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote convert error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/*/');
        }

        return $resultRedirect;
    }

    /**
     * Get the count of the order with a specific quote id
     *
     * @param string $incrementId
     * @return int
     */
    protected function getCountOrders($incrementId)
    {
        return $this->getOrdersByQuoteId($incrementId)->getTotalCount();
    }

    /**
     * Get orders by quote id
     *
     * @param string $incrementId
     * @return \Magento\Sales\Api\Data\OrderSearchResultInterface
     */
    protected function getOrdersByQuoteId($incrementId)
    {
        $filterGroupQuoteId = $this->filterGroupFactory->create()->setFilters([
            $this->filterFactory->create()->setField('increment_id')->setValue($incrementId)
        ]);

        $this->searchCriteria->setFilterGroups([$filterGroupQuoteId]);

        return $this->orderRepository->getList($this->searchCriteria);
    }

    /**
     * Move quote to order create session
     *
     * @param \Cart2Quote\Quotation\Model\Quote $quotation
     * @return void
     */
    protected function saveToQuoteSession($quotation)
    {
        $this->quoteSession
            ->setQuoteId($quotation->getId())
            ->setCustomerId($quotation->getCustomerId())
            ->setStoreId($quotation->getStoreId())
            ->setQuotationId($quotation->getIncrementId())
            ->setQuotationQuoteId($quotation->getQuoteId());
    }
}
