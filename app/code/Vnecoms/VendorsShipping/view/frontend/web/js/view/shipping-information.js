/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Checkout/js/view/shipping-information',
        'Magento_Checkout/js/model/quote'
    ],
    function ($ , Component, quote) {
        'use strict';
        return Component.extend({
            /*Const variables*/
            SEPARATOR: '||',
            METHOD_SEPARATOR: '|_|',


            getShippingMethodTitle: function () {
                var self = this;
                var vendorsList = checkoutConfig.vendors_list;
                var shippingMethod = quote.shippingMethod();
                if (!shippingMethod) {
    return ""; }
                var methods = shippingMethod.method_title;
                methods = methods.split(self.METHOD_SEPARATOR);
                var methodTitle = "";
                for (var index in methods) {
                    var methodCode = methods[index];
                    var vendorMethodTitle = methodCode.toString().split(self.SEPARATOR);
                    var vendorId = vendorMethodTitle[1];
                    if (vendorsList['vendor_'+vendorId] != undefined) {
                        var vendorTitle = vendorsList['vendor_'+vendorId];
                        vendorTitle = vendorTitle.vendor_id;
                        vendorMethodTitle = vendorTitle+" : "+vendorMethodTitle[0];
                        methodTitle += vendorMethodTitle+" | ";
                    }
                }
                methodTitle = methodTitle.trim();
                methodTitle = methodTitle.slice(0,-1);
                return methodTitle;
            }
        });
    }
);
