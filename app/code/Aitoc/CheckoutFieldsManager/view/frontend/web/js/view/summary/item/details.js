/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'uiComponent',
        'Magento_Customer/js/customer-data',
        'Magento_Checkout/js/model/totals',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/step-navigator'
    ],
    function ($, Component, customerData, totals, quote, stepNavigator) {
    'use strict';
    return Component.extend({
    defaults: {
        template: 'Aitoc_CheckoutFieldsManager/summary/item/details'
      },

    /**
     *
     * @param quoteItem
     * @returns {*}
     */
    getValue: function (quoteItem) {
        return quoteItem.name;
      },

    /**
     *  params system config read_cart_in_checkout
     * @returns {*}
     */
    readCart: function () {
        return window.checkoutConfig.additional_cfm.read_cart_in_checkout;
      },

    /**
     * get Config url
     *
     * @param quoteItem
     * @returns {string}
     */
    getConfigUrl: function (quoteItem) {
        var url = '';
        var item_id = quoteItem.item_id;
        var cart = customerData.get('cart');
        _.each(cart().items, function (value) {
            if (value.item_id == item_id) {
              url = value.configure_url;
            }
          });

        return url;
      },

    /**
     * event for input
     *
     * @param id
     */
    onKeyUp: function (id) {
        var item = $('#' + id);
        if (this._isValid(item.data('item-qty'), item.val())) {
          $('button#update-cart-item-' + item.data('cart-item')).show('fade', 300);
        } else {
          $('button#update-cart-item-' + item.data('cart-item')).hide('fade', 300);
        }
      },

    /**
     * event for button
     *
     * @param elem
     */
    updateItem: function (data, event) {
        var element = $(event.currentTarget).attr('id');
        var itemId = $('#cart-item-' + $(event.currentTarget).data('cart-item') + '-qty');
        this._ajax(window.checkoutConfig.additional_cfm.updateItemQtyUrl, {
            item_id: $(itemId).data('cart-item'),
            item_qty: $(itemId).val()
          }, $(event.currentTarget), itemId);
        this.navigateTo(stepNavigator);
      },

    /**
     * remove item
     *
     * @param data
     * @param event
     */
    removeItem: function (data, event) {
        var element = $(event.currentTarget).attr('id');
        var itemId = $('#cart-item-' + $(event.currentTarget).data('cart-item') + '-qty');
        this._ajax(window.checkoutConfig.additional_cfm.removeItemUrl, {
            item_id: $(itemId).data('cart-item'),
            form_key: window.checkoutConfig.formKey
          }, $(event.currentTarget), itemId);
        this.navigateTo(stepNavigator);
      },

    /**
     * Check valid for Qty
     *
     * @param origin
     * @param changed
     * @returns {boolean}
     * @private
     */
    _isValid: function (origin, changed) {
        return (origin != changed) &&
            (changed.length > 0) &&
            (changed - 0 == changed) &&
            (changed - 0 > 0);
      },

    /**
     * Ajax
     *
     * @param url - ajax url
     * @param data - post data for ajax call
     * @param elem - element that initiated the event
     * @param callback - callback method to execute after AJAX success
     */
    _ajax: function (url, data, elem, item) {
        $.ajax({
            url: url,
            data: data,
            async: false,
            type: 'post',
            dataType: 'json',
            context: this,
            beforeSend: function () {
                elem.attr('disabled', 'disabled');
              },
            complete: function () {
                elem.attr('disabled', null);
              }
          })
        .done(function (response) {
            if (response.success) {
              elem.hide('fade', 300);
              item.attr('data-item-qty', item.val());
            } else {
              var msg = response.error_message;
              if (msg) {
                alert({
                    content: $.mage.__(msg)
                  });
              }
            }
          })
        .fail(function (error) {
            console.log(JSON.stringify(error));
          });
      },

    /**
     * Navigate to current step
     *
     * navigate step
     * @param step
     */
    navigateTo: function (step) {
        var sortedItems = step.steps.sort(this.sortItems);
        step.steps.sort(stepNavigator.sortItems).forEach(function (element, index) {
            if (element.isVisible()) {
              element.isVisible(false);
              //  window.location = window.checkoutConfig.checkoutUrl + "#" + stepNavigator.steps()[index].code;
              window.location.reload();
              element.isVisible(true);
            }
          });
      },
    });
    }
);
