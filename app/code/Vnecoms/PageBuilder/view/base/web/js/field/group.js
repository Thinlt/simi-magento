define([
    'uiCollection',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    "mage/adminhtml/wysiwyg/widget"
], function (Element, layout, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
        	isActive: true,
        	visible: true,
        	isMouseOver: false,
        	frontend_class: '',
        	fieldPrefix: '',
        	parentObj: false,
        	fields: {},
            listens: {
            	isActive: 'elementChanged',
            }
        },
        
        initialize: function () {
            this._super();
            this.initChildFields();
            return this;
        },

        initChildFields: function(){
        	for(var fieldId in this.fields){
        		var field 		= this.fields[fieldId];
        		var fieldData 	= JSON.parse(JSON.stringify(this.getPageBuilder().fieldsData[field.type]));
        		if(!fieldData) continue;
        		fieldData['label'] 			= field.label?field.label:fieldId;
        		fieldData['isActive'] 		= field.is_active=='1'?true:false;
        		fieldData['config'] 		= field.data;
        		fieldData['name'] 			= this.name+'.'+fieldId;
        		fieldData['displayArea'] 	= fieldId;
        		fieldData['position'] 		= fieldId;
        		fieldData['parent'] 		= this.name;
        		fieldData['parentObj'] 		= this;
        		fieldData['editable'] 		= this.editable;
        		fieldData['pagebuilder'] 	= this.pagebuilder;
        		fieldData['fieldPrefix'] 	= this.getFieldId();
        		this.addElement(fieldData);
        	}
        },
        
        addElement: function(elementData){
        	layout([elementData]);
        },
        
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['isActive', 'visible', 'isMouseOver']);

            return this;
        },
        
        /**
         * Get element label
         */
        getLabel: function(){
        	var self = this;
        	var label = this.label;
        	if(this.label_element){
        		this.elems().each(function(elm){
        			if(elm.position == self.label_element){
        				label = elm.getValue();
        			}
        		});
        	}
        	return label.length > 47?
        			label.substr(0, 47) + ' ...':
    				label;
        },
        
        /**
         * Get Field Id
         */
        getFieldId: function(){
        	return this.fieldPrefix+'group_'+this.position;
        },
        
        /**
         * Get Field Id
         */
        getPreviewFieldId: function(){
        	return this.fieldPrefix+'preview_group_'+this.position;
        },
        
        /**
         * Get Edit Template
         */
        getEditTemplate: function(){
        	return this.edit_template;
        },
        
        /**
         * Get page builder object
         */
        getPageBuilder: function(){
        	return this.pagebuilder;
        },
        
        /**
         * Element has been changed
         */
        elementChanged: function(){
        	if(this.parentObj){
        		this.parentObj.elementChanged();
    		}else{
    			this.getPageBuilder().updateContent();
    		}
        },
        
        /**
         * Mouse is moving in element
         */
        mouseOverElement: function(){
        	this.isMouseOver(true);
        },
        
        /**
         * Mouse is moved out of element
         */
        mouseOutElement: function(){
        	this.isMouseOver(false);
        },
        
        /**
         * Toggle active field
         */
        toggleActive: function(){
        	this.isActive(!this.isActive());
        },
        
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var elementsData = {};
        	this.elems().each(function(element){
        		elementsData[element.displayArea] = element.getJsonData();
        		elementsData[element.displayArea]['type'] = element.id;
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
