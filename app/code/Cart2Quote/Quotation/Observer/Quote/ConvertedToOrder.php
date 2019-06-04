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

namespace Cart2Quote\Quotation\Observer\Quote;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class ConvertedToOrder
 * @package Cart2Quote\Quotation\Observer\Quote
 */
class ConvertedToOrder implements ObserverInterface
{
    /**
     * Quote factory
     *
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    protected $quoteFactory;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Mage Quote Factory
     *
     * @var \Magento\Quote\Model\QuoteFactory
     */
    protected $mageQuoteFactory;

    /**
     * ConvertedToOrder constructor.
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteFactory $mageQuoteFactory
    ) {
        $this->quoteFactory = $quoteFactory;
        $this->checkoutSession = $checkoutSession;
        $this->mageQuoteFactory = $mageQuoteFactory;
    }

    /**
     * Set the quote to complete and ordered
     *
     * @param EventObserver $observer
     * @return void
     */
    public function execute(EventObserver $observer)
    {
        $quote = $this->quoteFactory->create()->load($observer->getQuote()->getLinkedQuotationId());
        if ($quote->getQuoteId()) {
            $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_COMPLETED)
                ->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_ORDERED)->save();

            $this->checkoutSession->replaceQuote(
                $this->getEmptyCheckoutQuote($quote->getStoreId())
            );
        }
    }

    /**
     * Get empty checkout quote
     *
     * @param int $storeId
     * @return \Magento\Quote\Model\Quote
     */
    protected function getEmptyCheckoutQuote($storeId)
    {
        return $this->mageQuoteFactory->create()
            ->setStoreId($storeId)
            ->setIsActive(true)
            ->collectTotals()
            ->save();
    }
}
