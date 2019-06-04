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

/**
 * Class Index
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class Index extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Shopping cart display action
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($invalid = $this->isInvalidQuoteRequest()) {
            return $this->_redirect('quotation/quote/emptyQuote');
        }

        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->set(__('Quote'));
        return $resultPage;
    }

    /**
     * Checks if the request is valid
     *
     * @return bool
     */
    public function isInvalidQuoteRequest()
    {
        $quoteId = $this->_quotationSession->getQuoteId();
        $quote = $this->_quotationSession->getQuote();
        return $quoteId == null || !($quote && $quote->hasItems() && $quote->getIsActive());
    }
}
