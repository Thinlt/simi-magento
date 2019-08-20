define([
    'jquery',
    './list',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'jquery/colorpicker/js/colorpicker'
], function ($, Element, layout, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
        	titleColor: '',
            listens: {
            	isActive: 'elementChanged',
            	titleColor: 'elementChanged',
            }
        },
        
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
            	.observe([
                	'titleColor'
                ]);

            return this;
        },
        
        /**
         * Get custom style
         */
        getCustomStyle:function(){
        	var style = '';
        	if(this.titleColor()){
        		style += '.pb-section-faq2 #'+this.getPreviewFieldId()+' .pb-feature-title{color: '+this.titleColor()+'}';
        	}
        	return style;
        },
        
        /**
         * Init color picker
         */
        initColorPicker: function(){
        	var self = this;
        	$('#'+this.getFieldId()+'_titlecolor').ColorPicker({
                color: self.titleColor().replace('#',''),
                onChange: function (hsb, hex, rgb) {
                	self.titleColor("#" + hex);
                }
            });
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var jsonData = this._super();
        	jsonData['data']['titleColor'] = this.titleColor();
        	return jsonData;
        }
    });
});
