/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */
define([
    'Magento_Checkout/js/view/minicart',
    'jquery'
], function (Component, $) {
'use strict';

return Component.extend({
    initialize: function () {
        this._super();
        this.disable_cart = window.checkout.additional_cfm.disable_cart;
        if (this.disable_cart == 1) {
          $("[data-block='minicart']").on('dropdowndialogopen', function () {
              $('.block-minicart').find('.viewcart').remove();
            });
        }
      }
  });
});
