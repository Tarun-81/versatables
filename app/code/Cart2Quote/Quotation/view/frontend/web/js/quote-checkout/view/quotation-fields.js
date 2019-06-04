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
        'ko',
        'Magento_Ui/js/form/form',
        'Cart2Quote_Quotation/js/quote-checkout/checkout-data-quotation',
        'Cart2Quote_Quotation/js/quote-checkout/model/email-form-usage-observer',
        'mage/translate',
        'uiRegistry',
        'Magento_Customer/js/model/customer'
    ],
    function ($,
              ko,
              Component,
              checkoutQuotationData,
              emailFormUsageObserver,
              $t,
              registry,
              customer) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Cart2Quote_Quotation/quote-checkout/view/fields'
            },

            formSelector: '#quotation-fields',
            allowToUseForm: emailFormUsageObserver.showFields,
            showGuestField: emailFormUsageObserver.showGuestField,
            isCustomerLoggedIn: customer.isLoggedIn,
            loginEnabled: emailFormUsageObserver.allowToUseForm(),

            showQuotationFields: null,

            /**
             * Init component
             */
            initialize: function () {
                this._super();

                this.initShowQuotationButton();

                this.allowToUseForm.extend({notify: 'always'});

                emailFormUsageObserver.updateFields();

                registry.async('checkoutProvider')(function (checkoutProvider) {
                    var quotationFieldsData = checkoutQuotationData.getQuotationFieldsFromData();

                    if (quotationFieldsData) {
                        checkoutProvider.set(
                            'quotationFieldData',
                            $.extend({}, checkoutProvider.get('quotationFieldData'), quotationFieldsData)
                        );
                    }
                    checkoutProvider.on('quotationFieldData', function (quotationFieldData) {
                        checkoutQuotationData.setQuotationFieldsFromData(quotationFieldData);
                    });
                });
            },

            /**
             * Validate the fields
             * @return boolean
             */
            validateFields: function () {
                var emailValidationResult = false,
                    loginFormSelector = 'form[data-role=email-with-possible-login]',
                    firstNameSelector = '[name="quotationFieldData.firstname"]',
                    lastNameSelector = '[name="quotationFieldData.lastname"]';

                this.source.set('params.invalid', false);

                if (customer.isLoggedIn()) {
                    emailValidationResult = true;
                    this.triggerValidateFieldSet('quotationFieldData');
                } else {
                    $(loginFormSelector).validation();
                    emailValidationResult = Boolean($(loginFormSelector + ' input[name=username]').valid());
                }

                if (!this.showGuestField() || customer.isLoggedIn()) {
                    $(firstNameSelector).removeClass('_required');
                    $(lastNameSelector).removeClass('_required');
                } else {
                    $(firstNameSelector).addClass('_required');
                    $(lastNameSelector).addClass('_required');
                    this.triggerValidateFieldSet('guestFieldData');
                }

                this.triggerValidateFieldSet('custom_attributes');

                return !(this.source.get('params.invalid')) && emailValidationResult;
            },

            /**
             * Trigger field validation for a fieldset
             *
             * @param fieldSet
             */
            triggerValidateFieldSet: function (fieldSet) {
                this.source.trigger(fieldSet + '.data.validate');
                if (typeof this.source.get('.' + fieldSet) !== 'undefined') {
                    this.source.trigger('.' + fieldSet + '.data.validate');
                }
            },

            /**
             * Init the login button
             */
            initShowQuotationButton: function () {
                var self = this;

                self.showQuotationFields = ko.computed(function () {
                    return self.allowToUseForm() || (self.isCustomerLoggedIn() && !self.allowToUseForm())
                });
            }
        });
    }
);
