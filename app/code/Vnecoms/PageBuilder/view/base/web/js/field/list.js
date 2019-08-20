define([
    'uiCollection',
    'uiLayout',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/confirm',
    "mage/adminhtml/wysiwyg/widget"
], function (Element, layout, utils, $t, confirm) {
    'use strict';

    return Element.extend({
        defaults: {
        	isActive: true,
        	visible: true,
        	frontend_class: '',
        	isMouseOver: false,
        	fieldPrefix: '',
        	editingItem: '',
        	itemEditVisible: '0',
        	parentObj: false,
        	fields: {},
        	templateItem: '', /* This will be set for last child element */
        	canAddNewItem: true,
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
        	var sortOrder = 0;
        	for(var fieldId in this.fields){
        		var field 		= this.fields[fieldId];
        		if(!this.getPageBuilder().fieldsData[field.type]) continue;
        		var fieldData 	= JSON.parse(JSON.stringify(this.getPageBuilder().fieldsData[field.type]));
        		if(!fieldData) continue;
        		fieldData['label'] 			= field.label?field.label:fieldId;
        		fieldData['isActive'] 		= field.is_active=='1'?true:false;
        		fieldData['config'] 		= field.data;
        		fieldData['name'] 			= this.name + '.' + fieldId;
        		fieldData['displayArea'] 	= 'children';
        		fieldData['position'] 		= fieldId;
        		if(field.fields){
        			fieldData['config']['fields'] = field.fields;
        		}
        		fieldData['parent'] 		= this.name;
        		fieldData['parentObj'] 		= this;
        		fieldData['editable'] 		= this.editable;
        		fieldData['pagebuilder'] 	= this.pagebuilder;
        		fieldData['fieldPrefix'] 	= this.getFieldId();
        		fieldData['sortOrder']		= field['sortOrder']?field['sortOrder']:sortOrder;
        		sortOrder ++;
        		if(fieldId == this.templateItem){
        			this.templateElement = fieldData;
        		}

        		this.addElement(fieldData);
        	}
        	if(!this.templateElement){
        		this.templateElement = fieldData;
        	}
        },
        
        /**
         * Can add new item
         */
        canAddNew: function(){
        	return parseInt(this.canAddNewItem);
        },
        
        /**
         * Get sorted items
         * 
         */
        getItems: function(){
        	var items = [];
        	var elems = this.elems();
        	elems.each(function(item){
        		for(var insertPosition = 0; insertPosition < elems.size(); insertPosition++){
    				if(item.sortOrder < elems[insertPosition].sortOrder){
    					break;
    				}
    			}
    			for(var i = items.size(); i > insertPosition ; i --){
    				items[i] = items[i-1];
    			}
    			items[i] = item;
        	});
        	return items;
        },
        
        /**
         * Add element
         */
        addElement: function(elementData){
        	layout([elementData]);
        },
        
        /**
         * add new element
         */
        addNewElement: function(){
        	var self = this;
        	var copyAttributes = [
	          	'template',
	          	'id',
	          	'edit_template',
	          	'component',
	          	'class',
	          	'block_template',
	          	'label',
	          	'type',
	          	'is_active',
	          	'data',
	          	'fields',
	          	'displayArea',
	          	'config',
	          	'parent',
	          	'editable',
	          	'pagebuilder',
	          	'fieldPrefix'
	        ];
        	var templateField = {};
        	copyAttributes.each(function(attr){
        		templateField[attr] = self.templateElement[attr];
        	});
        	var key = 'listitem-'+ Math.round(Math.random()*10000);
        	templateField['name'] = this.name + '.' + key;
        	templateField['position'] = key;
        	/*Get the max sort order from current list*/
        	var maxSortOrder = 0;
        	this.elems().each(function(elm){
        		if(elm.sortOrder > maxSortOrder) maxSortOrder = elm.sortOrder;
        	});
        	maxSortOrder ++;
        	templateField['sortOrder'] = maxSortOrder;
        	templateField['config']['sortOrder'] = maxSortOrder;
        	this.addElement(templateField);
        	this.elementChanged();
        },
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
                      'isActive',
                      'visible',
                      'isMouseOver',
                      'itemEditVisible',
                      'editingItem'
                  ]);

            return this;
        },
        
        /**
         * is active has been changed
         */
        elementChanged: function(){
        	if(this.parentObj){
        		this.parentObj.elementChanged();
    		}else{
    			this.getPageBuilder().updateContent();
    		}
        },
        
        /**
         * Get Field Id
         */
        getFieldId: function(){
        	return this.fieldPrefix+'list_'+this.position;
        },
        
        /**
         * Get Field Id
         */
        getPreviewFieldId: function(){
        	return this.fieldPrefix+'prev_list_'+this.position;
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
         * Delete a list item
         */
        deleteElement: function(element){
        	var self = this;
        	confirm({
    			modalClass: 'confirm vpb-confirm',
    			title: $t('Delete this element?'),
    			content: $t('This element will be deleted completly.'),
    			actions:{
    				confirm: function(){
    					self.removeChild(element);
    					self.elementChanged();
        			}
    			},
    			buttons: [{
                    text: $t('No'),
                    class: 'action-secondary action-dismiss vpb-action-no',
                    click: function (event) {
                        this.closeModal(event);
                    }
                }, {
                    text: $t('Yes'),
                    class: 'action-primary action-accept vpb-action-yes',
                    click: function (event) {
                        this.closeModal(event, true);
                    }
                }]
    		});
        },
        
        /**
         * Edit an item
         */
        editItem: function(element){
        	this.itemEditVisible(true);
        	this.editingItem(element);
        },
        
        /**
         * Move item up
         */
        moveItemUp: function(element){
        	var currentSortOrder = element.sortOrder;
        	if(currentSortOrder == 0) return;
        	
        	var nextSortOrder = currentSortOrder - 1;
        	this.elems().each(function(elm){
        		if(elm.sortOrder == nextSortOrder){
        			elm.sortOrder = currentSortOrder;
        			return false;
        		}
        	});
        	element.sortOrder = nextSortOrder;
        	this.elementChanged();
        	this._updateCollection();
        },
        
        /**
         * Move item up
         */
        moveItemDown: function(element){
        	var currentSortOrder = element.sortOrder;
        	
        	var nextSortOrder = currentSortOrder + 1;
        	var check = false;
        	this.elems().each(function(elm){
        		if(elm.sortOrder == nextSortOrder){
        			elm.sortOrder = currentSortOrder;
        			check = true;
        		}
        	});
        	if(!check) return;
        	element.sortOrder = nextSortOrder;
        	this.elementChanged();
        	this._updateCollection();
        },
        /**
         * Get section
         */
        getListItem: function(itemIndex){
        	return this.getChild(itemIndex);
        },
        /**
         * Close item edit modal
         */
        closeItemEdit: function(){
        	this.itemEditVisible(false);
        	this.editingItem(false);
        },
        
        getItemsSize: function(){
        	return this.elems().size();
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
