define(
    [
        'ko',
        'Magento_Checkout/js/view/payment/default',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Cryozonic_StripePayments/js/action/get-payment-url',
        'mage/translate',
        'mage/url',
        'jquery',
        'Magento_Checkout/js/action/place-order',
        'Magento_Checkout/js/model/payment/additional-validators',
        'Magento_Checkout/js/action/redirect-on-success',
        'mage/storage',
        'Magento_Checkout/js/model/url-builder'
    ],
    function (
        ko,
        Component,
        globalMessageList,
        quote,
        customer,
        getPaymentUrlAction,
        $t,
        url,
        $,
        placeOrderAction,
        additionalValidators,
        redirectOnSuccessAction,
        storage,
        urlBuilder
    ) {
        'use strict';

        return Component.extend({
            externalRedirectUrl: null,
            defaults: {
                template: 'Cryozonic_StripePayments/payment/form',
                cryozonicStripeCardSave: true,
                cryozonicStripeShowApplePaySection: false,
                cryozonicApplePayToken: null
            },

            initObservable: function ()
            {
                this._super()
                    .observe([
                        'cryozonicStripeError',
                        'cryozonicStripeCardName',
                        'cryozonicStripeCardNumber',
                        'cryozonicStripeCardExpMonth',
                        'cryozonicStripeCardExpYear',
                        'cryozonicStripeCardVerificationNumber',
                        'cryozonicStripeJsToken',
                        'cryozonicApplePayToken',
                        'cryozonicStripeCardSave',
                        'cryozonicStripeSelectedCard',
                        'cryozonicStripeShowNewCardSection',
                        'cryozonicStripeShowApplePaySection',
                        'cryozonicCreatingToken',
                        'isPaymentRequestAPISupported'
                    ]);

                this.cryozonicStripeSelectedCard.subscribe(this.onSelectedCardChanged, this);
                this.cryozonicStripeSelectedCard('new_card');
                if (!this.hasSavedCards())
                    this.cryozonicStripeShowNewCardSection(true);

                this.showSavedCardsSection = ko.computed(function()
                {
                    return this.hasSavedCards() && this.isBillingAddressSet() && !this.cryozonicApplePayToken();
                }, this);

                this.displayAtThisLocation = ko.computed(function()
                {
                    return this.config().applePayLocation == 1;
                }, this);

                this.showNewCardSection = ko.computed(function()
                {
                    return this.cryozonicStripeShowNewCardSection() &&
                        this.isBillingAddressSet() &&
                        (!this.displayAtThisLocation() || !this.cryozonicApplePayToken());
                }, this);

                this.showSaveCardOption = ko.computed(function()
                {
                    return this.config().showSaveCardOption && customer.isLoggedIn() && (this.showNewCardSection() || this.cryozonicApplePayToken());
                }, this);

                this.securityMethod = this.config().securityMethod;

                var self = this;
                window.stripePaymentForm = this;

                if (typeof onPaymentSupportedCallbacks == 'undefined')
                    window.onPaymentSupportedCallbacks = [];

                onPaymentSupportedCallbacks.push(function()
                {
                    self.isPaymentRequestAPISupported(true);
                    self.cryozonicStripeShowApplePaySection(true);
                });

                if (typeof onTokenCreatedCallbacks == 'undefined')
                    window.onTokenCreatedCallbacks = [];

                onTokenCreatedCallbacks.push(function(token)
                {
                    self.cryozonicStripeJsToken(token.id + ':' + token.card.brand + ':' + token.card.last4);
                    self.setApplePayToken(token);
                });

                quote.billingAddress.subscribe(function (address)
                {
                    cryozonic.paramsApplePay = this.getApplePayParams();
                    cryozonic.setAVSFieldsFrom(address);

                    if (cryozonic.stripeJsV3)
                        cryozonic.initPaymentRequestButton();
                }
                , this);

                cryozonic.paymentIntent = this.config().paymentIntent;

                return this;
            },

            hasSavedCards: function()
            {
                return (typeof this.config().savedCards != 'undefined'
                    && this.config().savedCards != null
                    && this.config().savedCards.length);
            },

            onSelectedCardChanged: function(newValue)
            {
                if (newValue == 'new_card')
                    this.cryozonicStripeShowNewCardSection(true);
                else
                    this.cryozonicStripeShowNewCardSection(false);
            },

            onCheckoutFormRendered: function()
            {
                var self = stripePaymentForm;
                if (self.config().securityMethod > 0)
                    initStripe(self.config().stripeJsKey, self.config().securityMethod);
            },

            isBillingAddressSet: function()
            {
                return quote.billingAddress() && quote.billingAddress().canUseForBilling();
            },

            onStripeInit: function(err)
            {
                if (err)
                {
                    this.cryozonicStripeError(err);
                    return this.showError(this.maskError(err));
                }
                else
                    this.cryozonicStripeError(null);
            },

            isPlaceOrderEnabled: function()
            {
                if (this.cryozonicStripeError())
                    return false;

                if (this.cryozonicCreatingToken())
                    return false;

                if (this.isBillingAddressSet())
                    cryozonic.setAVSFieldsFrom(quote.billingAddress());

                return this.isBillingAddressSet();
            },

            isZeroDecimal: function(currency)
            {
                var currencies = ['bif', 'djf', 'jpy', 'krw', 'pyg', 'vnd', 'xaf',
                    'xpf', 'clp', 'gnf', 'kmf', 'mga', 'rwf', 'vuv', 'xof'];

                return currencies.indexOf(currency) >= 0;
            },

            isApplePayEnabled: function()
            {
                return this.config().isApplePayEnabled;
            },

            getApplePayParams: function()
            {
                if (!this.isApplePayEnabled())
                    return null;

                if (!this.isBillingAddressSet())
                    return null;

                var amount, currency;
                if (this.config().useStoreCurrency)
                {
                    currency = quote.totals().quote_currency_code;
                    amount = quote.totals().grand_total + quote.totals().tax_amount;
                }
                else
                {
                    currency = quote.totals().base_currency_code;
                    amount = quote.totals().base_grand_total;
                }

                currency = currency.toLowerCase();

                var cents = 100;
                if (this.isZeroDecimal(currency))
                    cents = 1;

                amount = Math.round(amount * cents);

                var description = quote.billingAddress().firstname + " " + quote.billingAddress().lastname;

                if (typeof customer.customerData.email != 'undefined')
                    description += " <" + customer.customerData.email + ">";

                return {
                    "country": quote.billingAddress().countryId,
                    "currency": currency,
                    "total": {
                        "label": description,
                        "amount": amount
                    }
                };
            },

            beginApplePay: function()
            {
                var self = this;
                var paymentRequest = this.getApplePayParams();
                var session = Stripe.applePay.buildSession(paymentRequest, function(result, completion)
                {
                    self.setApplePayToken(result.token);
                    self.cryozonicStripeJsToken(result.token.id + ':' + result.token.card.brand + ':' + result.token.card.last4);
                    completion(ApplePaySession.STATUS_SUCCESS);
                },
                function(error)
                {
                    alert(error.message);
                });

                session.begin();
            },

            setApplePayToken: function(token)
            {
                if (!this.isApplePayEnabled())
                    return;

                this.cryozonicApplePayToken(token);
                this.cryozonicStripeShowApplePaySection(false);
            },

            resetApplePay: function()
            {
                if (!this.isApplePayEnabled())
                    return;

                this.cryozonicApplePayToken(null);
                this.cryozonicStripeShowApplePaySection(true);
                this.cryozonicStripeJsToken(null);
            },

            showApplePaySection: function()
            {
                return (this.cryozonicStripeShowApplePaySection || this.isPaymentRequestAPISupported);
            },

            showApplePayButton: function()
            {
                return !this.isPaymentRequestAPISupported;
            },

            config: function()
            {
                return window.checkoutConfig.payment[this.getCode()];
            },

            isActive: function(parents)
            {
                return true;
            },

            isNewCard: function()
            {
                if (!this.hasSavedCards()) return true;
                if (this.cryozonicStripeSelectedCard() == 'new_card') return true;
                return false;
            },

            maskError: function(err)
            {
                return cryozonic.maskError(err);
            },

            stripeJsPlaceOrder: function()
            {
                cryozonic.applePaySuccess = false;
                if (this.config().securityMethod > 0)
                {
                    var self = this;

                    this.cryozonicStripeJsToken(null);
                    this.cryozonicCreatingToken(true);
                    cryozonic.setAVSFieldsFrom(quote, customer);

                    // Use the Apple Pay token as the source
                    if (this.cryozonicApplePayToken())
                    {
                        cryozonic.applePaySuccess = true;
                        cryozonic.sourceId = this.cryozonicApplePayToken().id;
                    }
                    // Create a new source
                    else if (this.cryozonicStripeSelectedCard() == 'new_card')
                        cryozonic.sourceId = null;
                    // Use one of the selected saved cards
                    else
                        cryozonic.sourceId = cryozonic.cleanToken(this.cryozonicStripeSelectedCard());

                    createStripeToken(function(err, token, response)
                    {
                        self.cryozonicCreatingToken(false);
                        if (err)
                        {
                            self.showError(self.maskError(err));
                            self.resetApplePay();
                            return;
                        }
                        else
                        {
                            self.cryozonicStripeJsToken(token);
                            self.placeOrder();
                        }
                    });
                }
                else
                    this.placeOrder(); // Stripe.js is disabled

            },

            /**
             * Place order.
             */
            placeOrder: function (data, event) {
                var self = this;

                if (event) {
                    event.preventDefault();
                }

                var customErrorHandler = this.handlePlaceOrderErrors.bind(this);

                if (this.validate() && additionalValidators.validate()) {
                    this.isPlaceOrderActionAllowed(false);

                    this.getPlaceOrderDeferredObject()
                        .fail(customErrorHandler)
                        .done(
                            function () {
                                self.afterPlaceOrder();

                                if (self.redirectAfterPlaceOrder) {
                                    redirectOnSuccessAction.execute();
                                }
                            }
                        );

                    return true;
                }

                return false;
            },

            /**
             * @return {*}
             */
            getPlaceOrderDeferredObject: function () {
                return $.when(
                    placeOrderAction(this.getData(), this.messageContainer)
                );
            },

            handlePlaceOrderErrors: function (result)
            {
                var self = this;
                var status = result.status + " " + result.statusText;

                self.resetPaymentIntent(status, result.responseText, function(err, response)
                {
                    if (err)
                    {
                        self.showError(err);
                    }
                    else
                    {
                        cryozonic.paymentIntent = response.paymentIntent;
                        self.isPlaceOrderActionAllowed(true);
                    }
                });
            },

            resetPaymentIntent: function (status, response, callback) {
                var serviceUrl = urlBuilder.createUrl('/cryozonic/stripepayments/reset_payment_intent', {});

                return storage.post(
                    serviceUrl,
                    JSON.stringify({ status: status, response: response }),
                    false
                )
                .fail(function (xhr, textStatus, errorThrown)
                {
                    var response = JSON.parse(xhr.responseText);
                    callback(response.message, response);
                })
                .done(function (response)
                {
                    if (typeof response === 'string') {
                        response = JSON.parse(response);
                    }

                    callback(null, response);
                });
            },

            showError: function(message)
            {
                if (this.cryozonicApplePayToken() && this.config().applePayLocation == 2)
                {
                    document.getElementById('checkout').scrollIntoView(true);
                    globalMessageList.addErrorMessage({ "message": message });
                }
                else
                {
                    document.getElementById('actions-toolbar').scrollIntoView(true);
                    this.messageContainer.addErrorMessage({ "message": message });
                }
            },

            // afterPlaceOrder: function()
            // {
            //     if (this.redirectAfterPlaceOrder)
            //         return;
            // },

            validate: function(elm)
            {
                if (this.cryozonicApplePayToken)
                    return true;

                // @todo Replace these with proper form validation
                var data = this.getData().additional_data;

                if (this.isNewCard())
                {
                    if (this.config().securityMethod > 0)
                    {
                        if (!this.cryozonicStripeJsToken())
                            return this.showError('Could not process card details, please try again.');
                    }
                    else
                    {
                        if (!data.cc_owner) return this.showError('Please enter the cardholder name');
                        if (!data.cc_number) return this.showError('Please enter your card number');
                        if (!data.cc_exp_month) return this.showError('Please enter your card\'s expiration month');
                        if (!data.cc_exp_year) return this.showError('Please enter your card\'s expiration year');
                        if (!data.cc_cid) return this.showError('Please enter your card\'s security code (CVN)');
                    }
                }
                else if (!this.cryozonicStripeSelectedCard() || this.cryozonicStripeSelectedCard().indexOf('card_') !== 0)
                    return this.showError('Please select a card!');

                return true;
            },

            getCode: function()
            {
                return 'cryozonic_stripe';
            },

            shouldSaveCard: function()
            {
                return ((this.showSaveCardOption() && this.cryozonicStripeCardSave()) || this.config().alwaysSaveCard);
            },

            getData: function()
            {
                var data = {
                    'method': this.item.method
                };

                if (this.config().securityMethod == 0 && this.cryozonicStripeSelectedCard() && this.cryozonicStripeSelectedCard() != 'new_card')
                {
                    data.additional_data = {
                        'cc_saved': this.cryozonicStripeSelectedCard()
                    };
                }
                else if (this.config().securityMethod >= 1)
                {
                    data.additional_data = {
                        'cc_stripejs_token': this.cryozonicStripeJsToken(),
                        'cc_save': this.shouldSaveCard()
                    };
                }
                else
                {
                    data.additional_data = {
                        'cc_owner': this.cryozonicStripeCardName(),
                        'cc_number': this.cryozonicStripeCardNumber(),
                        'cc_exp_month': this.cryozonicStripeCardExpMonth(),
                        'cc_exp_year': this.cryozonicStripeCardExpYear(),
                        'cc_cid': this.cryozonicStripeCardVerificationNumber(),
                        'cc_save': this.shouldSaveCard()
                    };
                }

                return data;
            },

            getCcMonthsValues: function() {
                return $.map(this.getCcMonths(), function(value, key) {
                    return {
                        'value': key,
                        'month': value
                    };
                });
            },

            getCcYearsValues: function() {
                return $.map(this.getCcYears(), function(value, key) {
                    return {
                        'value': key,
                        'year': value
                    };
                });
            },

            getCcMonths: function()
            {
                return window.checkoutConfig.payment[this.getCode()].months;
            },

            getCcYears: function()
            {
                return window.checkoutConfig.payment[this.getCode()].years;
            },

            getCvvImageUrl: function() {
                return window.checkoutConfig.payment[this.getCode()].cvvImageUrl;
            },

            getCvvImageHtml: function() {
                return '<img src="' + this.getCvvImageUrl() +
                    '" alt="' + 'Card Verification Number Visual Reference' +
                    '" title="' + 'Card Verification Number Visual Reference' +
                    '" />';
            }
        });
    }
);
