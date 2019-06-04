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
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/shipping-service'
    ],
    function ($, ko, Component, shippingService) {
        'use strict';

        /**
         * A model to set the email, first name and last name
         * when a quest uses Guests checkout and Cart2Quote.
         */
        return Component.extend({

            /**
             * Checks if the data is already set
             */
            loaded: false,

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this._super();
                var self = this;

                /** Fire code when shipping service is done loading */
                shippingService.isLoading.subscribe(function (loading) {
                    if (!self.loaded && !loading && checkoutConfig.quotationGuestCheckout) {
                        /** Quote data */
                        var quotationCustomerData = checkoutConfig.quotationCustomerData;

                        /**
                         * An array with an object containing a selector and value.
                         * These are the fields that need to be set on the checkout
                         *
                         * @type {*[]}
                         */
                        var fields = [
                            {
                                selector: 'form[data-role=email-with-possible-login] [name="username"]',
                                value: quotationCustomerData.email
                            },
                            {
                                selector: '#shipping-new-address-form [name="firstname"]',
                                value: quotationCustomerData.firstname
                            },
                            {
                                selector: '#shipping-new-address-form [name="lastname"]',
                                value: quotationCustomerData.lastname
                            }
                        ];

                        /** Process the array */
                        $.each(fields, function (id, field) {
                            $(field.selector).val(field.value);
                            $(field.selector).change();
                        });

                        self.loaded = true;
                    }
                });
                return this;
            }
        });
    }
);
