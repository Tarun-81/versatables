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
         * This is the js model that is used for the grid
         */
        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/quote/section/grid'
            },

            /**
             * section data as array of JSON objects
             */
            sections: ko.observableArray(),

            /**
             * Complete parsed data
             */
            sectionsData: null,
            /**
             * Initial data used to reset
             */
            initialData: null,
            /**
             * Quote id
             */
            quoteId: null,
            actionUrl: null,
            /**
             * Make the element sortable
             *
             * @param e
             */
            makeSortable: function (e) {
                $(e).sortable({
                    update: this.sort,
                    opacity: 0.7,
                    placeholder: {
                        element: function(currentItem) {
                            return $("<tr class=\'placeholder\'><td colspan=\'3\'></td></tr>")[0];
                        },
                        update: function(container, p) {
                            return;
                        }
                    }
                });
            },
            sort: function () {
                $('#sortable-table').find('tr:not(.deleted) .sort-order').each(function (index, element) {
                    ko.dataFor(element).sortOrder(index + 1);
                });
            },
            /**
             * Observable field container: mandatory for a observable array with observable vars
             *
             * @param field
             */
            observableField: function (field) {
                this.label = ko.observable(field.label);
                this.quoteId = ko.observable(field.quote_id);
                this.sectionId = ko.observable(field.section_id);
                this.sortOrder = ko.observable(field.sort_order);
                this.isDeleted = ko.observable(false);
            },

            /**
             * @return {exports.initObservable}
             */
            initObservable: function () {
                this.initFields(this.data);
                var self = this;
                if (this.data.sections) {
                    this.data.sections.sort(this.sortBySortOrder);
                    this.initialData = ko.observableArray(
                        ko.utils.arrayMap(this.data.sections, function (field) {
                            return new self.observableField(field);
                        })
                    );
                    this.sections(this.initialData);
                } else {
                    this.sections(ko.observableArray([]));
                }
                this.sections().subscribe(this.sort.bind(this));
                return this;
            },

            /**
             * Initialize the fields
             *
             * @param data
             */
            initFields: function (data) {
                this.quoteId = data.quote_id;
                this.actionUrl = data.action_url;
            },

            /**
             * Sort array
             *
             * @param value1
             * @param value2
             * @returns {number}
             */
            sortBySortOrder: function (value1, value2) {
                var sortOrder1 = parseInt(value1.sort_order);
                var sortOrder2 = parseInt(value2.sort_order);

                return ((sortOrder1 < sortOrder2) ? -1 : ((sortOrder1 > sortOrder2) ? 1 : 0));
            },
            /**
             * Get the sections
             * @returns {*}
             */
            getSections: function () {
                return this.sections();
            },
            /**
             * Add a sections row to the grid
             */
            addSection: function () {
                this.getSections().push(new this.observableField({
                    quote_id: this.quoteId
                }));
                this.sort();
            },
            /**
             * Remove a sections row from the grid
             */
            deleteSection: function (section) {
                if (!section.sectionId()) {
                    this.getSections().remove(section);
                } else {
                    section.isDeleted(true);
                }
                this.sort();
            }
        });
    }
);
