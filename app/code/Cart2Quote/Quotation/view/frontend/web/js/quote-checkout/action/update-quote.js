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
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/place-order',
        'Cart2Quote_Quotation/js/quote-checkout/action/redirect-on-success',
        'Cart2Quote_Quotation/js/quote-checkout/model/resource-url-manager',
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/full-screen-loader',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation'
    ],
    function ($,
              quote,
              placeOrderService,
              redirectOnSuccessAction,
              resourceUrlManagerModel,
              $t,
              globalMessageList,
              fullScreenLoader,
              checkoutDataQuotation) {
        'use strict';

        /**
         * This action handles the update quotation action
         */
        return function () {
            var quoteRequestUrl, quoteData;

            /**
             * The quote data that is being send wit the request
             *
             * @type {{cartId: *, form_key: *, quotation_data}}
             */
            quoteData = {
                cartId: quote.getQuoteId(),
                form_key: $.mage.cookies.get('form_key'),
                quotation_guest_field_data: JSON.stringify(checkoutDataQuotation.getQuotationGuestFieldsFromData()),
                quotation_field_data: JSON.stringify(checkoutDataQuotation.getQuotationFieldsFromData()),
                quotation_product_data: JSON.stringify(checkoutDataQuotation.getQuotationProductsFromData()),
                quotation_store_config_data: JSON.stringify(checkoutDataQuotation.getQuotationConfigDataFromData())
            };

            /**
             * Get request URL
             *
             * @type {*|string}
             */
            quoteRequestUrl = resourceUrlManagerModel.getUrlForUpdateQuote(quote, quoteData);

            /**
             * Request quotation update
             *
             * @see Quotation/Controller/Quote/Ajax/UpdateQuote.php
             */
            return placeOrderService(quoteRequestUrl, quoteData, globalMessageList);
        };
    }
);
