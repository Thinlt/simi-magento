/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define,alert*/
define(
    [
        '../model/shipping'
    ],
    function (shipping) {
        "use strict";
        return function (shippingMethod) {
            var selectedVendorMethods = shipping.getSelectedVendorShippingMethods();
            var methods = selectedVendorMethods();
            var index = 'vendor_'+shippingMethod.vendor_id;
            
            methods[index] = shippingMethod.method_code;
            selectedVendorMethods(methods);
        }
    }
);
