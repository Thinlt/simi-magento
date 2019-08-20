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
        	color: '',
        	colorHover: '',
            listens: {
            	isActive: 'elementChanged',
            	titleColor: 'elementChanged',
            	color: 'elementChanged',
            	colorHover: 'elementChanged'
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
                      'titleColor',
                      'color',
                      'colorHover'
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
        		style += '.pb-section-feature1 #'+this.getPreviewFieldId()+' .pb-feature-title{color: '+this.titleColor()+'}';
        	}
        	if(this.colorHover()){
        		style += '.pb-section-feature1 #'+this.getPreviewFieldId()+' .pb-feature-container:hover .pb-feature-box-icon{background: '+this.colorHover()+'}';
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
        	
        	$('#'+this.getFieldId()+'_color').ColorPicker({
                color: self.color().replace('#',''),
                onChange: function (hsb, hex, rgb) {
                	self.color("#" + hex);
                }
            });
        	
        	$('#'+this.getFieldId()+'_colorhover').ColorPicker({
                color: self.colorHover().replace('#',''),
                onChange: function (hsb, hex, rgb) {
                	self.colorHover("#" + hex);
                }
            });
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var jsonData = this._super();
        	jsonData['data']['titleColor'] = this.titleColor();
        	jsonData['data']['color'] = this.color();
        	jsonData['data']['colorHover'] = this.colorHover();
        	return jsonData;
        }
    });
});
