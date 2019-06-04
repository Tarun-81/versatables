/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define(
    [
        'jquery',
        'underscore',
        'ko',
        'uiComponent',
        'uiRegistry',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Mageplaza_Osc/js/action/set-checkout-information',
        'Mageplaza_Osc/js/model/braintree-paypal'
    ],
    function (
        $,
        _,
        ko,
        Component,
        registry,
        quote,
        additionalValidators,
        setCheckoutInformationAction,
        braintreePaypalModel
    ) {
        "use strict";

        return Component.extend({
            defaults: {
                template: 'Mageplaza_Osc/container/review/place-order',
                visibleBraintreeButton: false
            },
            braintreePaypalModel: braintreePaypalModel,
            selectors: {
                default: '#co-payment-form .payment-method._active button.action.primary.checkout'
            },
            initialize: function () {
                this._super();
                var self = this;
                quote.paymentMethod.subscribe(function (value) {
                    self.checkVisiblePlaceOrderButton();
                });

                registry.async(this.getPaymentPath('braintree_paypal'))
                (this.asyncBraintreePaypal.bind(this));

                return this;
            },
            /**
             * Set list of observable attributes
             * @returns {exports.initObservable}
             */
            initObservable: function () {
                var self = this;

                this._super()
                    .observe(['visibleBraintreeButton']);

                return this;
            },
            asyncBraintreePaypal: function () {
                this.checkVisiblePlaceOrderButton();
            },
            checkVisiblePlaceOrderButton: function () {
                this.visibleBraintreeButton(this.getBraintreePaypalComponent() && this.isPaymentBraintreePaypal());
            },
            placeOrder: function () {
                var self = this;
                if (additionalValidators.validate()) {
                    this.preparePlaceOrder().done(function () {
                        self._placeOrder();
                    });
                }

                return this;
            },

            brainTreePaypalPlaceOrder: function () {
                var component = this.getBraintreePaypalComponent();
                var _arguments = arguments;
                if(component && additionalValidators.validate()) {
                    this.preparePlaceOrder().done(function () {
                        component.placeOrder.apply(component, _arguments);
                    });
                }

                return this;
            },

            brainTreePayWithPayPal: function () {
                var component = this.getBraintreePaypalComponent();
                var _arguments = arguments;
                if(component && additionalValidators.validate()) {
                    if(component.isSkipOrderReview()) {
                        this.preparePlaceOrder().done(function () {
                            component.payWithPayPal.apply(component, _arguments);
                        });
                    } else {
                        component.payWithPayPal.apply(component, _arguments);
                    }
                }
            },
            preparePlaceOrder: function (scrollTop) {
                var scrollTop = scrollTop !== undefined ? scrollTop : true;
                var deferer = $.when(setCheckoutInformationAction());

                return scrollTop ? deferer.done(function () {
                    $("body").animate({ scrollTop: 0 }, "slow");
                }): deferer;
            },

            getPaymentPath: function(paymentMethodCode) {
                return 'checkout.steps.billing-step.payment.payments-list.' + paymentMethodCode;
            },

            getPaymentMethodComponent: function (paymentMethodCode) {
                return registry.get(this.getPaymentPath(paymentMethodCode));
            },


            isPaymentBraintreePaypal: function () {
                return quote.paymentMethod() && quote.paymentMethod().method === 'braintree_paypal';
            },

            getBraintreePaypalComponent: function () {
                return this.getPaymentMethodComponent('braintree_paypal');
            },

            _placeOrder: function () {
                $(this.selectors.default).trigger('click');
            },

            isPlaceOrderActionAllowed: function () {
                return true;
            }
        });
    }
);
