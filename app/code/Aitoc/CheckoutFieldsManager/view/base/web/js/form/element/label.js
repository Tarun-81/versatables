/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

define([
    'jquery',
    'underscore',
    'mageUtils',
    'uiRegistry',
    'ko',
    'Magento_Ui/js/form/element/abstract'
], function ($, _, utils, registry, ko, Abstract) {
    'use strict';

    return Abstract.extend({

        initConfig: function () {
            this._super();

            this.value = this.normalizeData(this.value);

            return this;
        },

        /**
         * @inheritdoc
         */
        setInitialValue: function () {
            this.initialValue = this.getInitialValue();

            if (this.value.peek() !== this.initialValue) {
                this.value(this.initialValue);
            }
            this.on('value', this.onUpdate.bind(this));
            this.isUseDefault(this.disabled());

            return this;
        },
    });
});
