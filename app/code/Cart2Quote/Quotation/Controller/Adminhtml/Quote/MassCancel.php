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
 * Class MassCancel
 */
class MassCancel extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Cancel quote
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $selectedQuoteIds = $this->getRequest()->getPost('ids');
        $countSuccess = 0;
        $countFailed = 0;

        if (!is_array($selectedQuoteIds)) {
            $this->messageManager->addError(__('Please select items.'));
        } else {
            foreach ($selectedQuoteIds as $val) {
                $quote = $this->quoteFactory->create()->load($val);
                if ($quote->getStatus() == \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED) {
                    $countFailed++;
                } else {
                    $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
                    $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
                    $quote->save();
                    $countSuccess++;
                }
            }

            if ($countFailed) {
                $this->messageManager->addError(__('%1 quote(s) cannot be canceled.', $countFailed));
            }
            if ($countSuccess) {
                $this->messageManager->addSuccess(__('We canceled %1 quote(s).', $countSuccess));
            }
        }

        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }
}
