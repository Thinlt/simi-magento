define([
    'uiElement',
    'mageUtils',
    'mage/translate'
], function (Element, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            visible: true,
            uid: utils.uniqueid(),
            isActive: true,
            edit_template: '',
            isMouseOver: false,
            pagebuilder: '',
            fieldPrefix: '',
            frontend_class: '',
            parentObj: false,
            listens: {
            	isActive: 'elementChanged',
            }
        },
        
        /**
         * Initialize
         */
        initialize: function () {
            this._super();
            return this;
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
         * Get Field Id
         */
        getFieldId: function(){
        	return this.fieldPrefix+'edit_elm_'+this.position;
        },
        
        /**
         * Get Field Id
         */
        getPreviewFieldId: function(){
        	return this.fieldPrefix+'preview_elm_'+this.position;
        },
        
        /**
         * Get element label
         */
        getLabel: function(){
        	return this.label.length > 50?
        			this.label.substr(0, 50) + ' ...':
    				this.label.length;
        },
        /**
         * Get value of the field
         */
        getValue: function(){
        	return '';
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
         * Get Frontend class
         */
        getFrontendClass: function(){
        	return this.frontend_class;
        },
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {};
        }
    });
});
