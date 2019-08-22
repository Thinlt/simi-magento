/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'ko',
        'jquery',
        'underscore',
        'Vnecoms_VendorsShipping/js/view/shipping',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Catalog/js/price-utils',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/checkout-data'
    ],
    function (
        ko,
        $,
        _,
        Component,
        shippingService,
        priceUtils,
        quote,
        selectShippingMethodAction,
        checkoutData
    ) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Vnecoms_VendorsShipping/cart/shipping-rates'
            },
            /**
             * Format shipping price.
             * @returns {String}
             */
            getFormattedPrice: function (price) {
                return priceUtils.formatPrice(price, quote.getPriceFormat());
            },
            /**
             * Change date range
             */
            changeVendorMethod: function (shippingMethod, event) {
                /*combine selected vendor shipping methods and select the main method*/
                var methods = {};
                var isAllVendorMethodSelected = true;
                $(this.ratesByVendor()).each(function(index, vendorRates){
                	methods[index] = vendorRates.selectedRate();
                    if (!methods[index]) {isAllVendorMethodSelected = false; }
                });
                
                if (!isAllVendorMethodSelected) {return true; }
                
                /*Sort vendor methods by increment of vendor id*/
                methods = this.sortVendorMethods(methods);
                
                var selectedMethodCode = methods.join(this.METHOD_SEPARATOR);
                console.log(selectedMethodCode);
                var selectedMethod = false;
                /*Get the currently selected method*/
                $(this.rates()).each(function (index, method) {
                    if (method.method_code == selectedMethodCode) {selectedMethod = method; }
                });
                if (selectedMethod !== false) {
                    /*Call the parent class method*/
                    this.selectShippingMethod(selectedMethod);
                }
                
                return true;
            },
            test: function () {
                
            }
        });
    }
);
