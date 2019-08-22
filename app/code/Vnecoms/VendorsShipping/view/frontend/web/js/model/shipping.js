/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'ko',
        'Magento_Checkout/js/model/quote'
    ],
    function (ko, quote) {
        'use strict';

        var selectedVendorShippingMethods = ko.observable([]);

        return {
            /**
             * @return {Function}
             */
            getSelectedVendorShippingMethods: function () {
                return selectedVendorShippingMethods;
            }
        };
    }
);
