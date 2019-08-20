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
        	color: '',
        	colorHover: '',
            listens: {
            	isActive: 'elementChanged',
            	color: 'elementChanged',
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
                      'color'
                  ]);

            return this;
        },
        
        /**
         * Get custom style
         */
        getCustomStyle:function(){
        	var style = '';
        	if(this.color()){
        		style += '.pb-section-feature1 #'+this.getPreviewFieldId()+' .pb-feature-box-icon{background: '+this.color()+'}';
        		style += '.pb-section-feature1 #'+this.getPreviewFieldId()+' .pb-feature-title{color: '+this.color()+'}';
        	}
        	return style;
        },
        
        /**
         * Init color picker
         */
        initColorPicker: function(){
        	var self = this;
        	$('#'+this.getFieldId()+'_color').ColorPicker({
                color: self.color(),
                onChange: function (hsb, hex, rgb) {
                	self.color("#" + hex);
                }
            });
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var jsonData = this._super();
        	jsonData['data']['color'] = this.color();
        	return jsonData;
        }
    });
});
