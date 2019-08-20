define([
    './html',
    'mageUtils',
    'mage/translate',
    'Vnecoms_PageBuilder/codemirror/lib/codemirror',
    'Vnecoms_PageBuilder/codemirror/addon/hint/show-hint',
    'Vnecoms_PageBuilder/codemirror/addon/hint/xml-hint',
    'Vnecoms_PageBuilder/codemirror/addon/hint/html-hint',
    'Vnecoms_PageBuilder/codemirror/mode/xml/xml',
    'Vnecoms_PageBuilder/codemirror/mode/htmlmixed/htmlmixed',
    'Vnecoms_PageBuilder/codemirror/mode/javascript/javascript',
    'Vnecoms_PageBuilder/codemirror/mode/css/css',
    "mage/adminhtml/events"
], function (Element, utils, $t, CodeMirror) {
    'use strict';

    return Element.extend({
    	defaults: {
    		theme: 'dracula',
            htmlEditor: '',
            isCodeEditor: '',
            updateCodeEditorFlag: true
        },
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
					'isCodeEditor',
					'updateCodeEditorFlag'
				]);

            return this;
        },
        /**
         * Init Editor
         */
        initEditor: function(){
        	this._super();
        	this.initHtmlEditor();
        },
        
        /**
         * Editor on Key Up
         */
        onEditorUpdate: function(editor, e){
        	this._super();
        	
        	this.updateCodeEditorFlag(false);
        	if(this.htmlEditor){
        		this.htmlEditor.doc.setValue(this.code);
        	}
        	this.updateCodeEditorFlag(true);
        },
        
        /**
         * Init html editor
         */
        initHtmlEditor: function(){
        	var self = this;
        	this.htmlEditor = CodeMirror(document.getElementById(this.getFieldId()+'_codeEditor'), {
	    		mode: "text/html",
	    		lineNumbers: true,
	    		lineWrapping: true,
	    		styleActiveLine: true,
	    		matchBrackets: true,
	    		theme: this.theme,
	    		extraKeys: {"Ctrl-Space": "autocomplete"},
	    		value: this.code
    		});
        	this.htmlEditor.on("update", function(cm) {
        		if(!self.updateCodeEditorFlag()) return;
        		var content = cm.doc.getValue();
        		tinymce.get(self.getFieldId()).setContent(content);
        		self.code = content;
    		});
        },

        /**
         * Toggle editor
         */
        toggleEditor: function(){
        	this.isCodeEditor(!this.isCodeEditor());
        },
        /**
         * Get toggle button label
         */
        getButtonLabel: function(){
        	return $t('Show / Hide Editor');
        }
    });
});
