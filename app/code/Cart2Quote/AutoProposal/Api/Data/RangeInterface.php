<?php
/**
 *
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2017] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 */

namespace Cart2Quote\AutoProposal\Api\Data;

/**
 * Interface RangeInterface
 *
 * @package Cart2Quote\AutoProposal\Api\Data
 */
interface RangeInterface
{
    const DISCOUNT_IDENTIFIER = 'discount';
    const DISABLE_AUTOPROPOSAL_IDENTIFIER = 'disable_autoproposal';
    const ENABLE_SHIPPING_IDENTIFIER = 'enable_shipping';
    const SHIPPING_AMOUNT_IDENTIFIER = 'shipping_amount';
    const NOTIFY_SALESREP_IDENTIFIER = 'notify_salesrep';
    const MIN_VALUE_IDENTIFIER = 'min_value';
    const MAX_VALUE_IDENTIFIER = 'max_value';

    public function getDiscount();

    public function getDisableAutoProposal();

    public function getEnableShipping();

    public function getShippingAmount();

    public function getNotifySalesrep();

    public function getMinValue();

    public function getMaxValue();
}