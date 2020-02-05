define([
    './text',
    'mageUtils',
    "mage/adminhtml/wysiwyg/tiny_mce/setup",
    "mage/adminhtml/wysiwyg/widget"
], function (Element, utils, wysiwyg) {
    'use strict';

    return Element.extend({
    	defaults: {
    		code: '',
            tracks: {
				code: true,
            },
            listens: {
            	code: 'elementChanged',
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
					editor.on('KeyUp', self.onEditorUpdate.bind(self, editor));
					editor.on('Change', self.onEditorUpdate.bind(self, editor));
    			}
      		});
        },
        
        /**
         * Editor on key up
         */
        onEditorUpdate: function(editor, e){
        	this.code = editor.getContent();
        },

        /**
         * Get value of the field
         */
        getValue: function(){
        	return this.code;
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
        			code: this.code
        		}
    		};
        }
    });
});
