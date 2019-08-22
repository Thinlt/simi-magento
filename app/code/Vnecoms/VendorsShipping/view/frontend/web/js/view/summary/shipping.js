/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
/*global alert*/
define(
    [
        'jquery',
        'Magento_Tax/js/view/checkout/summary/shipping',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-service',
    ],
    function ($, Component, quote, shippingService) {
        return Component.extend({
            /*Const variables*/
            CARRIER_CODE: 'vendor_multirate',
            SEPARATOR: '||',
            METHOD_SEPARATOR: '|_|',
            
            defaults: {
                template: 'Vnecoms_VendorsShipping/summary/shipping'
            },
            quote: quote,
            initialize: function () {
                this._super();
                window.TEST = this;
            },
            getRatesByVendor: function () {
                var self = this;
                var groups = {};
                var vendorsList = checkoutConfig.vendors_list; /*The list of vendors that have products in current shopping cart*/
                shippingService.getShippingRates().each(function (rate) {
                    if (rate.carrier_code == 'vendor_multirate' || !rate.method_code) {return; }
                    var tmp = rate.method_code.split(self.SEPARATOR);
                    
                    if (tmp.length != 2) {
    return; }
                    var vendorId = tmp[1];
                    var index = 'vendor_' + vendorId;
                    if (vendorsList[index] == 'undefined') {
    return; }
                    
                    if (typeof(groups[index]) == 'undefined') {
                        groups[index] = {
                            vendor_id: vendorId,
                            vendor: vendorsList[index],
                            title: vendorsList[index].shipping_title,
                            rates: []
                        };
                    }
                    rate['vendor_id'] = vendorId;
                    groups[index]['rates'].push(rate);
                });
                
                return groups;
            },
            getDetails: function () {
                var self = this;
                var details = [];
                var vendorRates = this.getRatesByVendor();
                
                var shippingMethod = quote.shippingMethod();
                if (!shippingMethod) {
    return details; }
                var methods = typeof(shippingMethod.method_code) != 'undefined'?shippingMethod.method_code:false;
                
                if (!methods) {
    return details; }
                
                methods = methods.split(this.METHOD_SEPARATOR);
                
                for (var index in methods) {
                    var methodCode = methods[index];
                    var vendorId = methodCode.toString().split(self.SEPARATOR);
                    vendorId = vendorId[1];
                    if (vendorRates['vendor_'+vendorId] != undefined) {
                        var rates = vendorRates['vendor_'+vendorId]['rates'];
                        for (var i in rates) {
                            if (methodCode == (rates[i].carrier_code+'_'+rates[i].method_code)) {
                                rates[i].vendor = vendorRates['vendor_'+vendorId].vendor;
                                details.push(rates[i]);
                                break;
                            }
                        }
                    }
                }
                
                return details;
            },
            formatPrice: function (amount) {
                return this.getFormattedPrice(amount);
            }
        });
    }
);
