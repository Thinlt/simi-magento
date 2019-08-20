define([
    'Magento_Ui/js/lib/view/utils/async',
    'uiLayout',
    'underscore',
    'ko',
    'mageUtils',
    'uiRegistry',
    'uiCollection',
    'mage/translate',
    'Magento_Ui/js/modal/confirm'
], function ($, layout, _, ko, utils, registry, Element, $t, confirm) {
    'use strict';

    return Element.extend({
    	defaults: {
    		ELEMENT_TYPE_SECTION_PREVIEW: 	'preview_section',
    		ELEMENT_TYPE_SECTION_LAYOUT: 	'layout_section',
    		ELEMENT_TYPE_MEDIA_MANAGER: 	'media_manager',
    		URL_TYPE_MEDIA: 	'media',
    		URL_TYPE_STATIC: 	'static',
    		URL_TYPE_URL: 		'url',
    		REGEX: /<!--VNECOMS_PAGEBUILDER\s*(.*?)\s*-->/gi,
            elementTmpl: 'Vnecoms_PageBuilder/form/element/wysiwyg',
            selectSectionTmpl: 'Vnecoms_PageBuilder/page-builder/select-section-modal',
            editSectionTmpl: 'Vnecoms_PageBuilder/page-builder/edit-section-modal',
            elementId: 'pb_',
            sectionsData: [],
            fieldsData: [],
            sectionTypes: [],
            defaultSectionType: '',
            listens: {
            	elems: 'updateContent',
            	currentMediaElm: 'currentMediaELementChange'            	
            },
            editor: null, /*Store editor object*/
            enablePageBuilder: false,
            sectionCount: 0,
            sections:[],
            sectionModalVisible: '0',
        	sectionEditVisible: '0',
        	currentSectionTypeId: '',
        	addSectionType: '', /*one of {before, after, none}*/
        	addSectionSortOrder: 0,
        	currentEdittingSection: false,
        	windowHeight: '',
        	isPasedContent: false,
        	mediaComponent: "Vnecoms_PageBuilder/js/media",
        	mediaComponentTemplate: "Vnecoms_PageBuilder/media",
        	currentMediaElm: false,
        	uploadUrl: '',
        	downloadUrl: '',
        	removeUrl: '',
        	validateUrl: '',
        	media: [],
        	baseMediaUrl: '',
        	baseStaticUrl: '',
        	pbResource: [],
        	pexelsCategories: [],
        	pexelsAPI: ''
        },
        /**
         *
         * @returns {} Chainable.
         */
        initialize: function () {
        	var self = this;
            this._super();
            this.initSections();
            this.initMediaManager();
            this.windowHeight(this.getWindowHeight());
            $(window).resize(function() {
            	self.windowHeight(self.getWindowHeight());
            });
            return this;
        },
        
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
					'enablePageBuilder',
					'sectionCount',
					'sections',
					'sectionModalVisible',
					'sectionEditVisible',
					'currentSectionTypeId',
					'addSectionSortOrder',
					'currentEdittingSection',
					'addSectionType',
					'windowHeight',
					'isPasedContent',
					'currentMediaElm'
				]);

            return this;
        },
        
        /**
         * Init the media manager
         */
        initMediaManager: function(){
        	var mediaFieldData = {
    			component: this.mediaComponent,
    			elementType: this.ELEMENT_TYPE_MEDIA_MANAGER,
    			name: "media_manager",
    			displayArea: 'media_manager',
    			parent: this.name,
    			uploaderConfig: {
    				url: this.uploadUrl
    			},
    			removeUrl: 		this.removeUrl,
    			downloadUrl: 	this.downloadUrl,
    			validateUrl: 	this.validateUrl,
    			pagebuilder: 	this,
    			sourceImages: 	this.media,
    			fieldPrefix: 	this.elementId,
    			template: 		this.mediaComponentTemplate,
    			pexelsAPI: 		this.pexelsAPI,
    			pexelsCategories: this.pexelsCategories
        	};
        	this.addElement(mediaFieldData);
        },
        
        /**
         * Get media URL
         */
        getMediaUrl: function(type, file){
        	switch(type){
        		case this.URL_TYPE_MEDIA:
        			return this.baseMediaUrl + file;
        		case this.URL_TYPE_STATIC:
        			return this.baseStaticUrl + file;
        		case this.URL_TYPE_URL:
        			return file;
    			default:
    				return this.baseStaticUrl + file;
        	}
        },
        
        /**
         * Copy field content
         */
        copyData: function(from, to, templateField=[]){
        	var self = this;
        	for (var subFieldID in from){
        		if(!to[subFieldID]){
        			to[subFieldID] = JSON.parse(JSON.stringify(templateField));
        		}
				to[subFieldID]['is_active'] = from[subFieldID]['is_active'];
				if(from[subFieldID].data){
					for(var subFieldDataId in from[subFieldID]['data']){
						to[subFieldID]['data'][subFieldDataId] = from[subFieldID]['data'][subFieldDataId];
					}
				}
				if(from[subFieldID].fields){
					to[subFieldID].fields = self.copyData(
						from[subFieldID].fields,
						to[subFieldID].fields
					);
				}
				
				/*Remove removed Field*/
		        for(var fieldId in to){
		            if(!from[fieldId]){
		                delete(to[fieldId]);
		            }
		        }
			}
        	return to;
        },
        
        /**
         * Get content of editor
         */
        getEditorContent: function(){
        	return this.editor.value();
        },
        /**
         * parse Content
         */
        parseContent: function(){
        	if(this.isPasedContent()) return;
        	var self = this;
    		this.isPasedContent(true);
    		var matches = [];
    		while(true){
    			var result = this.REGEX.exec(this.getEditorContent());
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
    				data.each(function(sData){
    					if(sData.type){
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
    						self.addSection(sectionData, self.ELEMENT_TYPE_SECTION_LAYOUT, key);
    						sectionSortOrder ++;
    					}
    				});
    			}catch(e){
    				/*Data is not json*/
    				console.log(e);
    			}
    		});
        },
        /**
         * Init sections
         */
        initSections: function(){
        	for(var sectionId in this.sectionsData){
        		var sectionData = this.sectionsData[sectionId];
        		this.addSection(sectionData, this.ELEMENT_TYPE_SECTION_PREVIEW);
        	}
        },
        
        /**
         * is having section
         * 
         * @return boolean
         */
        hasSection: function(){
        	var sectionCount = 0;
        	var self = this;
        	this.elems().each(function(section){
        		if(section.elementType == self.ELEMENT_TYPE_SECTION_LAYOUT){
        			sectionCount ++;
        		}
        	});
        	return sectionCount;
        },
        
        /**
         * Apply new sort order
         */
        applyNewSortOrder: function(){
        	var sortedSections = this.getSections();
        	var orderType = this.addSectionType();
        	var orderPosition = this.addSectionSortOrder();
        	
        	var insertPosition = 0;
			/*Find insert position*/
			for(var insertPosition = 0; insertPosition < sortedSections.size(); insertPosition++){
				if(orderPosition <= sortedSections[insertPosition].sectionSortOrder){
					break;
				}
			}
			
        	if(orderType == 'none') {
        		return orderPosition;
    		}else{
    			for(var i = insertPosition; i < sortedSections.size() ; i ++){
    				this.getChild(sortedSections[i].index).sectionSortOrder += 1;
    			}
    		}
        },
        
        /**
         * Add section element.
         */
        addSection: function(sectionData, elementType, uniqueId=false){
        	if(!uniqueId){
        		uniqueId = Math.floor(Math.random() * 10000);
        	}
        	if(elementType == this.ELEMENT_TYPE_SECTION_LAYOUT){
        		this.applyNewSortOrder();
        	}
        	var sectionName = sectionData.id;
        	var elementData = JSON.parse(JSON.stringify(sectionData));
        	elementData['name'] 	= elementType+'.'+sectionName+uniqueId;
        	elementData['parent'] 	= this.name;
        	elementData['sectionType'] = sectionData.type;
        	elementData['sectionSortOrder'] = this.addSectionSortOrder();
        	var sectionId = this.elementId + 'sec'+uniqueId;
        	elementData['sectionId'] = sectionId;
        	elementData['pagebuilder'] = this;
        	elementData['fields'] = sectionData.fields;
        	this.addElement(elementData, elementType);
        },
        
        /**
         * Add Element
         */
        addElement: function(elementData, elementType){
        	elementData.elementType = elementType;
        	layout([elementData]);
        },
        
        /**
         * Choose section
         */
        chooseSection: function(type = 'none', sortOrder = 0){
        	this.addSectionType(type);
        	this.addSectionSortOrder(sortOrder);
        	this.sectionTypeClick(this.defaultSectionType);
        	this.sectionModalVisible(true);
        	
        	if(this.sectionEditVisible() === true) this.closeSectionEdit();
        },
        
        /**
         * Add first section template
         */
        chooseSectionTemplate: function(){
        	this.chooseSection();
        },
        
        /**
         * add a new section before a section
         */
        chooseSectionTemplateBefore: function(section){
        	this.chooseSection('before', section.sectionSortOrder);
        },
        
        /**
         * add a new section after a section
         */
        chooseSectionTemplateAfter: function(section){
        	this.chooseSection('after', section.sectionSortOrder + 1);
        },
        /**
         * Close Section Modal
         */
        closeSectionModal: function(){
        	this.sectionModalVisible(false);
        },
        
        /**
         * Get all section types
         */
        getSectionType: function(){
        	var self = this;
        	var result = [];
        	        	
        	/* The list of section types that have section*/
        	var activeSectionType = [];
        	for(var sectionId in this.sectionsData){
        		var sectionData = this.sectionsData[sectionId];
        		if(activeSectionType.indexOf(sectionData.type) === -1){
        			activeSectionType.push(sectionData.type);
        		}
        	}
        	
        	this.sectionTypes.each(function(sectionType){
        		if(
        			self.pbResource.indexOf(sectionType.resource) >=0 &&
        			activeSectionType.indexOf(sectionType.id) !== -1
				){
        			result.push(sectionType);
        		}
        	});
        	return result;
        },
        
        /**
         * Set current section type
         */
        setCurrentSectionType: function(typeId){
        	this.currentSectionId(typeId);
        },
        
        /**
         * Section Type Click
         * @param type
         */
        sectionTypeClick: function(type){
        	this.currentSectionTypeId(type.id);
        },
        
        /**
         * Get all sections
         */
        getPreviewSections: function(){
        	var sections = [];
        	var self = this;
        	this.elems().each(function(elm){
        		if(
        				elm.elementType == self.ELEMENT_TYPE_SECTION_PREVIEW &&
        				elm.sectionType == self.currentSectionTypeId()
				) {
        			sections.push(elm);
        		}
        	});
        	
        	return sections;
        },
        
        /**
         * When click to section preview, add the section to the layout
         */
        sectionPreviewClick: function(section){
        	var sectionData = this.sectionsData[section.id];
        	var key = Math.floor(Math.random() * 10000);        	
        	this.addSection(sectionData, this.ELEMENT_TYPE_SECTION_LAYOUT, key);
        	this.closeSectionModal();
        },
        
        /**
         * Get sorted sections
         */
        getSections: function(){
        	var self = this;
        	var sections = [];
        	this.elems().each(function(elm){
        		if(elm.elementType == self.ELEMENT_TYPE_SECTION_LAYOUT){
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
         * Delete section
         */
        deleteSection: function(section){
        	var self = this;
        	confirm({
    			modalClass: 'confirm vpb-confirm',
    			title: $t('Delete this section?'),
    			content: $t('This section will be deleted completly.'),
    			actions:{
    				confirm: function(){
    					self.removeChild(section);
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
        },
        
        /**
         * Move section up
         */
        moveSectionUp: function(section){
        	var sections = this.getSections();
        	for(var i = 0; i < sections.size(); i ++){
        		if(sections[i].index == section.index){
        			if(!sections[i-1]) return;
        			var tmpOrder = section.sectionSortOrder;
        			this.getChild(section.index).sectionSortOrder = sections[i-1].sectionSortOrder;
        			this.getChild(sections[i-1].index).sectionSortOrder = tmpOrder;
        			break;
        		}
        	}
        	this._updateCollection();
        },
        
        /**
         * Move section down
         */
        moveSectionDown: function(section){
        	var sections = this.getSections();
        	for(var i = 0; i < sections.size(); i ++){
        		if(sections[i].index == section.index){
        			if(!sections[i+1]) return;
        			var tmpOrder = section.sectionSortOrder;
        			this.getChild(section.index).sectionSortOrder = sections[i+1].sectionSortOrder;
        			this.getChild(sections[i+1].index).sectionSortOrder = tmpOrder;
        			break;
        		}
        	}
        	this._updateCollection();
        },
        
        /**
         * Open edit section window
         */
        editSection: function(section){
        	this.sectionEditVisible(true);
        	if(this.sectionModalVisible() === true) this.closeSectionModal();

        	/*Active the wysiwyg editor*/
        	var elements = this.getSection(section.index).elems();
        	elements.each(function(element){
        		if(!element.hasEditor) return true;
        		
        		if(element.editor){
        			tinymce.get(element.getFieldId()).remove();
        		}
        		element.initEditor();
        	});
        	this.currentEdittingSection(section);
        },
        
        /**
         * Get section
         */
        getSection: function(sectionIndex){
        	return this.getChild(sectionIndex);
        },
        
        /**
         * Close Section Modal
         */
        closeSectionEdit: function(){
        	this.sectionEditVisible(false);
        	this.currentEdittingSection(false);
        },
        
        /**
         * Update content of
         */
        updateContent: function(){
        	var self = this;
        	/*Delay 1 seconds to wait for all elements is created*/
        	setTimeout(function(){
        		if(self.editor){
        			self.editor.value('<!--VNECOMS_PAGEBUILDER '+ JSON.stringify(self.getJsonData()) +'-->');
            	}
        	},500);
        },
        
        /**
         * Get object data to store to DB
         */
        getJsonData: function(){
        	var data = [];
        	this.getSections().each(function(section){
        		data.push(section.getJsonData());
        	});
        	
        	return data;
        },
        currentMediaELementChange: function(){
        	if(this.currentMediaElm()){
        		$('body').addClass('vpb-im-modal-opening');
        	}else{
        		$('body').removeClass('vpb-im-modal-opening');
        	}
        },
        /**
         * Get Window Height
         */
        getWindowHeight: function(){
        	return ($(window).height() - 150);
        }
    });
});
