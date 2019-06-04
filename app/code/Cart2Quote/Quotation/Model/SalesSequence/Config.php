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

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Class Config - configuration container for sequence
 */
class Config extends \Magento\SalesSequence\Model\Config
{
    /**
     * Default sequence values
     * Prefix represents prefix for sequence: AA000
     * Suffix represents suffix: 000AA
     * startValue represents initial value
     * warning value will be using for alert messages when increment closing to overflow
     * maxValue represents last available increment id in system
     * @var array
     */
    protected $defaultValues = [
        'prefix' => 'VERSA.',
        'suffix' => '',
        'startValue' => 1,
        'step' => 1,
        'warningValue' => 4294966295, 
        'maxValue' => 4294967295,
		              
    ];
}
