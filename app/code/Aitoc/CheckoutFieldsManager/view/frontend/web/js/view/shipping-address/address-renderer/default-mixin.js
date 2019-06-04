/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define([
        'underscore',
        'Aitoc_CheckoutFieldsManager/js/model/checkoutConfig'
    ], function (_, cfmCheckoutConfig) {
        'use strict';

        var mixin = _.extend({
            isCfmAttribute: function (attributeName) {
                var cfmAttributes = cfmCheckoutConfig.attributes;

                return (typeof cfmAttributes[attributeName] !== 'undefined');
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    });
