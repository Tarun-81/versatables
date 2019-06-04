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
                registry.async('checkoutProvider')(function (checkoutProvider) {
                    checkoutProvider.set('params.invalid', false);
                    checkoutProvider.trigger('billingAddress.custom_attributes.data.validate');
                    checkBoll = checkoutProvider.get('params.invalid');
                });

             return !checkBoll;
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    }
);
