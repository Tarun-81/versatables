/**
 * Copyright © 2018 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'uiRegistry',
    'Magento_Ui/js/form/element/select'
], function (uiRegistry, select) {
    'use strict';

    return select.extend({

        /**
         * On value change handler.
         *
         * @param {String} value
         */
        onUpdate: function (value) {

            var multiFileField = uiRegistry.get('index = ' + this.indexies.multi_file_field);
            if (multiFileField.visibleValue == value) {
                multiFileField.show();
            } else {
                multiFileField.hide();
            }

            var urlField = uiRegistry.get('index = ' + this.indexies.url_field);
            if (urlField.visibleValue == value) {
                urlField.show();
            } else {
                urlField.hide();
            }

            return this._super();
        }
    });
});
