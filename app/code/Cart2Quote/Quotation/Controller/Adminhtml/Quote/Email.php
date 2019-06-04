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

use Magento\Framework\App\ObjectManager;

/**
 * Class Email
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class Email extends \Cart2Quote\Quotation\Controller\Adminhtml\Quote
{
    /**
     * Notify user
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $quote = $this->_initQuote();
        if ($quote) {
            try {
                /**
                 * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteSenderInterface $sender
                 */
                $sender = null;
                switch ($quote->getStatus()) {
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OPEN:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_NEW:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CHANGE_REQUEST:
                        $sender = ObjectManager::getInstance()->get('\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender');
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_AUTO_PROPOSAL_SENT:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT:
                        $sender = ObjectManager::getInstance()->get('\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalSender');
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_OUT_OF_STOCK:
                        $sender = ObjectManager::getInstance()->get('\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteCanceledSender');
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED:
                        $sender = ObjectManager::getInstance()->get('\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender');
                        break;
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_ACCEPTED:
                    case \Cart2Quote\Quotation\Model\Quote\Status::STATUS_CLOSED:
                        $sender = ObjectManager::getInstance()->get('\Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalAcceptedSender');
                        break;
                    default:
                        throw new \Magento\Framework\Exception\LocalizedException(__('No e-mail available for this status'));
                }

                if ($sender->send($quote)) {
                    $this->messageManager->addSuccess(__('You sent the quote email.'));
                } else {
                    $this->messageManager->addError(__('This e-mail is not enabled'));
                }
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(__('We can\'t send the email for the quote right now.'));
                $this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
            }
            return $this->resultRedirectFactory->create()->setPath(
                'quotation/quote/view',
                ['quote_id' => $quote->getId()]
            );
        }
        return $this->resultRedirectFactory->create()->setPath('quotation/*/');
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::email');
    }
}
