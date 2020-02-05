define([
    'Vnecoms_PageBuilder/js/section/default',
    'mageUtils',
    'uiLayout'
], function (Element, utils, layout) {
    'use strict';

    return Element.extend({
    	defaults: {
            FIELD_TYPE_SECTION: 'section'
        },
        
        /**
         * Init Section
         */
        initialize: function () {
            this._super();
            return this;
        },

        /**
         * Get Json Data
         */
        getJsonData: function(){
        	
    		var sectionData = {
				type: this.id,
				elements: {}
    		};
    		this.elems().each(function(element){
    			sectionData.elements[element.displayArea] = element.getJsonData();
    		});

        	return sectionData;
        },
    });
});
