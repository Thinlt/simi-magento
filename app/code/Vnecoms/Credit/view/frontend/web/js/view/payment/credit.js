/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define(
    [
        'jquery',
        'ko',
        'uiComponent',
        'Magento_Checkout/js/model/quote',
        'Vnecoms_Credit/js/action/set-credit',
        'Vnecoms_Credit/js/action/cancel-credit',
        'priceUtils',
        'Vnecoms_Credit/js/model/payment/credit-messages',
        'mage/translate',
        'tooltip',
    ],
    function ($, ko, Component, quote, setCreditAction, cancelCreditAction, utils,messageContainer,$t) {
        'use strict';
        var quoteData = window.checkoutConfig.quoteData;
        var totals = quote.getTotals();
        var creditAmount = ko.observable(Math.abs(quoteData.credit_amount));
        var baseCreditAmount = ko.observable(Math.abs(quoteData.base_credit_amount));
        var creditBalance = 0;
        if(typeof(window.checkoutConfig.storeCredit) != 'undefined')
        	creditBalance = parseFloat(window.checkoutConfig.storeCredit.balance);
        var isCustomerLoggedIn = window.checkoutConfig.isCustomerLoggedIn;
        var canUseCreditConfig = window.checkoutConfig.canUseCredit;
/*        if (totals()) {
        	$.each(totals().total_segments, function(index, total_segment){
        		if(total_segment.code=='credit'){
        			var value = parseFloat(total_segment.value).toFixed(2);
        			value = Math.abs(value);
        			creditAmount(value);
        		}
        	});
        }*/
        var isApplied = ko.observable(baseCreditAmount() > 0);
        var isLoading = ko.observable(false);
        
        return Component.extend({
            defaults: {
                template: 'Vnecoms_Credit/payment/credit'
            },
            creditBalance: creditBalance,
            customerLoggedIn: isCustomerLoggedIn,
            canUseCreditConfig: canUseCreditConfig,
            
            /**
             * used credit amount
             */
            creditAmount: creditAmount,
            baseCreditAmount: baseCreditAmount,
            /**
             * Applied flag 
             */
            isApplied: isApplied,
            isLoading: isLoading,

            /**
             * Is customer logged in
             */
            isCustomerLoggedIn: function(){
            	return this.customerLoggedIn;
            },
            canUseCredit: function(){
            	return this.canUseCreditConfig;
            },
            /**
             * Get available credit
             */
            getAvailableCredit: function(){
            	return this.formatBasePrice(this.creditBalance);
            },
            
            /**
             * Get credit balance of current customer
             */
            getCreditBalance: function(){
            	return this.creditBalance;
            },
            
            /**
             * Format price to current currency
             * @param float amount
             * @return string
             */
            formatPrice: function(amount){
            	var priceFormat 	= quote.getPriceFormat();
            	return utils.formatPrice(amount, priceFormat);
            },
            /**
             * Format price to base currency
             * @param float amount
             * @return string
             */
            formatBasePrice: function(amount){
            	var basePriceFormat 	= quote.getBasePriceFormat();
            	return utils.formatPrice(amount, basePriceFormat);
            },
            /**
             * is currently using base currency
             * @return boolean
             */
            currentCurrencyIsBaseCurrency: function(){
            	var basePriceFormat	= quote.getBasePriceFormat();
            	var priceFormat 	= quote.getPriceFormat();
            	return basePriceFormat.pattern == priceFormat.pattern; 
            },
            /**
             * Init tooltip
             */
            initTooltip: function(){
            	$(".credit-icon").tooltip();
            	return '';
            },
            /**
             * Enable/Disable edit mode
             */
            readOnlyMode: function(readOnlyMode){
            	var usedCreditCotnainer = $(".used-credit-container");
            	var creditAmountContainer = $('.credit-amount-cotainer');
            	if(readOnlyMode === true){
            		if(usedCreditCotnainer.hasClass('_hide')) usedCreditCotnainer.removeClass('_hide');
	            	if(creditAmountContainer.hasClass('_show')) creditAmountContainer.removeClass('_show');
            	}else{
            		if(!usedCreditCotnainer.hasClass('_hide')) usedCreditCotnainer.addClass('_hide');
	            	if(!creditAmountContainer.hasClass('_show')) creditAmountContainer.addClass('_show');
            	}
            },
            /**
             * Set edit mode
             */
            setEditMode: function(){
            	this.readOnlyMode(false);
            },
            /**
             * Set read only mode
             */
            setReadOnlyMode: function(){
            	this.readOnlyMode(true);
            },
            
            /**
             * Coupon code application procedure
             */
            apply: function() {
                if (this.validate()) {
                    isLoading(true);
                    setCreditAction(baseCreditAmount,creditAmount, isApplied, isLoading);
                    this.readOnlyMode(true);
                }
            },
            
            /**
             * Cancel using credit
             */
            cancel: function() {
                isLoading(true);
                creditAmount('');
                baseCreditAmount('');
                cancelCreditAction(isApplied, isLoading);
                this.readOnlyMode(true);
            },
            
            /**
             * Toggle use credit
             */
            toggleUseCredit: function(){
            	var creditContainer = $('#use-credit-container');
            	if($("#use-store-credit").prop("checked")){
            		if(!creditContainer.hasClass('_show')) 
            			creditContainer.addClass('_show');
            	}else{
            		if(creditContainer.hasClass('_show')) 
            			creditContainer.removeClass('_show');
            		if(isApplied()) this.cancel();
            	}
            },
            /**
             * Coupon form validation
             *
             * @returns {boolean}
             */
            validate: function() {
                var form = '#credit-form';
                var amount = parseFloat($("#credit-amount").val());
                var check = (amount <= this.creditBalance);
                if(!check){
                	messageContainer.addErrorMessage({'message': $t('You can\'t apply the amount which is greater than your credit balance')});
                }
                return $(form).validation() && $(form).validation('isValid') && check;
            }
        });
    }
);
