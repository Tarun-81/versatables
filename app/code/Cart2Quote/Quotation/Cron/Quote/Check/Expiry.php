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

namespace Cart2Quote\Quotation\Cron\Quote\Check;

/**
 * Class Expiry
 * @package Cart2Quote\Quotation\Cron\Quote\Check
 */
class Expiry
{
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender
     */
    protected $proposalExpireSender;

    /**
     * Expiry constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender $proposalExpireSender
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Model\Quote\Email\Sender\QuoteProposalExpireSender $proposalExpireSender
    ) {

        $this->collectionFactory = $collectionFactory;
        $this->proposalExpireSender = $proposalExpireSender;
    }

    /**
     * Check for expired quotes
     */
    public function execute()
    {
        $availableStatus = [
            \Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_SENT
        ];

        $quotes = $this->collectionFactory->create()
            ->addFieldToSelect('*')
            ->addFieldToFilter('expiry_enabled', ['eq' => 1])
            ->addFieldToFilter('is_quote', ['eq' => 1])
            ->addFieldToFilter('status', ['in' => $availableStatus])
            ->setOrder('created_at', 'desc');

        /**
         * @var \Cart2Quote\Quotation\Model\Quote $quote
         */
        foreach ($quotes as $quote) {
            $expiryDate = $quote->getExpiryDate();
            if (!is_null($expiryDate)) {
                if ($expiryDate == date('Y-m-d') && !$quote->getProposalExpiredEmailSent()) {
                    //Proposal expires today
                    $this->proposalExpireSender->send($quote);
                } else {
                    if ($expiryDate <= date('Y-m-d')) {
                        $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_PENDING);
                        $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_PROPOSAL_EXPIRED);
                    }
                }
            }
        }
    }
}