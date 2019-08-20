/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    'jquery',
    'underscore',
    'mage/template',
    'priceUtils',
    "Magento_Ui/js/modal/alert",
    'priceBox',
    'jquery/ui',
    'jquery/jquery.parsequery',
    'Vnecoms_Credit/ionslider/ion.rangeSlider.min'
], function ($, _, mageTemplate,utils,alert) {
    'use strict';
    
    $.widget('vnecoms.credit', {
        options: {
            valueFieldSelector: '#vnecoms_credit_value',
            creditSliderSelector: '#vnecoms-credit-slider',
            priceHolderSelector: '.price-box',
            creditType: "1",
            creditTypes: {},
            creditOptions: {}
        },

        /**
         * Creates widget
         * @private
         */
        _create: function () {
            // Initial setting of various option values
        	var options = this.options;
        	if(options.creditType == options.creditTypes.TYPE_OPTION){
        		this._initializeOptions();
        	}else if(options.creditType == options.creditTypes.TYPE_RANGE){
        		this._initRangeSlider();
        	}
        	
        	/*Bind events*/
        	this._bindEvents();
        },
        
        /**
         * Initialize options
         */
        _initializeOptions: function(){        	
        	var creditOptions = this.options.creditOptions;
        	var valueField = $(this.options.valueFieldSelector);
        	$(creditOptions).each(function(index,option){
        		valueField.append($("<option/>", {
        	        value: option.value,
        	        text: option.label
        	    }));
        	});
        },
        
        /**
         * Bind events
         */
        _bindEvents: function(){
        	var options = this.options;
        	var valueField = $(this.options.valueFieldSelector);
        	if(options.creditType == options.creditTypes.TYPE_OPTION){
        		$(valueField).on('change', this, this._changeCreditDropdown);
        	}else if(options.creditType == options.creditTypes.TYPE_RANGE){
        		$(valueField).on('change', this, this._changeCreditCustom);
        	}
        },
        
        /**
         * Change credit dropdown.
         */
        _changeCreditDropdown: function(){
        	var options = $(this).credit('option');
        	var priceBoxOption 	= $(options.priceHolderSelector).priceBox('option');
        	var priceFormat 	= priceBoxOption.priceConfig.priceFormat;
        	var priceTemplate 	= mageTemplate(priceBoxOption.priceTemplate);
        	
        	var value = this.value;
        	var price = 0;
        	
        	$(options.creditOptions).each(function(index,option){
        		if(option.value == value){price = option.price;}
        	});
            var priceData = {formatted:utils.formatPrice(price, priceFormat)};
            $('[data-price-type="finalPrice"]').html(priceTemplate({data: priceData}));
        },
        
        /**
         * Init credit slider
         */
        _initRangeSlider: function(){
        	var options = this.options;
        	var exchangeRate 	= options.creditOptions.rate;
        	var currencyRate	= options.creditOptions.currency_rate;
        	var priceBoxOption 	= $(options.priceHolderSelector).priceBox('option');
        	var priceFormat 	= priceBoxOption.priceConfig.priceFormat;
        	var priceTemplate 	= mageTemplate(priceBoxOption.priceTemplate);
        	var initValue 		= options.creditOptions.init_value;
        	var $this			= this;
			
        	$(options.creditSliderSelector).slider({
				range: "min",
				value: initValue,
				animate: "1000",
				min: options.creditOptions.from,
				max: options.creditOptions.to,
				slide: function( event, ui ) {
					$this._changePrice(ui.value);
					$(options.valueFieldSelector).val(ui.value);
				}
			});
        	
			/*$(options.creditSliderSelector).ionRangeSlider({
				min: options.creditOptions.from,
				max: options.creditOptions.to,
				from: initValue,
				type: 'single',
				step: 1,
				from_shadow: true,
				hide_min_max: true,
				hide_from_to: true,
				grid: true,
				keyboard: true,
				prettify: function (num) {
					var price = num * currencyRate;
					var priceData = {formatted:utils.formatPrice(price, priceFormat)};
					
					return priceTemplate({data: priceData});
			    },
				onChange:function(data) {
					$this._changePrice(data.from);
					$(options.valueFieldSelector).val(data.from);
				}
			});*/
			
			$this._changePrice(initValue);
			$(options.valueFieldSelector).val(initValue);
			
        },
        /**
         * Change price of price box
         */
        _changePrice: function(value){
        	var options = this.options;
        	var exchangeRate 	= options.creditOptions.rate;
        	var currencyRate	= options.creditOptions.currency_rate;
        	var priceBoxOption 	= $(options.priceHolderSelector).priceBox('option');
        	var priceFormat 	= priceBoxOption.priceConfig.priceFormat;
        	var priceTemplate 	= mageTemplate(priceBoxOption.priceTemplate);
        	
        	var price = value * currencyRate/exchangeRate;
			
			var priceData = {formatted:utils.formatPrice(price, priceFormat)};
			$('[data-price-type="finalPrice"]').html(priceTemplate({data: priceData}));
        },
        
    	/**
    	 * Change credit custom
    	 */
        _changeCreditCustom: function(){
        	var options = $(this).credit('option');
        	var priceBoxOption 	= $(options.priceHolderSelector).priceBox('option');
        	var priceFormat 	= priceBoxOption.priceConfig.priceFormat;
        	var priceTemplate 	= mageTemplate(priceBoxOption.priceTemplate);
        	var exchangeRate 	= options.creditOptions.rate;
        	var currencyRate	= options.creditOptions.currency_rate;
        	
        	var value = parseInt(this.value);

        	if(value > options.creditOptions.to){
        		value = options.creditOptions.to;
        		$(this).val(value);
        	}else if(value < options.creditOptions.from || !value || value=="NaN"){
        		value = options.creditOptions.from;
        		$(this).val(value);
        	}
        	
        	/*Change price*/
        	var price = value  * currencyRate/exchangeRate;
            var priceData = {formatted:utils.formatPrice(price, priceFormat)};
            $('[data-price-type="finalPrice"]').html(priceTemplate({data: priceData}));
        	
        	/*Change the value of the slider*/
        	$(options.creditSliderSelector).slider("value",value);
        	
            /*var slider = $(options.creditSliderSelector).data('ionRangeSlider');
            slider.update({from:value});*/
            
        },        

    });

    return $.vnecoms.credit;
});
