/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'underscore',
    'Magento_Ui/js/modal/modal-component'
], function (registry, _, ModalComponent) {
    'use strict';

    return ModalComponent.extend({
        defaults: {
            requiredFields: {},
            downloadsLimitField: null
        },

        /**
         * Open modal
         */
        openModal: function () {
            this._super();

            // set required fields objects when open modal
            this.requiredFields.sectionField        = registry.get('index = ' + this.indexies.section_field);
            this.requiredFields.multiFileField      = registry.get('index = ' + this.indexies.multi_file_field);
            this.requiredFields.urlField            = registry.get('index = ' + this.indexies.url_field);
            this.requiredFields.customerGroupsField = registry.get('index = ' + this.indexies.customer_groups_field);
            this.requiredFields.storesField         = registry.get('index = ' + this.indexies.stores_field);
            this.requiredFields.isActiveField       = registry.get('index = ' + this.indexies.is_active_field);

            this.downloadsLimitField = registry.get('index = ' + this.indexies.downloads_limit_field);

            for (var field in this.requiredFields) {
                this.requiredFields[field].validation['required-entry'] = true;
            }

            this.requiredFields.urlField.validation['validate-url']             = true;
            this.downloadsLimitField.validation['validate-not-negative-number'] = true;
        },

        validateRequiredFields: function () {
            var countValidFields = 0;
            var countFields      = 0;

            for (var field in this.requiredFields) {
                countFields++;

                if (this.requiredFields[field].validate().valid) {
                    countValidFields++;
                }
            }

            countFields++;

            if (this.downloadsLimitField.validate().valid) {
                countValidFields++;
            }

            if (countValidFields == countFields) {
                this.closeModal();
            }
        },

        /**
         * Close modal
         */
        closeModal: function () {
            this._super();

            for (var field in this.requiredFields) {
                this.requiredFields[field].validation['required-entry'] = false;
            }

            this.requiredFields.urlField.validation['validate-url']             = false;
            this.downloadsLimitField.validation['validate-not-negative-number'] = false;
        }
    });
});
