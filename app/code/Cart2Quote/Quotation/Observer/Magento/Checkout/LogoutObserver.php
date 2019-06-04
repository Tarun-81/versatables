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

namespace Cart2Quote\Quotation\Observer\Magento\Checkout;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class ConfirmModeObserver
 * @package Cart2Quote\Quotation\Observer\Magento\Checkout
 */
class LogoutObserver implements ObserverInterface
{
    /**
     * Data helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $helper;

    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Quote Repository
     *
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;

    /**
     * ConfirmModeObserver constructor.
     * @param \Cart2Quote\Quotation\Helper\Data $helper
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $helper,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository
    ) {
        $this->helper = $helper;
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Remove confirmation mode from session
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $activeMode = $this->helper->getActiveConfirmMode();
        if ($activeMode) {
            $this->helper->setActiveConfirmMode(false);
            $this->clearCart();
        }
    }

    /**
     * Deletes the quote which was created from quotation quote
     *
     */
    public function clearCart()
    {
        $quote = $this->checkoutSession->getQuote();

        if ($quote->getLinkedQuotationId()) {
            $this->quoteRepository->delete($quote);
        }
    }
}
