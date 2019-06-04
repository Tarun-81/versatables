/*
 * Copyright © 2018 Aitoc. All rights reserved.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Aitoc_CheckoutFieldsManager/js/view/billing-address-mixin': true
            },
            'Magento_Checkout/js/view/payment/default': {
                'Aitoc_CheckoutFieldsManager/js/view/payment/default-mixin': true
            },
            'Magento_OfflinePayments/js/view/payment/method-renderer/purchaseorder-method': {
                'Aitoc_CheckoutFieldsManager/js/view/payment/purchaseorder-method-mixin': true
            },
            'Magento_Checkout/js/view/shipping': {
                'Aitoc_CheckoutFieldsManager/js/view/shipping-mixin': true
            },
            'Magento_Checkout/js/view/shipping-information/address-renderer/default': {
                'Aitoc_CheckoutFieldsManager/js/view/shipping-information/address-renderer/default-mixin': true
            },
            'Magento_Checkout/js/view/shipping-address/address-renderer/default': {
                'Aitoc_CheckoutFieldsManager/js/view/shipping-address/address-renderer/default-mixin': true
            },
            'mage/utils/misc': {
                'Aitoc_CheckoutFieldsManager/js/view/theme/mage/utils/misc-mixin': true
            },
            'Magento_Ui/js/form/element/select': {
                'Aitoc_CheckoutFieldsManager/js/form/element/select-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/view/summary/item/details': 'Aitoc_CheckoutFieldsManager/js/view/summary/item/details',
            'Magento_Checkout/template/billing-address/details.html': 'Aitoc_CheckoutFieldsManager/template/billing-address/details.html',
            'Magento_Checkout/template/shipping-address/address-renderer/default.html': 'Aitoc_CheckoutFieldsManager/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/shipping-information/address-renderer/default.html': 'Aitoc_CheckoutFieldsManager/template/shipping-information/address-renderer/default.html'
        }
    }
};
