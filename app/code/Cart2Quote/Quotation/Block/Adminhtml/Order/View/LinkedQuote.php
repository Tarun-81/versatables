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

namespace Cart2Quote\Quotation\Block\Adminhtml\Order\View;

/**
 * Class LinkedQuote
 * @package Cart2Quote\Quotation\Block\Adminhtml\Order\View
 */
class LinkedQuote extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\Factory $quoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection
     */
    protected $quoteCollection;

    /**
     * LinkedQuote constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteCollectionFactory
     * @param \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param array $data
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection $quoteCollectionFactory,
        \Magento\Quote\Model\Resourcemodel\Quote\Collection $quoteCollection,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        array $data = []
    ) {
        $this->quoteCollection = $quoteCollection;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * Get quote view URL
     *
     * @param int $quoteId
     * @return string
     */
    public function getQuoteViewUrl($quoteId)
    {
        $quoteId = $this->getLinkedQuotation($quoteId);

        return $this->getUrl('quotation/quote/view', ['quote_id' => $quoteId]);
    }

    /**
     * Get quote increment id
     *
     * @param int $quoteId
     * @return string|bool
     */
    public function getQuoteNumber($quoteId)
    {
        $quoteId = $this->getLinkedQuotation($quoteId);
        $quote = $this->quoteCollectionFactory->getQuote($quoteId);

        return is_array($quote) ? $quote['increment_id'] : false;
    }

    /**
     * Get linked quote number
     *
     * @param int $quoteId
     * @return int $quoteId
     */
    public function getLinkedQuotation($quoteId)
    {
        $quote = $this->quoteCollection->addFieldToFilter('entity_id', ['eq' => $quoteId])->load()->getFirstItem();
        $linkedQuoteId = $quote->getLinkedQuotationId();

        return isset($linkedQuoteId) ? $linkedQuoteId : $quoteId;
    }
}
