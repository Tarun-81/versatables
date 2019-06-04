/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define(
    [
        'ko',
        'underscore',
        'uiRegistry',
    ],
    function (
        ko,
        _,
        registry
    ) {
        'use strict';

        var mixin = _.extend({
            validate: function () {
                this._super();
                var checkBoll = true;
                var form = 'form[data-role=purchaseorder-form]';
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    checkoutProvider.set('params.invalid', false);
                    checkoutProvider.trigger('billingAddress.custom_attributes.data.validate');
                    checkBoll = checkoutProvider.get('params.invalid');
                });
                
                return jQuery(form).validation() && jQuery(form).validation('isValid') && !checkBoll;
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    }
);
