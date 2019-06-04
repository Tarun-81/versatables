<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Helper;

use Magento\Quote\Model\Quote\Address;

class Data
{
    /**
     * @param string $area
     *
     * @return string|null
     */
    public static function getCheckoutStepByDisplayArea($area)
    {
        if (!is_string($area) || !$area || strlen($area) < 3) {
            return null;
        }
        if (strpos($area, 'shipping') !== false) {
            return Address::ADDRESS_TYPE_SHIPPING;
        }

        return Address::ADDRESS_TYPE_BILLING;
    }
}
