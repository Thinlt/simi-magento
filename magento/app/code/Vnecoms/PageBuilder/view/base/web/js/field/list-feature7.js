define([
    'jquery',
    './list-feature1',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'jquery/colorpicker/js/colorpicker'
], function ($, Element, layout, utils, $t) {
    'use strict';

    return Element.extend({
    	/**
         * Get custom style
         */
        getCustomStyle:function(){
        	var style = '';
        	if(this.color()){
        		style += '.pb-section-feature7 #'+this.getPreviewFieldId()+' .pb-feature-box-icon{background-color: '+this.color()+'}';
        		style += '.pb-section-feature7 #'+this.getPreviewFieldId()+' .pb-feature-title{color: '+this.titleColor()+'}';
        	}
        	if(this.colorHover()){
        		style += '.pb-section-feature7 #'+this.getPreviewFieldId()+' .pb-feature-container:hover .pb-feature-box-icon{background-color: '+this.colorHover()+'}';
        	}
        	return style;
        },
    });
});
