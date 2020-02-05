define([
    './abstract',
    'mageUtils',
    'mage/translate',
    "mage/adminhtml/wysiwyg/tiny_mce/setup",
    "mage/adminhtml/wysiwyg/widget"
], function (Element, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
            editor: '',
            text: '',
            href: '',
            tracks: {
				text: true,
				href: true,
            },
            linkType: '',
            linkData: '',
            openNewWindow: false,
            listens: {
            	text: 'elementChanged',
            	linkType: 'elementChanged',
            	linkData: 'elementChanged',
            	openNewWindow: 'elementChanged'
            }
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe(['linkType', 'linkData', 'openNewWindow']);
            this.linkData(this.href);
            if(!this.linkType()) this.linkType('none');
            return this;
        },
        
        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.text;
        },
        
        
        /**
         * Get Href
         */
        getHref: function(){
        	var href = this.linkData();
        	if(this.linkType() == 'none'){
        		href = '';
        	}
        	
        	return href;
        },
        
        linkClick: function(){
        	/* Do nothing*/
        	return false;
        },
        
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {
        		is_active: this.isActive(),
        		data:{
        			text: this.text,
        			linkType: this.linkType(),
        			href: this.getHref(),
        			openNewWindow: this.openNewWindow()
        		}
    		};
        }
    });
});
