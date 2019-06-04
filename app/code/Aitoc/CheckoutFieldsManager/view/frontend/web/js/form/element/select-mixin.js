/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define(['underscore'], function (_) {
        'use strict';
        
        /**
         * Parses incoming options, considers options with undefined value property
         *     as caption
         *
         * @param  {Array} nodes
         * @return {Object}
         */
        function parseOptions(nodes, captionValue) {
            var caption,
                value;

            nodes = _.map(nodes, function (node) {
                value = node.value;

                if (value === null || value === captionValue) {
                    if (_.isUndefined(caption)) {
                        caption = node.label;
                    }
                } else {
                    return node;
                }
            });

            return {
                options: _.compact(nodes),
                caption: _.isString(caption) ? caption : false
            };
        }

        /**
         * Recursively set to object item like value and item.value like key.
         *
         * @param {Array} data
         * @param {Object} result
         * @returns {Object}
         */
        function indexOptions(data, result) {
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

        var mixin = _.extend({
            setOptions: function (data) {
                var captionValue = this.captionValue || '',
                    result = parseOptions(data, captionValue),
                    isVisible;

                this.indexedOptions = indexOptions(result.options);

                this.options(result.options);

                if (!this.caption()) {
                    this.caption(result.caption);
                }

                if (this.customEntry) {
                    isVisible = !!result.options.length;

                    this.setVisible(isVisible);
                    this.toggleInput(!isVisible);
                }

                return this;
            }
        });

        return function (target) {
            return target.extend(mixin);
        };
    }
);
