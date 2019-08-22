/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'jquery',
        'underscore',
        'Magento_Checkout/js/view/shipping',
        'ko',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/action/create-shipping-address',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-address/form-popup-state',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/action/select-shipping-method',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/action/set-shipping-information',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Ui/js/modal/modal',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'Magento_Checkout/js/checkout-data',
        'uiRegistry',
        'mage/translate',
        'Vnecoms_VendorsShipping/js/action/select-shipping-method',
        'Vnecoms_VendorsShipping/js/action/reload-shipping-rates',
        'Vnecoms_VendorsShipping/js/model/shipping',
        'Magento_Checkout/js/model/shipping-rate-service',
        'Magento_Checkout/js/action/get-totals'
    ],
    function (
        $,
        _,
        Component,
        ko,
        customer,
        addressList,
        addressConverter,
        quote,
        createShippingAddress,
        selectShippingAddress,
        shippingRatesValidator,
        formPopUpState,
        shippingService,
        selectShippingMethodAction,
        rateRegistry,
        setShippingInformationAction,
        stepNavigator,
        modal,
        checkoutDataResolver,
        checkoutData,
        registry,
        $t,
        selectVendorShippingMethodAction,
        reloadShippingRatesAction,
        vendorShippingMethod,
        shippingRateService,
        getTotalsAction
    ) {
        'use strict';

        var popUp = null;
        return Component.extend({
            defaults: {
                removeAllVendorItemsUrl: '',
                selectedVendorMethods: {},
                ratesByVendor: ko.observable([]),
                track: {
                	selectedVendorMethods: true,
                }
            },
            /*Const variables*/
            CARRIER_CODE: 'vendor_multirate',
            SEPARATOR: '||',
            METHOD_SEPARATOR: '|_|',

            /**
             * @return {exports}
             */
            initialize: function () {
                var self = this;
                this._super();
                this.initRatesByVendor();
                shippingService.getShippingRates().subscribe(function () {
                	this.initRatesByVendor();
                	this.initSelectedVendorMethods();
                }.bind(this));
                
                this.initSelectedVendorMethods();
                quote.shippingMethod.subscribe(function () {
                	this.initSelectedVendorMethods();
                }.bind(this));
                return this;
            },
            /**
             * Init selected vendor methods
             */
            initSelectedVendorMethods: function () {
            	console.log('INIT SELECTED VENDOR METHODS');
                var selectedShippingRates = '';
                if(quote.shippingMethod()){
            		selectedShippingRates = quote.shippingMethod()['carrier_code'] + '_' + quote.shippingMethod()['method_code'];
            	}
                if(!selectedShippingRates && checkoutData.getSelectedShippingRate()){
                	selectedShippingRates = checkoutData.getSelectedShippingRate();
                }
                
                selectedShippingRates = selectedShippingRates.replace('vendor_multirate_','').split(this.METHOD_SEPARATOR);

                var selectedVendorMethods = {};
                if (selectedShippingRates.length) {
                    $(selectedShippingRates).each(function (index, methodCode) {
                        var vendorId = methodCode.split(this.SEPARATOR);
                        if (vendorId.length == 2) {
                            vendorId = 'vendor_' + vendorId[1];
                            selectedVendorMethods[vendorId] = methodCode;
                        }
                    }.bind(this));
                }
                console.log(selectedVendorMethods);
                var ratesByVendor = this.ratesByVendor();
                $(ratesByVendor).each(function(index, vendorRates){
                	var key = 'vendor_'+vendorRates.vendor_id;
                	var selectedRateVal = typeof(selectedVendorMethods[key]) != 'undefined'?selectedVendorMethods[key]:false;
                	ratesByVendor[index].selectedRate(selectedRateVal);
                });
                this.ratesByVendor(ratesByVendor);
            },
            rates: function () {
                var self = this;
                var rates = shippingService.getShippingRates();
                var vendorRates = [];
                rates.each(function (rate) {
                    if (rate.carrier_code == self.CARRIER_CODE) {
                        vendorRates.push(rate);
                    }
                });
                
                return vendorRates;
            },
            /**
             * Get rates by vendor
             */
            initRatesByVendor: function () {
            	console.log('INIT RATES BY VENDOR');
                var self = this;
                var groups = {};
                var vendorsList = checkoutConfig.vendors_list; /*The list of vendors that have products in current shopping cart*/
                
                shippingService.getShippingRates().each(function (rate) {
                   if (rate.carrier_code == 'vendor_multirate' || !rate.method_code) {return; }
                    var tmp = rate.method_code.split(self.SEPARATOR);
                    
                    if (tmp.length != 2) {return; }
                    var vendorId = tmp[1];
                    var index = 'vendor_' + vendorId;
                    if (vendorsList[index] == 'undefined') {return; }
                    
                    if (typeof(groups[index]) == 'undefined') {
                        groups[index] = {
                    		selectedRate:ko.observable(false),
                            vendor_id: vendorId,
                            vendor: vendorsList[index],
                            title: vendorsList[index].shipping_title,
                            rates: []
                        };
                    }
                    rate['vendor_id'] = vendorId;
                    groups[index]['rates'].push(rate);
                });
                
                /**
                 * Add null vendor if they do not have any method
                 */
                for (var key in vendorsList) {
                    if (typeof(groups[key]) == 'undefined') {
                        groups[key] = {
                    		selectedRate:ko.observable(false),
                            vendor_id: vendorsList[key].entity_id,
                            vendor: vendorsList[key],
                            title: vendorsList[key].shipping_title,
                            rates: []
                        }
                    }
                }
                this.ratesByVendor(this.sortRates(groups))
            },
            /**
             * Sort the groups by vendor id ASC
             */
            sortRates: function (groups) {
                var sortedGroups = [];
                while (true) {
                    var minIndex = false;
                    for (var index in groups) {
                        if (!minIndex) {
                            minIndex = index;
                        } else if (parseInt(groups[index].vendor_id) < parseInt(groups[minIndex].vendor_id) ) {
                            minIndex = index;
                        }
                    }
                    if (!minIndex) {break; }
                    sortedGroups.push(groups[minIndex]);
                    delete(groups[minIndex]);
                }
                
                return sortedGroups;
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
            
            sortVendorMethods: function (methods) {
                var sortedMethods = [];
                while (true) {
                    var minIndex = false;
                    for (var index in methods) {
                        if (!minIndex) {
                            minIndex = index;
                        } else {
                            var minVendorId = parseInt(minIndex.replace('vendor_',''));
                            var vendorId = parseInt(index.replace('vendor_',''));
                            if (minVendorId > vendorId) {minIndex = index; }
                        }
                    }
                    if (!minIndex) {break; }
                    sortedMethods.push(methods[minIndex]);
                    delete(methods[minIndex]);
                }
                
                return sortedMethods;
            },
            
            validateShippingInformation: function () {
                 if (!this.isAllVendorMethodSelected()) {
                     this.errorValidationMessage('Please specify shipping method for all vendors.');
                     return false;
                 }
                 
                 return this._super();
            },
            
            isAllVendorMethodSelected: function () {
                for (var index in this.selectedVendorMethods) {
                    if (this.selectedVendorMethods[index]() === false) {return false; }
                }
                return true;
            },
            
            removeAllVendorItems: function (vendorShipping) {
                var self = this;
                $.ajax({
                      url: this.removeAllVendorItemsUrl,
                      method: "POST",
                      data: {
                          vendor_id: vendorShipping.vendor_id
                      },
                      dataType: "json"
                }).done(function ( response ) {
                    if (response.ajaxExpired) {
                        window.location = response.ajaxRedirect;
                        return;
                    }
                    if (response.redirect) {
                        window.location = response.redirect;
                        return;
                    }
                    
                    window.checkoutConfig.vendors_list = response.vendors_list;
                    
                    reloadShippingRatesAction();
                    self.initSelectedVendorMethods();
                    
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                });
            }
        });
    }
);
