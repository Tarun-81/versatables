define([
    "jquery"
], function ($) {
    'use strict';

    $.widget('mage.quotationShipping', {
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
                window.order.setShippingMethodWithPrice(
                    self.options.method,
                    $(self.options.selector.input + " input").val()
                );
            });
        }
    });

    return $.mage.quotationShipping;
});