define([
    'uiCollection',
    'mageUtils',
    'uiLayout'
], function (Element, utils, layout) {
    'use strict';

    return Element.extend({
    	defaults: {
            sectionId: '',
            pagebuilder: '',
            fields: []
        },
        /**
         * Init Section
         */
        initialize: function () {
            this._super();
            this.initChildrenElements();
            return this;
        },
        
        /**
         * Init Fields
         */
        initChildrenElements: function(){
        	var pagebuilder = this.getPageBuilder();
        	var parent = this.name;
        	var editable = (this.elementType == pagebuilder.ELEMENT_TYPE_SECTION_LAYOUT);
        	for(var fieldId in this.fields){
        		var field 		= this.fields[fieldId];
        		if(!pagebuilder.fieldsData[field.type]) continue;
        		var fieldData 	= JSON.parse(JSON.stringify(pagebuilder.fieldsData[field.type]));
        		if(!fieldData) continue;
        		fieldData['label'] 			= field.label?field.label:field.id;
        		fieldData['isActive'] 		= field.is_active=='1'?true:false;
        		fieldData['config'] 		= field.data;
        		if(field.fields){
        			fieldData['config']['fields'] = field.fields;
        		}
        		fieldData['parent']			= parent;
        		fieldData['name'] 			= this.id+'_'+field.id;
        		fieldData['displayArea'] 	= field.id;
        		fieldData['position'] 		= fieldId;
        		fieldData['editable'] 		= editable;
        		fieldData['pagebuilder'] 	= pagebuilder;
        		fieldData['fieldPrefix'] 	= this.getSectionId();
        		layout([fieldData]);
        	}
        },
        
        /**
         * Get Section Id
         */
        getSectionId: function(){
        	return this.sectionId;
        },
        
        
        /**
         * Get Page Builder
         */
        getPageBuilder: function(){
        	return this.pagebuilder;
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
