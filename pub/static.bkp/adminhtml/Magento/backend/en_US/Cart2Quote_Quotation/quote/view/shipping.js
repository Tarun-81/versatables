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


define([
    "jquery"
], function ($) {
    'use strict';

    $.widget('quotation.shipping', {
        options: {
            selector: {
                input: undefined,
                price: undefined,
                submit: undefined
            },
            method: undefined
        },

        _create: function () {
            var self = this;
            $(self.element).click(function (event) {
                event.preventDefault();
                $(self.options.selector.input).toggle();
                $(self.options.selector.price).toggle();
            });

            $(self.options.selector.submit).click(function (event) {
                event.preventDefault();

                /** @see AdminQuote.setShippingMethodWithPrice */
                window.quote.setShippingMethodWithPrice(
                    self.options.method,
                    $(self.options.selector.input + " input").val()
                );
            });
        }
    });

    return $.mage.shipping;
});