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

use Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender;

/**
 * Class Edit
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Edit extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     */
    protected $quoteFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender
     */
    protected $quoteCanceledSender;

    /**
     * Edit constructor.
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
     * @param QuoteCanceledSender $quoteCanceledSender
     * @param \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate
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
        QuoteCanceledSender $quoteCanceledSender,
        \Cart2Quote\Quotation\Model\Admin\Quote\Create $quoteCreate,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
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
        $this->quoteCanceledSender = $quoteCanceledSender;
    }

    /**
     * Cancel original quotation and create new quotation
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        if ($results = parent::execute()) {
            return $results;
        }

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        /** @var \Cart2Quote\Quotation\Model\Quote $quotation */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            //Cancel Original Quote
            $originQuote = $this->quoteFactory->create()->load($this->getRequest()->getPost('quote_id'));
            $originQuote->setData('state', \Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
            $originQuote->setData('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
            $originQuote->save();
            if ($this->quoteCanceledSender->send($originQuote)) {
                $this->messageManager->addSuccess(__('The customer is notified'));
            }

            $newQuote = $this->quoteFactory->create()->copy($originQuote);
            $newQuote->save();

            //create new quotation
            $quotation = $this->quoteFactory->create()->load($newQuote->getData('entity_id'));
            $quotation->setData('quote_id', $newQuote->getData('entity_id'));
            $quotation->setData('state', \Cart2Quote\Quotation\Model\Quote\Status::STATE_OPEN);
            $quotation->setData('status', \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN);
            $quotation->setData('original_base_subtotal', $newQuote->getData('original_base_subtotal'));
            $quotation->setData('original_subtotal', $newQuote->getData('original_subtotal'));

            $newIncrementId = $this->getNewIncrementId($this->getRequest()->getPost('increment_id'));
            $quotation->setData('increment_id', $newIncrementId);

            $quotation->saveQuote();
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quotation->getId()]);
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->getCurrentQuote()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        }

        return $resultRedirect;
    }

    /**
     * Get increment id for created new
     * @param string increment id
     * @return string increment_id
     */
    protected function getNewIncrementId($incrementId)
    {
        // get Parent Increment Id
        $splitIncrementId = explode('-', $incrementId);
        if (is_array($splitIncrementId)) {
            $parentIncrementId = $splitIncrementId[0];
        } else {
            $parentIncrementId = $incrementId;
        }

        $quoteCollection = $this->quoteFactory->create()->getCollection();
        $quoteCollection = $quoteCollection
            ->addFieldToSelect('*')
            ->addFieldToFilter(
                'main_table.increment_id',
                ['like' => '%' . $parentIncrementId . '%']
            );
        $quoteCollectionCount = $quoteCollection->getSize();

        if ($quoteCollectionCount) {
            return $parentIncrementId . '-' . $quoteCollectionCount;
        }

        return $parentIncrementId;
    }
}
