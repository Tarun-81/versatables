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

namespace Cart2Quote\Quotation\Controller\Copytoquote;

/**
 * Class Index
 * @package Cart2Quote\Quotation\Controller\Copytoquote
 */
class Index extends \Cart2Quote\Quotation\Controller\Copytoquote
{
    /**
     * Shopping cart display action
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        //copy quote and use new quote id
        $quoteId = $this->_checkoutSession->getQuoteId();

        $quote = $this->_cloneQuote($quoteId);
        if (!$quote) {
            //set error message
            $this->messageManager->addError(__('The cart could not be copied to the quote.'));
        } else {
            $this->messageManager->addSuccess(__('The cart is successfully copied to the quote.'));
            $this->_quotationSession->setQuoteId($quote->getId());
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('quotation/quote/index');

        return $resultRedirect;
    }
}
