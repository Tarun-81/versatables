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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\View\Tab;

/**
 * Quote history tab
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class History extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\History implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Template
     * @var string
     */
    protected $_template = 'quote/view/tab/history.phtml';

    /**
     * Compose and get quote full history.
     * Consists of the status history comments as well as of invoices, shipments and creditmemos creations
     *
     * @return array
     */
    public function getFullHistory()
    {
        $quote = $this->getQuote();

        $history = [];
        foreach ($quote->getAllStatusHistory() as $quoteComment) {
            $history[] = $this->_prepareHistoryItem(
                $quoteComment->getStatusLabel(),
                $quoteComment->getIsCustomerNotified(),
                $this->getQuoteAdminDate($quoteComment->getQuotationCreatedAt()),
                $quoteComment->getComment()
            );
        }

        usort($history, [__CLASS__, 'sortHistoryByTimestamp']);
        return $history;
    }

    /**
     * Retrieve quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Get quote admin date
     * @param int $createdAt
     * @return \DateTime
     */
    public function getQuoteAdminDate($createdAt)
    {
        return $this->_localeDate->date(new \DateTime($createdAt));
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Quote History');
    }

    /**
     * Customer Notification Applicable check method
     * @param array $historyItem
     * @return bool
     */
    public function isCustomerNotificationNotApplicable($historyItem)
    {
        return $historyItem['notified'] ==
            \Cart2Quote\Quotation\Model\Quote\Status\History::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }
}
