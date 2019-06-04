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
 * Class LoadCustomerQuoteObserver
 * @package Cart2Quote\Quotation\Observer\Magento\Checkout
 */
class LoadCustomerQuoteObserver implements ObserverInterface
{
    /**
     * Checkout session
     *
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Message Manager
     *
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * LoadCustomerQuoteObserver constructor.
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteSession = $quoteSession;
        $this->messageManager = $messageManager;
        $this->quotationHelper = $quotationHelper;
    }

    /**
     * Load the quote on the checkout session and on the quote session.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->quotationHelper->isFrontendEnabled()) {
                $this->quoteSession->loadCustomerQuote();
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Load customer quote error'));
        }
    }
}
