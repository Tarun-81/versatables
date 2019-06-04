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
 * Class MassDelete
 */
class MassDelete extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Delete quote
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $selectedQuoteIds = $this->getRequest()->getPost('ids');

        if (!is_array($selectedQuoteIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            foreach ($selectedQuoteIds as $selectedQuoteId) {
                $quote = $this->quoteFactory->create()->load($selectedQuoteId);
                $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
                $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
                $quote->setIsQuote(0);
                $quote->save();
            }

            $this->messageManager->addSuccess(__('Total of %1 record(s) were deleted', count($selectedQuoteIds)));
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::massDelete');
    }
}
