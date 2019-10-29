define(
    [
        'jquery',
        'Magento_Checkout/js/view/summary/abstract-total'
    ],
    function ($, Component) {
        "use strict";

        var preOrderTotal = window.checkoutConfig.totalsData.total_segments.find(function(item){
            return (item.hasOwnProperty('code') && item.code === 'preorder_deposit') ? true : false;
        });

        return Component.extend({
            defaults: {
                template: 'Simi_Simicustomize/checkout/summary/preorder-discount',
                preorder_deposit: preOrderTotal ? preOrderTotal.title : ''
            },
            isDisplayedPreorderDeposit : function(){
                if (!preOrderTotal) {
                    return false;
                }
                return true;
            },
            getPreorderDeposit : function(){
                return preOrderTotal ? preOrderTotal.value : '$0';
            }
        });
    }
);