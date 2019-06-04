/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define([
    'mageUtils',
    'Magento_Ui/js/form/element/checkbox-set'
], function (utils, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            template: 'Aitoc_CheckoutFieldsManager/form/element/checkbox-set',
            multiple: true
        },

        normalizeData: function (value) {
            if (!this.multiple) {
                return this._super();
            }
            if (!_.isArray(value)) {
                if (typeof value == 'string') {
                    var defaults = [];
                    defaults = value.split(",");
                    value = [];
                    value = defaults;
                } else {
                    var tempValue = value;
                    value = [];
                    value.push(tempValue);
                }
            }

            return _.isArray(value) ? utils.copy(value) : [];
        },
    });
});
