/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/components/group',
        'ko',
        'Vnecoms_VendorsShipping/js/view/shipping',
        'Vnecoms_VendorsShipping/js/model/shipping',
        'Vnecoms_VendorsShipping/js/action/select-shipping-method'
    ],
    function (
        $,
        _,
        Component,
        ko,
        shipping,
        shippingModel,
        selectVendorShippingMethodAction
    ) {
        'use strict';

        var popUp = null;

        return Component.extend({
            /*Const variables*/
            CARRIER_CODE: 'vendor_multirate',
            SEPARATOR: '||',
            METHOD_SEPARATOR: '|_|',
            
            /*Properties*/
            vendorId: false,
            /**
             * @return {exports}
             */
            initialize: function () {
                this._super();
                return this;
            },
            isSelected: ko.computed(function () {
                    return false;
                }),
            /**
             * Change date range
             */
            changeVendorMethod: function (shippingMethod, event) {
                selectVendorShippingMethodAction(shippingMethod);
                console.log(this);
            },
            testTest: function (data) {
                console.log(data);
                alert('loaded');
            }
        });
    }
);
