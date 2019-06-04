/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/* global AdminOrder */
/*global define*/
define([
    'jquery',
    'Lof_RequestForQuote/quote/scripts'
], function (jQuery) {
    'use strict';

    var $el,
        config,
        baseUrl,
        order,
        payment;

    if (jQuery('#quote-data').length ) {
        $el = jQuery('#quote-data');

    } else {
        $el = jQuery('#edit_form');
    }

    if (!$el.length || !$el.data('order-config')) {
        return;
    }

    config = $el.data('order-config');
    baseUrl = $el.data('load-base-url');

    order = new AdminOrder(config);
    order.setLoadBaseUrl(baseUrl);

    payment = {
        switchMethod: order.switchPaymentMethod.bind(order)
    };

    window.order = order;
    window.payment = payment;
});
