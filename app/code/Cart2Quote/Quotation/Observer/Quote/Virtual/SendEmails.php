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

namespace Cart2Quote\Quotation\Observer\Quote\Virtual;

use Magento\Framework\Event\ObserverInterface;

/**
 * Sales emails sending observer.
 *
 * Performs handling of cron jobs related to sending emails to customers
 * after creation/modification of a Quote.
 */
class SendEmails implements ObserverInterface
{
    /**
     * Global configuration storage.
     *
     * @var \Cart2Quote\Quotation\Model\EmailsSenders
     */
    protected $emailsSenders;

    /**
     * @param \Cart2Quote\Quotation\Model\EmailsSenders $emailsSenders
     */
    public function __construct(\Cart2Quote\Quotation\Model\EmailsSenders $emailsSenders)
    {
        $this->emailsSenders = $emailsSenders;
    }

    /**
     * Handles asynchronous email sending during corresponding
     * cron job.
     *
     * Also method is used in the next events:
     *
     * - config_data_sales_email_general_async_sending_disabled
     *
     * Works only if asynchronous email sending is enabled
     * in global settings.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $this->emailsSenders->sendEmails();
    }
}
