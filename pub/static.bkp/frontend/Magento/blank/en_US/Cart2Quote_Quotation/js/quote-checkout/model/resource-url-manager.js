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
        'mage/url',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/url-builder',
        'mageUtils'
    ],
    function (url, customer, urlBuilder, utils) {
        "use strict";

        /**
         * A model for handling the action URL's
         */
        return {

            /**
             * Get the update quote session URL.
             * @param quote
             * @returns string
             * @param params
             */
            getUrlForRedirectOnSuccess: function (quote, params) {
                var urls = {
                    'guest': 'quotation/quote/success',
                    'customer': 'quotation/quote/success'
                };
                return this.getUrl(urls, params, false);
            },

            /**
             * Get the update quote session URL.
             * @param quote
             * @returns string
             * @param params
             */
            getUrlForUpdateQuote: function (quote, params) {
                var urls = {
                    'guest': 'quotation/quote_ajax/updateQuote',
                    'customer': 'quotation/quote_ajax/updateQuote'
                };
                return this.getUrl(urls, params, false);
            },

            /**
             * Get the create quote URL.
             * @param quote
             * @returns string
             * @param params
             */
            getUrlForPlaceQuote: function (quote, params) {
                var urls = {
                    'guest': 'quotation/quote_ajax/createQuote',
                    'customer': 'quotation/quote_ajax/createQuote'
                };
                return this.getUrl(urls, params, false);
            },

            /**
             * Get url for service
             * @return string
             */
            getUrl: function (urls, urlParams, apiCall) {
                var newUrl;

                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }

                if (!utils.isEmpty(urls['default'])) {
                    newUrl = urls['default'];
                } else {
                    newUrl = urls[this.getCheckoutMethod()];
                }

                if (apiCall) {
                    return urlBuilder.createUrl(newUrl, urlParams);
                } else {
                    return url.build(newUrl) + this.prepareParams(urlParams);
                }
            },

            /**
             * Get the checkout method
             * @returns {string}
             */
            getCheckoutMethod: function () {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            },

            /**
             * Format params
             *
             * @param {Object} params
             * @returns {string}
             */
            prepareParams: function (params) {
                var result = '?';

                _.each(params, function (value, key) {
                    result += key + '=' + value + '&';
                });

                return result.slice(0, -1);
            }
        };
    }
);
