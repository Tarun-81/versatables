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

namespace Cart2Quote\Quotation\Block\Quote;

/**
 * Quotation quote history block
 */
class History extends AbstractQuote
{
    /**
     * @var string
     */
    protected $_template = 'quote/history.phtml';

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $_quoteCollectionFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config
     */
    protected $_quoteConfig;

    /** @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection */
    protected $quotes;

    /**
     * History constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        array $data = []
    ) {
        $this->_quoteCollectionFactory = $quoteCollectionFactory;
        $this->_customerSession = $customerSession;
        $this->_quoteConfig = $quoteConfig;
        parent::__construct($context, $customerSession, $quotationSession, $quotationHelper, $data);
    }

    /**
     * @return string
     */
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    /**
     * @param object $quote
     * @return string
     */
    public function getViewUrl($quote)
    {
        return $this->getUrl('quotation/quote/view', ['quote_id' => $quote->getId()]);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('customer/account/');
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->pageConfig->getTitle()->set(__('My Quotes'));
    }

    /**
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if ($this->getQuotes()) {
            $pager = $this->getLayout()->createBlock(
                'Magento\Theme\Block\Html\Pager',
                'sales.quote.history.pager'
            )->setCollection(
                $this->getQuotes()
            );
            $this->setChild('pager', $pager);
            $this->getQuotes()->load();
        }
        return $this;
    }

    /**
     * @return bool|\Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection
     */
    public function getQuotes()
    {
        if (!($customerId = $this->_customerSession->getCustomerId())) {
            return false;
        }
        if (!$this->quotes) {
            $this->quotes = $this->_quoteCollectionFactory->create()->addFieldToSelect(
                '*'
            )->addFieldToFilter(
                'customer_id',
                $customerId
            )->addFieldToFilter(
                'is_quote',
                1
            )->addFieldToFilter(
                'status',
                ['in' => $this->_quoteConfig->getVisibleOnFrontStatuses()]
            )->setOrder(
                'quotation_created_at',
                'desc'
            );
        }
        return $this->quotes;
    }
}
