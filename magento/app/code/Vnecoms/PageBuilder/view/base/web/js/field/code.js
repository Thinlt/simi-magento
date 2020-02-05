define([
    './text',
    'mageUtils',
    'Vnecoms_PageBuilder/codemirror/lib/codemirror',
    'Vnecoms_PageBuilder/codemirror/addon/hint/show-hint',
    'Vnecoms_PageBuilder/codemirror/addon/hint/xml-hint',
    'Vnecoms_PageBuilder/codemirror/addon/hint/html-hint',
    'Vnecoms_PageBuilder/codemirror/mode/xml/xml',
    'Vnecoms_PageBuilder/codemirror/mode/htmlmixed/htmlmixed',
    'Vnecoms_PageBuilder/codemirror/mode/javascript/javascript',
    'Vnecoms_PageBuilder/codemirror/mode/css/css'
], function (Element, utils, CodeMirror) {
    'use strict';

    return Element.extend({
    	defaults: {
    		code: '',
    		theme: 'dracula',
            tracks: {
				code: true,
            },
            htmlEditor: '',
            listens: {
            	code: 'elementChanged',
            }
        },
        /**
         * Init WYSIWYG editor
         */
        initHtmlEditor: function(){
        	var self = this;
        	this.htmlEditor = CodeMirror.fromTextArea(document.getElementById(this.getFieldId()), {
	    		mode: "text/html",
	    		lineNumbers: true,
	    		lineWrapping: false,
	    		styleActiveLine: true,
	    		matchBrackets: true,
	    		theme: this.theme,
	    		extraKeys: {"Ctrl-Space": "autocomplete"},
	    		value: this.code
    		});
        	this.htmlEditor.on("change", function(cm, change) {
    		  self.code = cm.doc.getValue();
    		});
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
