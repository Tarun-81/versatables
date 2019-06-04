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

namespace Cart2Quote\Quotation\Model\Admin\Quote;

/**
 * Class EmailSender
 */
class EmailSender
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender
     */
    protected $quoteRequestSender;

    /**
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $quoteRequestSender
     */
    public function __construct(
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Psr\Log\LoggerInterface $logger,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteRequestSender $quoteRequestSender
    ) {
        $this->messageManager = $messageManager;
        $this->logger = $logger;
        $this->quoteRequestSender = $quoteRequestSender;
    }

    /**
     * Send email about new quote.
     * Process mail exception
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote)
    {
        try {
            $this->quoteRequestSender->send($quote);
        } catch (\Magento\Framework\Exception\MailException $exception) {
            $this->logger->critical($exception);
            $this->messageManager->addWarning(
                __('You did not email your customer. Please check your email settings.')
            );

            return false;
        }

        return true;
    }
}
