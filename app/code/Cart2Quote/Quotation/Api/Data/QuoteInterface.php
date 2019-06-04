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

namespace Cart2Quote\Quotation\Api\Data;

/**
 * Quote interface.
 * Interface QuoteInterface
 * @package Cart2Quote\Quotation\Api\Data
 */
interface QuoteInterface extends \Magento\Quote\Api\Data\CartInterface
{
    /**
     * State
     */
    const STATE = 'state';

    /**
     * Status
     */
    const STATUS = 'status';

    /**
     * Increment id
     */
    const INCREMENT_ID = 'increment_id';

    /**
     * Proposal sent
     */
    const PROPOSAL_SENT = 'proposal_sent';

    /**
     * Send request email
     */
    const SEND_REQUEST_EMAIL = 'send_request_email';

    /**
     * Request email sent
     */
    const REQUEST_EMAIL_SENT = 'request_email_sent';

    /**
     * Send quote canceled email
     */
    const SEND_QUOTE_CANCELED_EMAIL = 'send_quote_canceled_email';

    /**
     * Quote canceled email sent
     */
    const QUOTE_CANCELED_EMAIL_SENT = 'quote_canceled_email_sent';

    /**
     * Send proposal accepted email
     */
    const SEND_PROPOSAL_ACCEPTED_EMAIL = 'send_proposal_accepted_email';

    /**
     * Proposa accepted email sent
     */
    const PROPOSAL_ACCEPTED_EMAIL_SENT = 'proposal_accepted_email_sent';

    /**
     * Send proposal expired email
     */
    const SEND_PROPOSAL_EXPIRED_EMAIL = 'send_proposal_expired_email';

    /**
     * Proposal expired email sent
     */
    const PROPOSAL_EXPIRED_EMAIL_SENT = 'proposal_expired_email_sent';

    /**
     * Send proposal email
     */
    const SEND_PROPOSAL_EMAIL = 'send_proposal_email';

    /**
     * Proposal email sent
     */
    const PROPOSAL_EMAIL_SENT = 'proposal_email_sent';

    /**
     * Send reminder email
     */
    const SEND_REMINDER_EMAIL = 'send_reminder_email';

    /**
     * Reminder email sent
     */
    const REMINDER_EMAIL_SENT = 'reminder_email_sent';

    /**
     * Original Base Subtotal
     */
    const ORIGINAL_BASE_SUBTOTAL = 'base_original_subtotal';

    /**
     * Original Subtotal
     */
    const ORIGINAL_SUBTOTAL = 'original_subtotal';

    /**
     * Base Custom Price Total
     */
    const BASE_CUSTOM_PRICE_TOTAL = 'base_custom_price_total';

    /**
     * Custom Price Total
     */
    const CUSTOM_PRICE_TOTAL = 'custom_price_total';

    /**
     * Base Quote Adjustment
     */
    const BASE_QUOTE_ADJUSTMENT = 'base_quote_adjustment';

    /**
     * Quote Adjustment
     */
    const QUOTE_ADJUSTMENT = 'quote_adjustment';

    /**
     * Fixed Shipping Price
     */
    const FIXED_SHIPPING_PRICE = 'fixed_shipping_price';

    /**
     * @return string
     */
    public function getIncrementId();

    /**
     * Sets the increment ID for the quote.
     * @param string $id
     * @return $this
     */
    public function setIncrementId($id);

    /**
     * @return string
     */
    public function getState();

    /**
     * @param $state
     * @return $this
     */
    public function setState($state);

    /**
     * @return string
     */
    public function getStateLabel();

    /**
     * @return string
     */
    public function getStatus();

    /**
     * @param $status
     * @return $this
     */
    public function setStatus($status);

    /**
     * @return string
     */
    public function getStatusLabel();

    /**
     * Sets the proposal sent for the quote.
     * @param string $timestamp
     * @return $this
     */
    public function setProposalSent($timestamp);

    /**
     * @return string
     */
    public function getProposalSent();

    /**
     * Get Original Subtotal
     *
     * @return float
     */
    public function getOriginalSubtotal();

    /**
     * Set Original Subtotal
     *
     * @param float $originalSubtotal
     * @return $this
     */
    public function setOriginalSubtotal($originalSubtotal);

    /**
     * Get Base Original Subtotal
     *
     * @return float
     */
    public function getBaseOriginalSubtotal();

    /**
     * Set Base Original Subtotal
     *
     * @param float $originalBaseSubtotal
     * @return $this
     */
    public function setBaseOriginalSubtotal($originalBaseSubtotal);

    /**
     * Get Base Customer Price Total
     *
     * @return float
     */
    public function getBaseCustomPriceTotal();

    /**
     * Set Base Custom Price Total
     *
     * @param float $baseCustomPriceTotal
     * @return $this
     */
    public function setBaseCustomPriceTotal($baseCustomPriceTotal);

    /**
     * Get Customer Price Total
     *
     * @return float
     */
    public function getCustomPriceTotal();

    /**
     * Set Custom Price Total
     *
     * @param float $customPriceTotal
     * @return $this
     */
    public function setCustomPriceTotal($customPriceTotal);

    /**
     * Get Base Quote Adjustment
     *
     * @return float
     */
    public function getBaseQuoteAdjustment();

    /**
     * Set Base Quote Adjustment
     *
     * @param float $baseQuoteAdjustment
     * @return $this
     */
    public function setBaseQuoteAdjustment($baseQuoteAdjustment);

    /**
     * Get Quote Adjustment
     *
     * @return float
     */
    public function getQuoteAdjustment();

    /**
     * Set Quote Adjustment
     *
     * @param float $quoteAdjustment
     * @return $this
     */
    public function setQuoteAdjustment($quoteAdjustment);

    /**
     * @param boolean $sendRequestEmail
     * @return $this
     */
    public function setSendRequestEmail($sendRequestEmail);

    /**
     * @return boolean
     */
    public function getSendRequestEmail();

    /**
     * @param boolean $requestEmailSent
     * @return $this
     */
    public function setRequestEmailSent($requestEmailSent);

    /**
     * @return boolean
     */
    public function getRequestEmailSent();

    /**
     * @param boolean $sendQuoteCanceledEmail
     * @return $this
     */
    public function setSendQuoteCanceledEmail($sendQuoteCanceledEmail);

    /**
     * @return boolean
     */
    public function getSendQuoteCanceledEmail();

    /**
     * @param boolean $quoteCanceledEmailSent
     * @return $this
     */
    public function setQuoteCanceledEmailSent($quoteCanceledEmailSent);

    /**
     * @return boolean
     */
    public function getQuoteCanceledEmailSent();

    /**
     * @param boolean $sendProposalAcceptedEmail
     * @return $this
     */
    public function setSendProposalAcceptedEmail($sendProposalAcceptedEmail);

    /**
     * @return boolean
     */
    public function getSendProposalAcceptedEmail();

    /**
     * @param boolean $proposalAcceptedEmailSent
     * @return $this
     */
    public function setProposalAcceptedEmailSent($proposalAcceptedEmailSent);

    /**
     * @return boolean
     */
    public function getProposalAcceptedEmailSent();

    /**
     * @param boolean $sendProposalExpiredEmail
     * @return $this
     */
    public function setSendProposalExpiredEmail($sendProposalExpiredEmail);

    /**
     * @return boolean
     */
    public function getSendProposalExpiredEmail();

    /**
     * @param boolean $proposalExpiredEmailSent
     * @return $this
     */
    public function setProposalExpiredEmailSent($proposalExpiredEmailSent);

    /**
     * @return boolean
     */
    public function getProposalExpiredEmailSent();

    /**
     * @param boolean $sendProposalEmail
     * @return $this
     */
    public function setSendProposalEmail($sendProposalEmail);

    /**
     * @return boolean
     */
    public function getSendProposalEmail();

    /**
     * @param boolean $proposalEmailSent
     * @return $this
     */
    public function setProposalEmailSent($proposalEmailSent);

    /**
     * @return boolean
     */
    public function getProposalEmailSent();

    /**
     * @param boolean $sendReminderEmail
     * @return $this
     */
    public function setSendReminderEmail($sendReminderEmail);

    /**
     * @return boolean
     */
    public function getSendReminderEmail();

    /**
     * @param boolean $reminderEmailSent
     * @return $this
     */
    public function setReminderEmailSent($reminderEmailSent);

    /**
     * @return boolean
     */
    public function getReminderEmailSent();

    /**
     * Set fixed shipping price
     *
     * @param float $fixedShippingPrice
     * @return $this
     */
    public function setFixedShippingPrice($fixedShippingPrice);

    /**
     * Get fixed shipping price
     *
     * @return float
     */
    public function getFixedShippingPrice();
}
