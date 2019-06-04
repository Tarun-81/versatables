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

namespace Cart2Quote\Quotation\Model\Quote\Email\Sender;

/**
 * Interface QuoteSenderInterface
 * @package Cart2Quote\Quotation\Model\Quote\Email\Sender
 */
interface QuoteSenderInterface
{
    /**
     * Sends quote request email to the customer.
     * Email will be sent immediately in two cases:
     * - if asynchronous email sending is disabled in global settings
     * - if $forceSyncMode parameter is set to TRUE
     * Otherwise, email will be sent later during running of
     * corresponding cron job.
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param bool $forceSyncMode
     * @return bool
     */
    public function send(\Cart2Quote\Quotation\Model\Quote $quote, $forceSyncMode = false);
}