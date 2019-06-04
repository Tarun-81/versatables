/*browser:true*/
/*global define*/
define(
    [
        'ko',
        'jquery',
        'Magento_Checkout/js/model/quote',
		'Magento_Checkout/js/model/cart/totals-processor/default'
    ],
    function (ko, $, quote, totalsDefaultProvider) {
        'use strict';
        
				$(document).ready(function(){
					$(document).on('change', "select[name='region_id']", function () {
						totalsDefaultProvider.estimateTotals(quote.shippingAddress());
					});
				});
           
    }
);