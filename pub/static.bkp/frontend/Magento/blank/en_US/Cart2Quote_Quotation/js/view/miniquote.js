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
    'uiComponent',
    'Magento_Customer/js/customer-data',
    'jquery',
    'ko',
    'sidebar'
], function (Component, customerData, $, ko) {
    'use strict';

    var sidebarInitializedQuote = false;
    var addToQuoteCalls = 0;

    var miniquote = $("[data-block='miniquote']");
    miniquote.on('dropdowndialogopen', function () {
        initSidebarQuote();
    });

    function initSidebarQuote() {
        if (!$('[data-role=product-item]').length) {
            return false;
        }
        miniquote.trigger('contentUpdated');
        if (sidebarInitializedQuote) {
            return false;
        }
        sidebarInitializedQuote = true;
        miniquote.sidebar({
            "targetElement": "div.block.block-miniquote",
            "url": {
                "checkout": window.quotation.checkoutUrl,
                "update": window.quotation.updateItemQtyUrl,
                "remove": window.quotation.removeItemUrl
            },
            "button": {
                "checkout": "#top-quote-btn-checkout",
                "remove": "#mini-quote a.action.delete",
                "close": "#btn-miniquote-close"
            },
            "showquote": {
                "parent": "span.counter",
                "qty": "span.counter-number",
                "label": "span.counter-label"
            },
            "miniquote": {
                "list": "#mini-quote",
                "content": "#miniquote-content-wrapper",
                "qty": "div.items-total",
                "subtotal": "div.subtotal span.price"
            },
            "item": {
                "qty": ":input.quote-item-qty",
                "button": ":button.update-quote-item"
            },
            "confirmMessage": $.mage.__(
                'Are you sure you would like to remove this item from the quote?'
            )
        });
    }

    return Component.extend({
        quoteCartUrl: window.quotation.quoteCartUrl,
        initialize: function () {
            var self = this;
            this._super();

            //fix for cached data
            customerData.reload(['quote'], false);

            this.quote = customerData.get('quote');
            this.quote.subscribe(function () {
                addToQuoteCalls--;
                this.isLoading(addToQuoteCalls > 0);
                sidebarInitializedQuote = false;
                initSidebarQuote();
            }, this);
            $('[data-block="miniquote"]').on('contentLoading', function (event) {
                addToQuoteCalls++;
                self.isLoading(true);
            });
        },
        isLoading: ko.observable(false),
        initSidebarQuote: initSidebarQuote,
        closeSidebar: function () {
            var miniquote = $('[data-block="miniquote"]');
            miniquote.on('click', '[data-action="close"]', function (event) {
                event.stopPropagation();
                miniquote.find('[data-role="dropdownDialog"]').dropdownDialog("close");
            });
            return true;
        },
        getItemRenderer: function (productType) {
            return this.itemRenderer[productType] || 'defaultRenderer';
        }
    });
});
