define([
    'jquery',
    'Mageplaza_Osc/js/action/set-checkout-information',
    'Mageplaza_Osc/js/model/braintree-paypal',
    'Magento_Checkout/js/model/payment/additional-validators',
], function ($, setCheckoutInformationAction, braintreePaypalModel, additionalValidators) {
    'use strict';
        return function (BraintreePaypalComponent) {
            return BraintreePaypalComponent.extend({
                /**
                 * Set list of observable attributes
                 * @returns {exports.initObservable}
                 */
                initObservable: function () {
                    var self = this;

                    this._super();
                    // for each component initialization need update property
                    this.isReviewRequired = braintreePaypalModel.isReviewRequired;
                    this.customerEmail = braintreePaypalModel.customerEmail;
                    this.active = braintreePaypalModel.active;

                    return this;
                }
            })
        }
});