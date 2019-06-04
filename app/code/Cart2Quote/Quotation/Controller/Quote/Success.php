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
 * Class Success
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class Success extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * Order success action
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('id', false);
        if (!$quoteId) {
            return $this->_redirect('*/*/index');
        }

        $quote = $this->_quoteFactory->create()->load($quoteId);
        $quote->setIsActive(false);
        $quote->save();

        $session = $this->getQuotationSession();
        $session->fullSessionClear();
        $session->updateLastQuote($quote);

        $resultPage = $this->resultPageFactory->create();
        $this->_eventManager->dispatch(
            'quotation_quote_controller_success_action',
            ['quote_ids' => [$session->getLastQuoteId()]]
        );
        return $resultPage;


    }
}
