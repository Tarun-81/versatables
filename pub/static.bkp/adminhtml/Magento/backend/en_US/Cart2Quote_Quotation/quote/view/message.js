/*
 *  CART2QUOTE CONFIDENTIAL
 *  __________________
 *  [2009] - [2018] Cart2Quote B.V.
 *  All Rights Reserved.
 *  NOTICE OF LICENSE
 *  All information contained herein is, and remains
 *  the property of Cart2Quote B.V. and its suppliers,
 *  if any.  The intellectual and technical concepts contained
 *  herein are proprietary to Cart2Quote B.V.
 *  and its suppliers and may be covered by European and Foreign Patents,
 *  patents in process, and are protected by trade secret or copyright law.
 *  Dissemination of this information or reproduction of this material
 *  is strictly forbidden unless prior written permission is obtained
 *  from Cart2Quote B.V.
 * @category    Cart2Quote
 * @package     Quotation
 * @copyright   Copyright (c) 2018. Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */
define([
    "jquery",
    "jquery/ui",
    'Magento_Ui/js/modal/modal',
    "mage/translate"
], function ($) {
    "use strict";
    $.widget('mage.orderEditDialog', {
        options: {
            url: null,
            message: null,
            modal: null
        },

        /**
         * @protected
         */
        _create: function () {
            this._prepareDialog();
        },

        /**
         * Show modal
         */
        showDialog: function () {
            this.options.dialog.html(this.options.message).modal('openModal');
        },

        /**
         * Redirect to edit page
         */
        redirect: function () {
            window.location = this.options.url;
        },

        /**
         * Prepare modal
         * @protected
         */
        _prepareDialog: function () {
            var self = this;

            this.options.dialog = $('<div class="ui-dialog-content ui-widget-content"></div>').modal({
                type: 'popup',
                modalClass: 'edit-order-popup',
                title: $.mage.__('Edit Order'),
                buttons: [{
                    text: $.mage.__('Ok'),
                    'class': 'action-primary',
                    click: function () {
                        self.redirect();
                    }
                }]
            });
        }
    });

    return $.mage.orderEditDialog;
});
