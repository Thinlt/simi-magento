define([
    './abstract',
    'mageUtils',
    'mage/translate',
    "wysiwygAdapter",
    "mage/adminhtml/wysiwyg/widget"
], function (Element, utils, $t, wysiwyg) {
    'use strict';

    return Element.extend({
        defaults: {
            editor: '',
            text: '',
            hasEditor: true,
            tracks: {
				text: true,
            },
            listens: {
            	text: 'elementChanged',
            }
        },
        
        /**
         * Init WYSIWYG editor
         */
        initEditor: function(){
        	var self = this;
        	this.editor = tinymce.init({
      		  	selector: '#'+this.getFieldId(),
      		  	height: 200,
      		  	menubar: false,
      		  	plugins: [
	      		    'advlist autolink lists link image charmap print preview anchor textcolor',
	      		    'searchreplace visualblocks code fullscreen',
	      		    'insertdatetime media table paste code help wordcount'
      		    ],
      		    toolbar: 'undo redo | formatselect | bold italic forecolor | alignleft aligncenter alignright alignjustify | removeformat',
      			init_instance_callback: function (editor) {
					editor.on('KeyUp', function (e) {
						self.text = editor.getContent();
					});
      			}
      		});
        },

        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.text;
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
        			text: this.text
        		}
    		};
        }
    });
});
