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
 * Quotation request success page
 */
class Success extends AbstractQuote
{
    /**
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $_quotationSession;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Config
     */
    protected $_quoteConfig;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Success constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\Quote\Config $quoteConfig,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context, $customerSession, $quotationSession, $quotationHelper, $data);
        $this->_quotationSession = $quotationSession;
        $this->_isScopePrivate = true;
        $this->_quoteConfig = $quoteConfig;
        $this->httpContext = $httpContext;
    }

    /**
     * See if the quote has state, visible on frontend
     * @return bool
     */
    public function isQuoteVisible()
    {
        return (bool)$this->_getData('is_quote_visible');
    }

    /**
     * Render additional quote information lines and return result html
     * @return string
     */
    public function getAdditionalInfoHtml()
    {
        return $this->_layout->renderElement('quote.success.additional.info');
    }

    /**
     * Initialize data and prepare it for output
     * @return string
     */
    protected function _beforeToHtml()
    {
        $this->_prepareLastQuote();
        return parent::_beforeToHtml();
    }

    /**
     * Get last quote ID from session, fetch it and check whether it can be viewed, printed etc
     * @return void
     */
    protected function _prepareLastQuote()
    {
        $quoteId = $this->_quotationSession->getLastQuoteId();
        if ($quoteId) {
            $incrementId = $this->_quotationSession->getLastRealQuoteId();
            $status = $this->_quotationSession->getLastQuoteStatus();
            if ($status && $incrementId) {
                $isVisible = !in_array($status, $this->_quoteConfig->getInvisibleOnFrontStatuses());
                $canView = $this->httpContext->getValue(Context::CONTEXT_AUTH) && $isVisible;
                $this->addData(
                    [
                        'is_quote_visible' => $isVisible,
                        'view_quote_url' => $this->getUrl('quotation/quote/view/', ['quote_id' => $quoteId]),
                        'can_print_quote' => $isVisible,
                        'can_view_quote' => $canView,
                        'quote_id' => $incrementId,
                    ]
                );
            }
        }
    }
}
