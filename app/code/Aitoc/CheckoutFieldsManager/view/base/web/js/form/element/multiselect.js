/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define([
    'underscore',
    'mageUtils',
    'Magento_Ui/js/form/element/select'
], function (_, utils, Select) {
    'use strict';

    return Select.extend({
        defaults: {
            size: 5
        },

        setOptions: function (data) {
            var isVisible;
            var label;

            this.indexedOptions = indexOptions(data);
            this.options(data);
            this.size = _.size(this.indexedOptions) + 1;
            if (this.customEntry) {
                isVisible = !!data.length;
                this.setVisible(isVisible);
                this.toggleInput(!isVisible);
            }

            return this;
        },

        /**
         * Splits incoming string value.
         *
         * @returns {Array}
         */
        normalizeData: function (value) {
            if (utils.isEmpty(value)) {
                value = [];
            }
            return _.isString(value) ? value.split(',') : value;
        },

        /**
         * Defines if value has changed
         *
         * @returns {Boolean}
         */
        hasChanged: function () {
            var value = this.value(),
                initial = this.initialValue;

            return !utils.equalArrays(value, initial);
        }
    });

    function indexOptions(data, result)
    {
        var value;

        result = result || {};

        data.forEach(function (item) {
            value = item.value;

            if (Array.isArray(value)) {
                indexOptions(value, result);
            } else {
                result[value] = item;
            }
        });

        return result;
    }

});
