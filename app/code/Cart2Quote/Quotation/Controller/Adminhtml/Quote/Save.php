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
 * Class Save
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Save extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Saving quote quotation
     * @return \Magento\Backend\Model\View\Result\Forward|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        /** @var \Cart2Quote\Quotation\Model\Quote $quotation */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $this->_initQuote();
            $this->_processActionData('save');

            //done
            $this->_getSession()->clearStorage();
            $this->messageManager->addSuccess(__('You updated the quote.'));

            $this->_reloadQuote();

            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Magento\Framework\Exception\PaymentException $e) {
            $this->getCurrentQuote()->saveQuote();
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $message = $e->getMessage();
            if (!empty($message)) {
                $this->messageManager->addError($message);
            }
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Quote saving error: %1', $e->getMessage()));
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $this->getCurrentQuote()->getId()]);
        }

        return $resultRedirect;
    }
}
