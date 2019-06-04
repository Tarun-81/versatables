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

namespace Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Plugin\Magento\Quote\Model\ResourceModel
 */
class Quote
{
    /**
     * Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;

    /**
     * Quotation helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * Quote constructor.
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper
    ) {
        $this->quotationHelper = $quotationHelper;
        $this->quoteSession = $quoteSession;
    }

    /**
     * Function that make sure that this function doesn't load a quotation quote as a cart
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote $subject
     * @param callable $proceed
     * @param \Magento\Quote\Model\Quote $quote
     * @param int $customerId
     * @return \Magento\Quote\Model\Quote $quote
     */
    public function aroundLoadByCustomerId(
        $subject,
        $proceed,
        $quote,
        $customerId
    ) {
        if (!$this->quotationHelper->isFrontendEnabled()) {
            return $proceed($quote, $customerId);
        }

        $quotationQuote = $this->quoteSession->getQuotationQuote();
        if (isset($quotationQuote) && $quotationQuote > 0) {
            $quotationQuote = 1;
        } else {
            $quotationQuote = 0;
        }

        $connection = $subject->getConnection();
        $select = $this->_getLoadSelect(
            'customer_id',
            $customerId,
            $quote,
            $subject
        )->where(
            'is_active = ?',
            1
        )->where(
            'is_quotation_quote = ?',
            $quotationQuote
        )->order(
            'updated_at ' . \Magento\Framework\DB\Select::SQL_DESC
        )->limit(
            1
        );

        $data = $connection->fetchRow($select);
        if ($data) {
            $quote->setData($data);
        }
        $subject->afterLoad($quote);

        return $quote;
    }

    /**
     * @param $field
     * @param $value
     * @param $object
     * @param $subject
     * @return mixed
     */
    protected function _getLoadSelect(
        $field,
        $value,
        $object,
        $subject
    ) {
        $field = $subject->getConnection()->quoteIdentifier(sprintf('%s.%s', $subject->getMainTable(), $field));
        $select = $subject->getConnection()->select()->from($subject->getMainTable())->where($field . '=?', $value);
        $storeIds = $object->getSharedStoreIds();
        if ($storeIds) {
            $select->where('store_id IN (?)', $storeIds);
        } else {
            $select->where('store_id < ?', 0);
        }

        return $select;
    }
}
