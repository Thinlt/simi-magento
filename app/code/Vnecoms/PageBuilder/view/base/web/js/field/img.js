define([
    './abstract',
    'mageUtils',
    'mage/translate'
], function (Element, utils, $t) {
    'use strict';

    return Element.extend({
        defaults: {
        	imgType: '',
        	imgFile: '',
            tracks: {
            	src: true,
            },
            listens: {
            	imgType: 'elementChanged',
            	imgFile: 'elementChanged'
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
					'imgType',
					'imgFile',
				]);

            return this;
        },
        
        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.getUrl();
        },
        
        /**
         * Get image URL
         */
        getImageUrl: function(){
        	return this.getPageBuilder().getMediaUrl(this.imgType(), this.imgFile());
        },
        
        replaceImage: function(){
        	this.getPageBuilder().currentMediaElm(this);
        },

        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	return {
        		/*type: this.id,
        		position: this.displayArea,*/
        		is_active: this.isActive(),
        		data:{
        			imgType: this.imgType(),
        			imgFile: this.imgFile()
        		}
    		};
        }
    });
});
