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
    "jquery/ui"
], function ($) {
    'use strict';

    $.widget('quotation.tiercontrol', {
        options: {
            action: undefined,
            itemId: undefined,
            tierItemId: undefined,
            newRowCount: 0,
            increment: 5,
            highestQty: 1
        },

        _create: function () {
            var self = this;
            $(self.element).click(function (event) {
                switch (self.options.action) {
                    case 'addRow':
                        event.preventDefault();
                        self.addRow();
                        break;
                    case 'removeRow':
                        event.preventDefault();
                        self.removeRow();
                        break;
                    case 'selectRow':
                        self.selectRow();
                        break;
                    default:
                        break;
                }
            });
        },

        /**
         * Makes a copy of a quote tier row and shows the row.
         */
        addRow: function () {
            if (typeof(this.options.itemId) != 'undefined') {
                var emptyRow = $('#quote-item-tier-row-empty-' + this.options.itemId);
                var clonedRow = this.cloneRow(emptyRow);
                this.placeClonedRow(emptyRow, clonedRow);

                this.options.newRowCount++;

                this.updateRowSpan(1);
            }
        },

        /**
         * Clone the row from the empty row
         *
         * @param emptyRow
         * @return emptyRowClone
         */
        cloneRow: function (emptyRow) {
            var emptyRowClone = $(emptyRow.clone());
            emptyRowClone.prop(
                'id',
                'quote-item-tier-row-empty-' + this.options.itemId + '-' + this.options.newRowCount
            );
            emptyRowClone.show();
            emptyRowClone.html(emptyRowClone.html().replace(new RegExp("%template%", "g"), 'new'));
            emptyRowClone.html(emptyRowClone.html().replace(new RegExp("%increment%", "g"), this.options.newRowCount));
            emptyRowClone = this.incrementQty(emptyRowClone);

            return emptyRowClone;
        },

        /**
         * Place the cloned row
         *
         * @param emptyRow
         * @param emptyRowClone
         */
        placeClonedRow: function (emptyRow, emptyRowClone) {
            var previousRow = $('#quote-item-tier-row-empty-' +
                this.options.itemId + '-' + (this.options.newRowCount - 1)
            );

            if (previousRow.length > 0) {
                previousRow.after(emptyRowClone);
            } else {
                emptyRow.after(emptyRowClone);
            }
        },

        /**
         * Update the row span
         */
        updateRowSpan: function (add) {
            $('.selected-tier-row').find('[rowspan]').each(function () {
                var rowspanValue = (parseInt($(this).attr('rowspan')) + add);
                $(this).attr('rowspan', rowspanValue);
            });
        },

        /**
         * Increment the qty for the emptyRow
         *
         * @param row
         * @return row
         */
        incrementQty: function (row) {
            this.options.highestQty = this.options.highestQty + this.options.increment;
            row.find('.item-qty').val(this.options.highestQty);

            return row;
        },

        /**
         * Remove a row
         */
        removeRow: function () {
            $(this.element).closest('tr').remove();
            this.updateRowSpan(-1);
        },

        /**
         * Select a row
         */
        selectRow: function () {
            $(this.element).closest('tbody').find('.selected-tier-row').removeClass('selected-tier-row');
            $('#quote-item-tier-row-' + this.options.tierItemId).addClass('selected-tier-row');
        }
    });

    return $.quotation.tiercontrol;
});