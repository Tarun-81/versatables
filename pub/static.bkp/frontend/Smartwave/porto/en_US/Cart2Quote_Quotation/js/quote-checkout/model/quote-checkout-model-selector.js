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
        'ko',
        'jquery'
    ],
    function (ko, $) {
        "use strict";

        /**
         * A model that helps with selecting KO models
         */
        return {
            /** Selectors: BEGIN */
            loginSelector: 'form[data-role=email-with-possible-login]',
            guestSelector: '#co-guest-form',
            shippingSelector: '#co-shipping-method-form',
            billingSelector: '.billing-address-form',
            quotationSelector: '#quotation-fields',
            guestCheckoutSelector: '#co-guest-form',
            /** Selectors: END */

            /**
             * Get the shipping KO model
             */
            getShippingModel: function () {
                return ko.dataFor($(this.shippingSelector)[0]);
            },

            /**
             * Get the billing KO model
             */
            getBillingModel: function () {
                return ko.dataFor($(this.billingSelector)[0]);
            },

            /**
             * Get the quotation KO model
             */
            getQuotationModel: function () {
                return ko.dataFor($(this.quotationSelector)[0]);
            },

            /**
             * Get the guest checkout KO model
             */
            getGuestCheckoutModel: function () {
                return ko.dataFor($(this.guestCheckoutSelector)[0]);
            },

            /**
             * Get the login KO model
             */
            getLoginModel: function () {
                return ko.dataFor($(this.loginSelector)[0]);
            },

            /**
             * Get the login form
             */
            getLoginForm: function () {
                return $(this.loginSelector);
            },

            /**
             * Get the login form field
             */
            getLoginFormField: function () {
                return $(this.loginSelector + ' input[name=username]');
            },

            /**
             * Get the login KO model
             */
            getGuestModel: function () {
                return ko.dataFor($(this.guestSelector)[0]);
            },

            /**
             * Check if shipping address is available
             * @returns {boolean}
             */
            hasShippingAddress: function () {
                return $(this.shippingSelector).length > 0;
            },

            /**
             * Check if billing address is available
             * @returns {boolean}
             */
            hasBillingAddress: function () {
                return $(this.billingSelector).length > 0;
            },

            /**
             * Check if quotation fields are available
             * @returns {boolean}
             */
            hasQuotationFields: function () {
                return $(this.quotationSelector).length > 0;
            },

            /**
             * Check if guest checkout fields are available
             * @returns {boolean}
             */
            hasGuestCheckoutFields: function () {
                return $(this.guestCheckoutSelector).length > 0;
            },

            /**
             * Check if the guest model is available
             * @returns {boolean}
             */
            hasGuestModel: function () {
                return $(this.guestSelector).length > 0;
            },

            /**
             * Check if the login model is available
             * @returns {boolean}
             */
            hasLoginModel: function () {
                return $(this.loginSelector).length > 0;
            }
        };
    }
);
