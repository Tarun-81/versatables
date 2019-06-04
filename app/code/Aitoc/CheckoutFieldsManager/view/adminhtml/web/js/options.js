/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define([
    'jquery',
    'Magento_Catalog/js/options',
    'uiRegistry',
    'prototype',
    'jquery/ui'
], function (jQuery, catalogOptions, rg) {
    'use strict';

    return function (config) {
        catalogOptions(config);
        window.attributeOption.getOptionInputType = function () {
            var optionDefaultInputType = 'radio';

            if ($('frontend_input') && ($('frontend_input').value === 'multiselect' || $('frontend_input').value === 'checkbox')) {
                optionDefaultInputType = 'checkbox';
            }

            return optionDefaultInputType;
        };

        rg.set('manage-options-panel', window.attributeOption);
        window.optionDefaultInputType = window.attributeOption.getOptionInputType();
    };

});
