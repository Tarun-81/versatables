/*
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

define(
    [
        'jquery',
        'Magento_Checkout/js/view/shipping',
        'ko',
        'Cart2Quote_Quotation/js/quote-checkout/action/place-quote',
        'Cart2Quote_Quotation/js/quote-checkout/action/update-quote',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/quote',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer'
    ],
    function ($,
              Component,
              ko,
              placeQuoteAction,
              updateQuoteAction,
              setShippingInformationAction,
              shippingService,
              quote,
              emailFormUsageObserver) {
        'use strict';

        return Component.extend({
            allowToUseForm: emailFormUsageObserver.showNonGuestField
        });
    }
);
