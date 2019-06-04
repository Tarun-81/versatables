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
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Osc
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'ko',
    'Magento_Checkout/js/view/form/element/email',
    'Magento_Customer/js/model/customer',
    'Mageplaza_Osc/js/model/osc-data',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Customer/js/action/check-email-availability',
    'mage/url',
    'rjsResolver',
    'mage/validation'
], function ($, ko, Component, customer, oscData, additionalValidators, checkEmailAvailability,urlBuilder, resolver) {
    'use strict';

    var cacheKey = 'form_register_chechbox',
        allowGuestCheckout = window.checkoutConfig.oscConfig.allowGuestCheckout,
        passwordMinLength = window.checkoutConfig.oscConfig.register.dataPasswordMinLength,
        passwordMinCharacter = window.checkoutConfig.oscConfig.register.dataPasswordMinCharacterSets,
        customerEmailElement = '.form-login #customer-email';

    if (!customer.isLoggedIn() && !allowGuestCheckout) {
        oscData.setData(cacheKey, true);
    }

    return Component.extend({
        defaults: {
            template: 'Mageplaza_Osc/container/form/element/email',
            isLoginVisible: false,
            listens: {
                email: ''
            }
        },
        checkDelay: 0,
        dataPasswordMinLength: passwordMinLength,
        dataPasswordMinCharacterSets: passwordMinCharacter,

        initialize: function () {
            this._super();

            if (!!this.email()) {
                resolver(this.emailHasChanged.bind(this));
            }

            additionalValidators.registerValidator(this);
        },

        initObservable: function () {
            this._super()
                .observe({
                    isCheckboxRegisterVisible: allowGuestCheckout,
                    isRegisterVisible: oscData.getData(cacheKey)
                });

            this.isRegisterVisible.subscribe(function (newValue) {
                oscData.setData(cacheKey, newValue);
            });

            return this;
        },

        triggerLogin: function () {
            if($('.osc-authentication-wrapper a.action-auth-toggle').hasClass('osc-authentication-toggle')){
                $('.osc-authentication-toggle').trigger('click');
            }else{
                window.location.href = urlBuilder.build("customer/account/login");
            }
        },


        validateEmail: function (focused) {
            var loginFormSelector = 'form[data-role=email-with-possible-login]',
                usernameSelector = loginFormSelector + ' input[name=username]',
                loginForm = $(loginFormSelector),
                validator;

            loginForm.validation();

            if (focused === false) {
                return !!$(usernameSelector).valid();
            }

            validator = loginForm.validate();

            return validator.check(usernameSelector);
        },

        validate: function (type) {

            if (customer.isLoggedIn() || !this.isRegisterVisible() || this.isPasswordVisible()) {
                oscData.setData('register', false);
                return true;
            }

            if (typeof type !== 'undefined') {
                var selector = $('#osc-' + type);

                selector.parents('form').validation();

                return !!selector.valid();
            }

            var passwordSelector = $('#osc-password');
            passwordSelector.parents('form').validation();

            var password = !!passwordSelector.valid();
            var confirm = !!$('#osc-password-confirmation').valid();

            var result = password && confirm;
            if (result) {
                oscData.setData('register', true);
                oscData.setData('password', passwordSelector.val());
            }

            return result;

        },

        /** Move label element when input has value */
        hasValue: function(){
            if (window.checkoutConfig.oscConfig.isUsedMaterialDesign) {
                $(customerEmailElement).val() ? $(customerEmailElement).addClass('active') : $(customerEmailElement).removeClass('active');
            }
        }
    });
});
