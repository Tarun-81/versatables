define(
    [
        'Cryozonic_StripeSubscriptions/js/view/checkout/summary/initial_fee'
    ],
    function (Component) {
        'use strict';

        return Component.extend(
        {
            isDisplayed: function ()
            {
                return this.getPureValue() !== 0;
            }
        });
    }
);
