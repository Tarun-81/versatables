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
        'jquery',
        'Magento_Ui/js/modal/modal',
        'mage/translate'
    ],
    function ($, modal, $t) {
        "use strict";
        //creating jquery widget
        $.widget('cart2quote.popup', $.mage.modal, {
            options: {
                elementId: null,
                title: $t('Sections'),
                buttons: [
                    {
                        text:
                            $t('Cancel'),
                        class: 'action-default scalable action-secondary',
                        click: function () {
                            this.closeModal();
                        }
                    },
                    {
                        text: $t('Save'),
                        class: 'action-default scalable action-primary',
                        click: function () {
                            var self = this;
                            $.ajax({
                                url: $('#section-form').attr('action'),
                                data: $('#section-form').serialize(),
                                type: 'post',
                                dataType: 'json',
                                showLoader: true,
                                /** @inheritdoc */
                                success: function (res) {
                                    location.reload();
                                    self.closeModal();
                                }
                            });
                        }
                    }
                ]
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
                this.dialog.modal('openModal');
            },
            /**
             * Prepare modal
             * @protected
             */
            _prepareDialog: function () {
                this.dialog = $(this.options.elementId).modal(this.options);
            }
        });

        return $.cart2quote.popup;
    }
);