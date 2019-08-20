/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
     	'jquery',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ],
    function ($,Component, quote) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Vnecoms_Credit/summary/credit'
            },
            totals: quote.getTotals(),
            isDisplayed: function() {
                return this.isFullMode() && this.getPureValue() != 0;
            },
            getPureValue: function() {
                var price = 0;
                if (this.totals() && this.totals().credit_amount) {
                    price = parseFloat(this.totals().credit_amount);
                }else{
                	$.each(this.totals().total_segments, function(index, total_segment){
                		if(total_segment.code=='credit'){
                			price = total_segment.value;
                		}
                	});
                }
                return price;
            },
            getValue: function() {
                return this.getFormattedPrice(this.getPureValue());
            }
        });
    }
);
