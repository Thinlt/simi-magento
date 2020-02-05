define([
	'./list',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function (Element, layout, utils, $t, confirm) {
    'use strict';

    return Element.extend({
    	defaults: {
    		FIELD_TYPE_SECTION: 'section'
        },
        
        initialize: function () {
            this._super();
            this.initChildrenSections();
            return this;
        },
        
        /**
         * Init Children Sections
         */
        initChildrenSections: function(){
        	var pagebuilder = this.getPageBuilder();
        	var parent = this.name;
        	var sortOrder = 0;
        	for(var fieldId in this.fields){
        		var field 		= this.fields[fieldId];
        		if(field.type != this.FIELD_TYPE_SECTION) continue;
        		var sectionId = field.data.section_name;
        		if(!pagebuilder.sectionsData[sectionId]) continue;
        		var sectionData = pagebuilder.sectionsData[sectionId];
        		var elementData = JSON.parse(JSON.stringify(sectionData));
        		var uniqueId = Math.floor(Math.random() * 10000);
        		elementData['name'] 		= sectionData.id+uniqueId;
            	elementData['parent'] 		= this.name;
            	elementData['sectionType'] 	= sectionData.type;
            	var sectionId = this.id + 'sec'+uniqueId;
            	elementData['sectionId'] 	= sectionId;
            	elementData['pagebuilder'] 	= pagebuilder;
            	elementData['sortOrder']	= sortOrder++;
            	//elementData['fields'] = sectionData.fields;
            	this.addElement(elementData);
        	}
        },
                /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var elementsData = {};
        	var self = this;
        	this.elems().each(function(element){
        		elementsData[element.position] = element.getJsonData();
        		elementsData[element.position]['type'] = element.id;
        		elementsData[element.position]['templateItem'] = self.templateItem;
        		elementsData[element.position]['data'] = {};
        		elementsData[element.position]['data']['sortOrder'] = element.sortOrder;
        	});
        	return {
        		/*type: this.id,
        		position: this.displayArea,*/
        		is_active: this.isActive(),
        		data:{
        			/*block_template: this.block_template*/
        		},
        		fields: elementsData,
    		};
        }
        
    });
});
