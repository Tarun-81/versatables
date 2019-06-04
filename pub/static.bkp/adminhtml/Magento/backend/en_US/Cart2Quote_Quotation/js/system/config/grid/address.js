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

define(
    [
        'jquery',
        'jquery/ui',
        'uiComponent',
        'ko'
    ],
    function ($, ui, Component, ko) {
        'use strict';

        /**
         * This is the js model that is used for the grid in the Cart2Quote settings.
         */
        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/system/config/grid/address'
            },

            /**
             * Address data as array of JSON objects
             */
            addressFields: ko.observableArray(),

            /**
             * Complete parsed data
             */
            addressData: null,

            /**
             * Config field name
             */
            name: null,

            /**
             * Config field label
             */
            label: null,

            /**
             * Config field Html ID
             */
            htmlId: null,

            /**
             * Config field saved data
             */
            initFieldValue: null,

            /**
             * Make the element sortable
             *
             * @param e
             */
            makeSortable: function (e) {
                var self = this;
                $(e).sortable(
                    {
                        update: function (event) {
                            $('#' + self.id + ' .sort-order').each(function (index, element) {
                                $(element).val(index + 1);
                            });
                        }
                    }
                );
            },

            /**
             * Observable field container: mandatory for a observable array with observable vars
             *
             * @param label
             * @param name
             * @param required
             * @param additionalCss
             * @param enabled
             * @param locked
             * @param sortOrder
             */
            observableField: function (label, name, required, additionalCss, enabled, locked, sortOrder) {
                this.label = ko.observable(label);
                this.name = ko.observable(name);
                this.required = ko.observable(required);
                this.additionalCss = ko.observable(additionalCss);
                this.enabled = ko.observable(enabled);
                this.locked = ko.observable(locked);
                this.sortOrder = ko.observable(sortOrder);
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                var self = this;
                this.initFields(this.data);
                var initialData = this.initFieldValue;

                if (initialData == null || initialData.length > this.getDefaultValues().length) {
                    initialData = this.getDefaultValues();
                }

                initialData.sort(self.sortBySortOrder);

                this.addressFields()[this.id] = ko.observableArray(
                    ko.utils.arrayMap(initialData, function (field) {
                        return new self.observableField(
                            field.label,
                            field.name,
                            field.required,
                            field.additionalCss,
                            field.enabled,
                            field.locked,
                            field.sortOrder
                        );
                    })
                );

                return this;
            },

            /**
             * Get default field values
             *
             * @returns {*[]}
             */
            getDefaultValues: function () {
                return [
                    {
                        label: 'First Name',
                        name: 'firstname',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: true,
                        sortOrder: 10
                    },
                    {
                        label: 'Last Name',
                        name: 'lastname',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: true,
                        sortOrder: 20
                    },
                    {
                        label: 'Company',
                        name: 'company',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 30
                    },
                    {
                        label: 'Street Address',
                        name: 'street',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 40
                    },
                    {
                        label: 'City',
                        name: 'city',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 50
                    },
                    {
                        label: 'State/Province',
                        name: 'region_id',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 60
                    },
                    {
                        label: 'Zip/Postal Code',
                        name: 'postcode',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 70
                    },
                    {
                        label: 'Country',
                        name: 'country_id',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 80
                    },
                    {
                        label: 'Phone Number',
                        name: 'telephone',
                        required: true,
                        additionalCss: '',
                        enabled: true,
                        locked: false,
                        sortOrder: 90
                    }
                ];
            },

            /**
             * Initialize the fields
             *
             * @param data
             */
            initFields: function (data) {
                this.addressData = ko.utils.parseJson(data);
                this.name = this.addressData.field.name;
                this.label = this.addressData.field.label;
                this.htmlId = this.addressData.field.htmlId;
                this.initFieldValue = ko.utils.parseJson(this.addressData.field.fieldValue);
            },

            /**
             * Sort array
             *
             * @param value1
             * @param value2
             * @returns {number}
             */
            sortBySortOrder: function (value1, value2) {
                var sortOrder1 = parseInt(value1.sortOrder);
                var sortOrder2 = parseInt(value2.sortOrder);

                return ((sortOrder1 < sortOrder2) ? -1 : ((sortOrder1 > sortOrder2) ? 1 : 0));
            },

            /**
             * Get the address fields
             * @returns {*}
             */
            getAddressFields: function () {
                var self = this;
                return self.addressFields()[self.id];
            }
        });
    }
);
