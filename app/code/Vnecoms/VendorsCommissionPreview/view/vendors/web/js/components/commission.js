define([
    'Magento_Ui/js/form/components/group',
    'jquery',
    'mage/translate',
    'uiRegistry',
    'uiLayout',
    'mageUtils'
], function (Element, $, $t, registry, layout, utils) {
    'use strict';

    return Element.extend({
    	defaults: {
    		queryTemplate: 'ns = ${ $.ns }, index = price',
    		timer: false,
    		timeout: 400,
    		commissionCalcUrl: '',
    		value: false,
    		isLoading: false
        },
        
        /** @inheritdoc */
        initialize: function () {
            this._super();

            this.setHandlers();
        },
        
        /**
         * Calls initObservable of parent class.
         * Defines observable properties of instance.
         *
         * @return {Object} - reference to instance
         */
        initObservable: function () {
            this._super()
                .observe('isLoading', false)
                .observe('value', false);

            return this;
        },
        
        /**
         * Split mask placeholder and attach events to placeholder fields.
         */
        setHandlers: function () {
        	registry.get(this.queryTemplate, function (component) {
                component.on('value', this.updateValue.bind(this, component));
                component.valueUpdate = 'keyup';
            }.bind(this));
        },
        
        updateValue: function(component){
        	clearTimeout(this.timer);
        	if(!component.getPreview().trim()) {
        		this.value(false);
        		return;
        	}
        	var self = this;
        	var productInfo = registry.get('product_form.product_form_data_source').data.product;
        	productInfo = JSON.stringify(productInfo);
        	
        	this.timer = setTimeout(function(){
        		self.isLoading(true);
        		
        		$.ajax({
          		  url: self.commissionCalcUrl,
          		  method: "POST",
          		  data: { 
          			  product: productInfo
      			  },
          		  dataType: "json"
      		}).done(function( response ){
      	  	  	if(response.ajaxExpired){
      	  	  	  	window.location = response.ajaxRedirect;
      	  	  	  	return;
      	  	  	}
      	  	  	if(response.redirect){
  	  	  	  	  	window.location = response.redirect;
  	  	  	  	  	return;
  	  	  	  	}
      	  	  	
      	  	  	if(response.error){
      	  	  		alert(response.msg);
      	  	  	}else{
      	  	  		self.value(response.commission);
      	  	  	}
      	  	  	self.isLoading(false);

      		});
        		
        	},this.timeout);
        }
    });
});
