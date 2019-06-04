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

namespace Cart2Quote\Quotation\Plugin\Events;

/**
 * Class Converter
 * @package Cart2Quote\Quotation\Plugin\Events
 */
class Converter
{
    /**
     * @param \Magento\Framework\Event\Config\Converter $subject
     * @param $results
     * @return array
     */
    public function afterConvert(\Magento\Framework\Event\Config\Converter $subject, $results)
    {
        foreach ($results as $eventName => $eventConfig) {
            if (strpos($eventName, 'carttoquote') !== false) {
                unset($results[$eventName]);
                $eventName = str_replace('carttoquote', 'cart2quote', $eventName);
                $results[$eventName] = $eventConfig;
            }
        }

        return $results;
    }
}