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
namespace Cart2Quote\Quotation\Controller\Quote;

use Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\RawFactory;


/**
 * Class UpdateTier
 * @package Cart2Quote\Quotation\Controller\Quote\Ajax
 */
class UpdateTier extends \Cart2Quote\Quotation\Controller\AbstractController\View
{

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote
     */
    private $quoteResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    private $quote;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    private $resultRawFactory;

    /**
     * UpdateTier constructor.
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface $quoteLoader
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Model\Quote $quote ,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel,
        \Magento\Framework\App\Action\Context $context,
        \Cart2Quote\Quotation\Controller\AbstractController\QuoteLoaderInterface $quoteLoader,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->quoteResourceModel = $quoteResourceModel;
        $this->quote = $quote;
        $this->resultRawFactory = $resultRawFactory;
        parent::__construct($context, $quoteLoader, $resultPageFactory);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $postData = $this->getRequest()->getPostValue();
        $quoteId = $this->getRequest()->getParam("quote_id");
        $this->quoteResourceModel->load($this->quote, $quoteId);
        if ($this->quote->getId()) {
            $recalculate = false;
            if (is_array($postData) && isset($postData['item_id'], $postData['tier_item_id'])) {
                $quoteItem = $this->quote->getItemById($postData['item_id']);
                $tierItemId = $postData['tier_item_id'];
                if ($quoteItem->getCurrentTierItem()->getId() != $tierItemId) {
                    $tierItemCollection = $quoteItem->getTierItems();
                    /** @var \Cart2Quote\Quotation\Model\Quote\TierItem $tierItem */
                    $tierItem = $tierItemCollection->getItemById($tierItemId);

                    $tierItem->setItem($quoteItem);
                    $tierItem->setSelected();
                    $recalculate = true;
                }

                if ($recalculate) {
                    $this->quote->setRecollect(true)->collectTotals()->save();
                }
            }
        }
        $response = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $layout = $response->addHandle('quotation_quote_ajax_updatetier')->getLayout();
        $block = $layout->getBlock('quote_totals');
        $block->setQuote($this->quote);
        $response =$block->toHtml();
        return $this->resultRawFactory->create()->setContents($response);
    }
}
