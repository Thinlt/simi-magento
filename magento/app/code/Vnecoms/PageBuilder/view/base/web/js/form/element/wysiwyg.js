define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiLayout',
    'underscore',
    'ko',
    'mageUtils',
    'Magento_Ui/js/form/element/wysiwyg',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function ($, layout, _, ko, utils, Element, $t, confirm) {
    'use strict';

    return Element.extend({
    	defaults: {
            elementTmpl: 'Vnecoms_PageBuilder/form/element/wysiwyg',
            editor_obj_name: ''
        },

        /**
         * Get container
         */
        getContainer: function(){
        	return this.containers[0];
        },
        /**
         * Get page builder element
         */
        getPageBuilder: function(){
        	var pageBuilder;
        	this.getContainer().elems().each(function(elm){
        		if(elm.index == 'pagebuilder'){
        			pageBuilder = elm;
        		}
        	});
        	/* Set the editor object*/
        	pageBuilder.editor = this;
        	
        	return pageBuilder
        },
        /**
         * Is enabled page builder
         */
        enablePageBuilder: function(){
        	return this.getPageBuilder().enablePageBuilder();;
        },
        /**
         * Parse content
         */
        parseContent: function(){
        	this.getPageBuilder().parseContent();
        },
        /**
         * Toggle page builder
         */
        togglePageBuilder: function(){
        	if(window[this.editor_obj_name]){
        		window[this.editor_obj_name].turnOff();
        	}
        	
        	var isEnabledPageBuilder = this.getPageBuilder().enablePageBuilder();
        	if(isEnabledPageBuilder){
        		var self = this;
        		confirm({
        			modalClass: 'confirm vpb-confirm',
        			title: $t('Disable Page Builder?'),
        			content: $t('All changes you made with page builder will be lost.'),
        			actions:{
        				confirm: function(){
            				self.getPageBuilder().enablePageBuilder(!isEnabledPageBuilder);
            				self.getPageBuilder().updateContent();
            				self.getPageBuilder().sectionModalVisible('0');
            				self.getPageBuilder().sectionEditVisible('0');
            			}
        			},
        			buttons: [{
                        text: $.mage.__('No'),
                        class: 'action-secondary action-dismiss vpb-action-no',
                        click: function (event) {
                            this.closeModal(event);
                        }
                    }, {
                        text: $.mage.__('Yes'),
                        class: 'action-primary action-accept vpb-action-yes',
                        click: function (event) {
                            this.closeModal(event, true);
                        }
                    }]
        		});
        		return;
        	}
        	
        	this.getPageBuilder().enablePageBuilder(!isEnabledPageBuilder);
        },
    });
});
