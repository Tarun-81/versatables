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

use Magento\Customer\Model\Context;

/**
 * Quote view block
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'quote/view.phtml';

    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * View constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->quotationHelper = $quotationHelper;
        $this->_statusCollection = $statusCollection;
        $this->_isScopePrivate = true;
        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Return back url for logged in and guest users
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }

        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return __('Back to My Quote');
        }

        return __('View Another Quote');
    }

    /**
     * Return the data for the button on the customer dashboard quote detail view
     * @return array
     */
    public function getActionButtonData()
    {
        $status = $this->getQuoteStatus();
        $flag = $status->getFrontendButtonHtmlFlag();
        $buttonLabel = $this->getFrontendButtonLabel($status);
        $data = [
            'flag' => $flag,
            'label' => $buttonLabel
        ];

        return $data;
    }

    /**
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Quote # %1', $this->getQuote()->getIncrementId()));
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        $acceptWithoutCheckout = $this->isCheckoutDisabled();

        if ($acceptWithoutCheckout) {
            return $this->getUrl(
                'quotation/quote_checkout/acceptwithoutcheckout',
                ['quote_id' => $this->getQuote()->getId()]);
        }

        return $this->getUrl(
            'quotation/quote_checkout/accept',
            ['quote_id' => $this->getQuote()->getId()]);
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Returns true if the button can be shown in quote view page in the customer dashboard
     *
     * @return bool
     */
    public function showCheckoutButton()
    {
        $button  = true;
        $status = $this->getQuoteStatus();
        if ($status->statusIsAccepted() && $this->isCheckoutDisabled()) {
            $button = false;
        }

        return $button;

    }

    /**
     * If checkout is disabled after accepting quotes, this method returns true
     *
     * @return bool
     */
    protected function isCheckoutDisabled()
    {
        return $this->quotationHelper->isCheckoutDisabled();
    }

    /**
     * Get the Label for the button on the customer dashboard quote detail view
     *
     * @return string
     */
    protected function getFrontendButtonLabel($status)
    {
        $buttonLabel = __('Accept & Checkout');
        if ($this->isCheckoutDisabled()) {
            $buttonLabel = __('Accept Quotation');
        } elseif ($status->statusIsAccepted()) {
            $buttonLabel = __('Checkout');
        }

        return $buttonLabel;
    }

    /**
     * Returns the status of a given quote.
     *
     * @return \Magento\Framework\DataObject
     */
    protected function getQuoteStatus()
    {
        $quote = $this->getQuote();
        $status = $this->_statusCollection->getItemByColumnValue('status', $quote->getStatus());

        return $status;
    }
}
