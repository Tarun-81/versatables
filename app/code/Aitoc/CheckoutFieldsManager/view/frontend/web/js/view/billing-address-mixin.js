/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define(
    [
        'ko',
        'Magento_Ui/js/form/form',
        'underscore',
        'Magento_Checkout/js/model/quote'
    ],
    function (
        ko,
        Component,
        _,
        quote
    ) {
        'use strict';

        var mixin = _.extend({
            initObservable: function () {
                this._super();

                quote.billingAddress.subscribe(function (newAddress) {
                    if (newAddress != null && this.source.get('billingAddress.custom_attributes')) {
                        newAddress.customAttributes = this.source.get('billingAddress.custom_attributes');
                    }
                }, this);

                return this;
            },

            validate: function () {
                //this._super();
                if (this.source.get('billingAddress.custom_attributes')) {
                    this.source.trigger('billingAddress.custom_attributes.data.validate');
                }
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    }
);
