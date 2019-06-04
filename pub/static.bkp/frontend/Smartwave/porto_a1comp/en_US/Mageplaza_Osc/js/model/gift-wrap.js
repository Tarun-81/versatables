/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(['ko'], function(ko) {
    'use strict';
    var hasWrap = ko.observable(window.checkoutConfig.oscConfig.giftWrap.hasWrap);
    return {
        hasWrap: hasWrap,
        getIsWrap: function() {
            return this.hasWrap();
        },
        setIsWrap: function(isWrap) {
            return this.hasWrap(isWrap); 
        }
    };
});