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
            },

            getCfmAttributeLabel: function (attributeName) {
                var cfmAttributes = cfmCheckoutConfig.attributes;

                return cfmAttributes[attributeName].label;
            },

            getCfmAttributeTitle: function (attributeName, attributeValue) {
                var cfmAttributes = cfmCheckoutConfig.attributes;

                var attributeOptions = cfmAttributes[attributeName].options;

                if (!attributeOptions) {
                    return attributeValue;
                }

                if (!Array.isArray(attributeValue)) {
                    return attributeOptions[attributeValue];
                }

                return _.map(attributeValue, function (item) {
                    return attributeOptions[item];
                });
            },

            isVisible: function (attributeName) {
                var cfmAttributes = cfmCheckoutConfig.attributes;

                return cfmAttributes[attributeName].is_visible == '1';
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    });
