/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define(['mage/utils/wrapper'],
    function (wrapper) {
        'use strict';

        return function (target) {

            /**
             * Converts PHP IntlFormatter format to moment format.
             *
             * @param {String} format - PHP format
             * @returns {String} - moment compatible formatting
             */
            target.convertToMomentFormat = wrapper.wrap(target.convertToMomentFormat, function(originalFunction, format) {
                var newFormat = format;

                newFormat = newFormat.replace(/yyyy|yyy|YYY|y|Y/g, 'YYYY');
                newFormat = newFormat.replace(/yy/g, 'YY');
                newFormat = newFormat.replace(/dd/g, 'DD');
                newFormat = newFormat.replace(/d/g, 'D');

                return newFormat;
            });

            return target;
        };
    }
);
