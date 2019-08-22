/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true*/
/*global define*/
define(
    [
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/shipping-rate-processor/new-address',
        'Magento_Checkout/js/model/shipping-rate-processor/customer-address',
        'Magento_Checkout/js/model/shipping-rate-registry'
    ],
    function (quote, defaultProcessor, customerAddressProcessor, rateRegistry) {
        'use strict';

        var processors = [];

        processors.default =  defaultProcessor;
        processors['customer-address'] = customerAddressProcessor;


        return function () {
            var type = quote.shippingAddress().getType();
            var address = quote.shippingAddress();
            /*Unset Rate rates from registry*/
            rateRegistry.set(address.getCacheKey(), null);
            if (processors[type]) {
                processors[type].getRates(address);
            } else {
                processors.default.getRates(address);
            }
        }
    }
);
