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

namespace Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History;

/**
 * Flat quotation quote status history collection
 */
class Collection extends \Cart2Quote\Quotation\Model\ResourceModel\Quote\Collection\AbstractCollection
    implements \Cart2Quote\Quotation\Api\Data\QuoteStatusHistorySearchResultInterface
{
    /**
     * Event prefix
     * @var string
     */
    protected $_eventPrefix = 'quotation_quote_status_history_collection';

    /**
     * Event object
     * @var string
     */
    protected $_eventObject = 'quote_status_history_collection';

    /**
     * Get history object collection for specified instance (quote, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: \Cart2Quote\Quotation\Model\Quote
     * @param \Magento\Sales\Model\AbstractModel $instance
     * @return \Cart2Quote\Quotation\Model\Quote\Status\History|null
     */
    public function getUnnotifiedForInstance($instance)
    {
        if (!$instance instanceof \Cart2Quote\Quotation\Model\Quote) {
            $instance = $instance->getQuote();
        }
        $this->setQuoteFilter(
            $instance
        )->setOrder(
            'created_at',
            'desc'
        )->addFieldToFilter(
            'entity_name',
            $instance->getEntityType()
        )->addFieldToFilter(
            'is_customer_notified',
            0
        )->setPageSize(
            1
        );
        foreach ($this->getItems() as $historyItem) {
            return $historyItem;
        }
        return null;
    }

    /**
     * Model initialization
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Cart2Quote\Quotation\Model\Quote\Status\History',
            'Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\History'
        );
    }
}
