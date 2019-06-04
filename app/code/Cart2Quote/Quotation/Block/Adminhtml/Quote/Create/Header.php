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

namespace Cart2Quote\Quotation\Block\Adminhtml\Quote\Create;

/**
 * Create order/quote form header
 */
class Header extends \Magento\Sales\Block\Adminhtml\Order\Create\Header
{
    /**
     * {@inheritdoc}
     */
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return __('Edit Quote #%1', $this->_getSession()->getOrder()->getIncrementId());
        }
        $out = $this->_getCreateOrderTitle();
        return $this->escapeHtml($out);
    }

    /**
     * Generate title for new order creation page.
     * @return string
     */
    protected function _getCreateOrderTitle()
    {
        $customerId = $this->getCustomerId();
        $storeId = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out .= __(
                'Create Quote for %1 in %2',
                $this->_getCustomerName($customerId),
                $this->getStore()->getName()
            );
            return $out;
        } elseif (!$customerId && $storeId) {
            $out .= __('Create Quote for New Customer in %1', $this->getStore()->getName());
            return $out;
        } elseif ($customerId && !$storeId) {
            $out .= __('Create Quote for %1', $this->_getCustomerName($customerId));
            return $out;
        } elseif (!$customerId && !$storeId) {
            $out .= __('Create Quote for New Customer');
            return $out;
        }

        return $out;
    }
}
