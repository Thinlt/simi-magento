define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiLayout',
    'underscore',
    'ko',
    'mageUtils',
    'uiRegistry',
    'Vnecoms_PageBuilder/js/form/element/page-builder',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function ($, layout, _, ko, utils, registry, Element, $t, confirm) {
    'use strict';

    return Element.extend({
    	defaults: {
    		ELEMENT_TYPE_SECTION_HEADER: 	'header_section',
    		ELEMENT_TYPE_SECTION_FOOTER: 	'footer_section',
    		addVariableTmpl: 'Vnecoms_PdfPro/variables',
    		isHeaderSection: false,
    		isFooterSection: false,
    		pdfVariables: {},
    		editingVariable: false,
    		variableFilter: ''
    	},
    	/**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
					'isHeaderSection',
					'isFooterSection',
					'editingVariable',
					'variableFilter'
				]);

            return this;
        },
        
        /**
         * parse Content
         */
        parseContent: function(){
        	if(this.isPasedContent()) return;
        	var self = this;
    		this.isPasedContent(true);
    		var matches = [];
    		var value = this.editor.value();
    		if(!value) this.enablePageBuilder(true);
    		
    		while(true){
    			var result = this.REGEX.exec(value);
    			if(!result) break;
    			matches.push(result)
    		}
    		var sectionSortOrder = 0;
    		/* Enable Page Builder if we found VNECOMS PAGE BUILDER Widget*/
    		if(!matches.size()) return;
    		
    		this.enablePageBuilder(true);
    		matches.each(function(match){
    			try{
    				var data = JSON.parse(match[1]);
    				for(var position in data){
    					var sectionType;
    					switch(position){
    						case 'header':
    							sectionType = self.ELEMENT_TYPE_SECTION_HEADER;
    							break;
    						case 'footer':
    							sectionType = self.ELEMENT_TYPE_SECTION_FOOTER;
    							break;
    						case 'content':
    							sectionType = self.ELEMENT_TYPE_SECTION_LAYOUT;
    					}
    					data[position].each(function(sData){
    						/*Process section data*/
        					if(sData.type){
        						/*Section data from xml*/
        						var sectionData = JSON.parse(JSON.stringify(self.sectionsData[sData.type]));
        						if(sData.elements){
        							for(var elementName in sData.elements){
        								if(!sectionData.fields[elementName]) continue;
        								sectionData.fields[elementName].is_active = sData.elements[elementName].is_active;
        								if(sData.elements[elementName].data){
        									for(var dkey in sData.elements[elementName].data){
        										sectionData.fields[elementName].data[dkey] = sData.elements[elementName].data[dkey];
        									}
        								}
        								if(sData.elements[elementName].fields){
        									if(sectionData.fields[elementName].data.templateItem){
        										var templateFieldId = sectionData.fields[elementName].data.templateItem;
        										var templateField      = sectionData.fields[elementName].fields[templateFieldId];
        									}
        									sectionData.fields[elementName].fields = self.copyData(
    											sData.elements[elementName].fields,
    											sectionData.fields[elementName].fields,
    											templateField
    										);
        								}
        							}
        						}
        						var key = Math.floor(Math.random() * 10000);
        						self.addSectionSortOrder(sectionSortOrder);
        						self.addSection(sectionData, sectionType, key);
        						sectionSortOrder ++;
        					}
        				});
    				}
    				
    			}catch(e){
    				/*Data is not json*/
    				console.log(e);
    			}
    		});
        },
        
    	/**
    	 * has Header sections
    	 */
    	hasHeaderSection: function(){
    		return this.hasSectionsByType(this.ELEMENT_TYPE_SECTION_HEADER);
    	},
    	
    	/**
    	 * has footer sections
    	 */
    	hasFooterSection: function(){
    		return this.hasSectionsByType(this.ELEMENT_TYPE_SECTION_FOOTER);
    	},
    	
    	/**
    	 * has sections by type
    	 */
    	hasSectionsByType: function(sectionType){
    		var sectionCount = 0;
        	var self = this;
        	this.elems().each(function(section){
        		if(section.elementType == sectionType){
        			sectionCount ++;
        		}
        	});
        	return sectionCount;
    	},
    	/**
    	 * Get header sections
    	 */
    	getHeaderSections: function(){
    		return this.getSectionsByType(this.ELEMENT_TYPE_SECTION_HEADER);
    	},
    	
    	/**
    	 * Get footer sections
    	 */
    	getFooterSections: function(){
    		return this.getSectionsByType(this.ELEMENT_TYPE_SECTION_FOOTER);
    	},
    	
    	/**
         * Get sorted sections by type
         */
    	getSectionsByType: function(sectionType){
        	var self = this;
        	var sections = [];
        	this.elems().each(function(elm){
        		if(elm.elementType == sectionType){
        			var insertPosition = 0;
        			/*Find insert position*/
        			for(var insertPosition = 0; insertPosition < sections.size(); insertPosition++){
        				if(elm.sectionSortOrder < sections[insertPosition].sectionSortOrder){
        					break;
        				}
        			}
        			for(var i = sections.size(); i > insertPosition ; i --){
        				sections[i] = sections[i-1];
        			}
        			sections[i] = elm;
        		}
        	});
        	
        	return sections;
        },
        
    	/**
         * Add first section template
         */
        chooseHeaderSectionTemplate: function(){
        	this.isHeaderSection(true);
        	this.chooseSection();
        },
        
        /**
         * Add footer section template
         */
        chooseFooterSectionTemplate: function(){
        	this.isFooterSection(true);
        	this.chooseSection();
        },
        
        getSectionJsonData: function(sections){
        	var data = [];
        	sections.each(function(section){
        		var sectionData = {
    				type: section.id,
    				elements: {}
        		};
        		section.elems().each(function(element){
        			sectionData.elements[element.displayArea] = element.getJsonData();
        		});
        		
        		data.push(sectionData);
        	});
        	
        	return data;
        },
        
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var data = {
	            header: this.getSectionJsonData(this.getHeaderSections()),
	            content: this.getSectionJsonData(this.getSections()),
	            footer: this.getSectionJsonData(this.getFooterSections())
        	};
        	return data;
        },
        
        /**
         * When click to section preview, add the section to the layout
         */
        sectionPreviewClick: function(section){
        	if(!this.isHeaderSection() && !this.isFooterSection()) return this._super();
        	
        	var sectionType =  this.isHeaderSection()?this.ELEMENT_TYPE_SECTION_HEADER:this.ELEMENT_TYPE_SECTION_FOOTER;
        	var sectionData = this.sectionsData[section.id];
        	var key = Math.floor(Math.random() * 10000);        	
        	this.addSection(sectionData, sectionType, key);
        	this.closeSectionModal();
        	
        },
        
        /**
         * Close Section Modal
         */
        closeSectionModal: function(){
        	this.isHeaderSection(false);
        	this.isFooterSection(false);
        	this._super();
        },
        /**
         * Open add variable dialog
         */
        addVariable: function(element){
        	this.editingVariable(element);
        },
        
        /**
         * Can show variable
         */
        canShowVariable: function(variable){
        	var filter = this.variableFilter().trim().toLowerCase();
        	if(!filter) return true;
        	return (variable.title.toLowerCase().indexOf(filter) >= 0);
        },
        
        /**
         * Get variable title
         */
        getVariableTitle: function(variable){
        	var filter = this.variableFilter().trim().toLowerCase();
        	if(!filter) return variable.title;
        	var index = variable.title.toLowerCase().indexOf(filter);
        	var matchedText = variable.title.substr(index, filter.length);
        	return variable.title.replace(
        			matchedText,
    			'<span class=\'matched-text\'>'+matchedText+'</span>'
			);
        },
        /**
         * Insert variable
         */
        insertVariable: function(variable){
        	var element = this.editingVariable();
        	var itemCategories = ['order_item', 'invoice_item', 'shipment_item', 'creditmemo_item'];
        	var newValue = element.code + ((itemCategories.indexOf(variable.category_code)>=0)?'{{var item.'+variable.code+'}}':variable.code);
        	if(element.editor){
        		tinymce.get(element.getFieldId()).setContent(newValue);
        		element.code = newValue;
        	}else{
        		element.code = newValue;
        	}
        	if(element.htmlEditor){
        		element.updateCodeEditorFlag(false);
        		element.htmlEditor.doc.setValue(element.code);
        		element.updateCodeEditorFlag(true);
        	}
        	this.editingVariable(false);
        },
        
        /**
         * Close variables dialog
         */
        closeVariablesDialog: function(){
        	this.editingVariable(false);
        },
        
        /**
         * Update content
         */
        updateContent: function(){
        	var self = this;
        	this._super();
        	this.updateProductThumbnail();
        },
        /**
         * Update product thumbnail
         */
        updateProductThumbnail: function(){
        	var self = this;
        	console.log('Update product thumbnail images');
        	$('#'+this.elementId+'content_container .pdf-items-tbl').find('img').each(function(index, img){
        		$(img).attr('src', self.productThumbnail);
        	});
        },
        test: function(){
        	
        }
    });
});
