define([
    'Vnecoms_PageBuilder/js/field/text',
    'mageUtils',
    'mage/translate',
    "wysiwygAdapter",
    "mage/adminhtml/wysiwyg/widget"
], function (Element, utils, $t, tinyMCE) {
    'use strict';

    return Element.extend({
        defaults: {
        	colTitle: $t('Title'),
        	colCode: $t('Code'),
            listens: {
            	colTitle: 'textHasChanged',
            	colCode: 'textHasChanged'
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
					'colTitle',
					'colCode'
				]);

            return this;
        },
        
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {
        		is_active: this.isActive(),
        		data:{
        			colTitle: this.colTitle(),
        			colCode: this.colCode()
        		}
    		};
        }
    });
});
