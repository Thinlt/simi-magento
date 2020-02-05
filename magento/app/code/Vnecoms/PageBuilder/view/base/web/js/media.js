define([
	'jquery',
	'ko',
	'uiLayout',
    'Magento_Ui/js/form/element/file-uploader',
    'mageUtils',
    'mage/translate',
    'Magento_Ui/js/modal/alert',
    'jquery/file-uploader',
    'jquery/ui'
], function ($, ko, layout, Element, utils, $t, alert) {
    'use strict';

    return Element.extend({
    	defaults: {
    		fieldPrefix: '',
    		TAB_MY_IMAGE:	'tab_myimage',
    		TAB_PEXELS:		'tab_pexels',
    		TAB_WEB_URL:	'tab_weburl',
    		pagebuilder: null,
    		currentTab: 'tab_myimage',
    		previewTmpl: 'Vnecoms_PageBuilder/media/preview',
    		selectedPreviewTmpl: 'Vnecoms_PageBuilder/media/selected-preview',
    		isMultipleFiles: true,
    		dropZone: '.vpb-im-drop-zone',
    		fieldName: 'images',
    		removeUrl: '',
    		downloadUrl: '',
    		validateUrl: '',
    		showingImgsLimit: 27,
    		imagePageSize: 18,
    		sourceImages: [],
    		imageFileNames: [],
    		selectedImage: '',
    		isValidUrl: 0,
    		isCheckingImageUrl: false,
    		pageBuilderProvider: '${ $.parentName }',
    		imports:{
    			'initCurrentMedia': '${ $.pageBuilderProvider}:currentMediaElm' 
    		},
    		imageUrl: '',
    		
    		pexelsAPI: '',
    		pexelAllUrl: '//api.pexels.com/v1/popular',
    		pexelSearchUrl: '//api.pexels.com/v1/search',
    		pexelsSelectedCategory: 'all',
    		pexelsCategories: [],
    		pexelsComponentTemplate: "Vnecoms_PageBuilder/media/pexels",
    		pexelsCurrentPage: 1,
    		pexelsPerPage: 40,
    		pexelsImages: [],
    		pexelsCanShowMoreImages: true,
    		pexelsLoadingImages: false,
    		pexelsSearch: ''
        },
        /**
         * Initialize
         */
        initialize: function () {
        	var self = this;
            this._super();
            this.sourceImages.each(function(image){
            	self.addFile(image);
            });
        },
        
        /**
         * Init current media
         */
        initCurrentMedia: function(){
        	if(!this.getPageBuilder().currentMediaElm()) return;
        	var self = this;
        	var type = this.getPageBuilder().currentMediaElm().imgType();
        	var imgFile = this.getPageBuilder().currentMediaElm().imgFile();
        	switch(type){
        		case this.getPageBuilder().URL_TYPE_MEDIA:
        			this.selectImageByImageFile(imgFile);
        			break;
        		case this.getPageBuilder().URL_TYPE_URL:
        			this.currentTab(this.TAB_WEB_URL);
        			this.imageUrl(imgFile);
        			break;
        	}
        },
        /**
         * Select image by image file
         */
        selectImageByImageFile: function(imageFile){
        	var self = this;
        	this.currentTab(self.TAB_MY_IMAGE);
        	if(this.selectedImage()){
        		this.selectedImage().isSelected(false);
        		this.selectedImage(false);
        	}
  	  		this.getImages().each(function(file){
				if(file.img_file == imageFile){
					self.selectedImage(file);
					file.isSelected(true);
				}
        	});
        },
        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable.
         */
        initObservable: function () {
            this._super()
                .observe([
					'currentTab',
					'showingImgsLimit',
					'selectedImage',
					'isValidUrl',
					'isCheckingImageUrl',
					'imageUrl',
					'pexelsSelectedCategory',
					'pexelsCurrentPage',
					'pexelsImages',
					'pexelsCanShowMoreImages',
					'pexelsLoadingImages',
					'pexelsSearch',
				]);

            return this;
        },
        
        /**
         * Get Field Id
         */
        getFieldId: function(){
        	return this.fieldPrefix+'media';
        },
        
        /**
         * Reset data
         */
        reset: function(){
        	if(this.selectedImage()){
        		this.selectedImage().isSelected(false);
        	}
        	this.selectedImage('');
        	
        	this.imageUrl('');
        	this.isValidUrl(0);
        	this.isCheckingImageUrl(false);
        },
        /**
         * Initializes file uploader plugin on provided input element.
         *
         * @param {HTMLInputElement} fileInput
         * @returns {FileUploader} Chainable.
         */
        initUploader: function (fileInput) {
            this.$fileInput = fileInput;

            _.extend(this.uploaderConfig, {
                dropZone:   $(this.dropZone),
                change:     this.onFilesChoosed.bind(this),
                drop:       this.onFilesChoosed.bind(this),
                add:        this.onBeforeFileUpload.bind(this),
                done:       this.onFileUploaded.bind(this),
                start:      this.onLoadingStart.bind(this),
                stop:       this.onLoadingStop.bind(this)
            });

            $(fileInput).fileupload(this.uploaderConfig);

            return this;
        },
        /**
         * Get page builder
         */
        getPageBuilder: function(){
        	return this.pagebuilder;
        },
        
        /**
         * Get currently editing media element
         */
        getCurrentMediaElm: function(){
        	return this.pagebuilder.currentMediaElm();
        },
        
        /**
         * Set my image tab
         */
        setMyImageTab: function(){
        	this.currentTab(this.TAB_MY_IMAGE);
        },
        
        /**
         * Set my Pexels tab
         */
        setPexelsTab: function(){
        	this.currentTab(this.TAB_PEXELS);
        },

        /**
         * Set web url tab
         */
        setWebUrlTab: function(){
        	this.currentTab(this.TAB_WEB_URL);
        },
        
        /**
         * Is my images tab
         */
        isMyImageTab: function(){
        	return this.currentTab() == this.TAB_MY_IMAGE;
        },
        /**
         * Is Pexels Tab
         */
        isPexelsTab: function(){
        	return this.currentTab() == this.TAB_PEXELS;
        },
        /**
         * Is web url tab
         */
        isWebUrlTab: function(){
        	return this.currentTab() == this.TAB_WEB_URL;
        },
        
        /**
         * Close the media image manager
         */
        close: function(){
        	this.pagebuilder.currentMediaElm(false);
        },
        
        /**
         * Select image
         */
        selectImage: function(image){
        	var isSelectedAlready = image.isSelected();
        	if(this.selectedImage()){
        		this.selectedImage().isSelected(false);
        	}
        	if(isSelectedAlready){
        		/*UnSelect*/
        		this.selectedImage('');
        		return;
        	}
        	
        	image.isSelected(true);
        	this.selectedImage(image);
        },
        
        /**
         * Apply selected image to the editing element 
         * @returns
         */
        applySelectedImage: function(){
        	switch(this.currentTab()){
        		case this.TAB_MY_IMAGE:
        			if(!this.selectedImage()){
                		alert({
                			'title': $t('Error'),
                			'content': $t('Please select an image')
                		});
                		return;
                	}
        			
        			this.updateMediaElement(
    					this.selectedImage().img_type,
    					this.selectedImage().img_file
					);
        			this.reset();
                	this.close();
        			break;
        		case this.TAB_WEB_URL:
        			if(!this.isValidUrl()){
        				alert({
                			'title': $t('Error'),
                			'content': $t('Please enter a valid image URL')
                		});
                		return;
        			}
        			this.updateMediaElement(
    					this.getPageBuilder().URL_TYPE_URL,
    					this.imageUrl()
					);
        			this.reset();
                	this.close();
        			break;
        	}
        },
        
        /**
         * Update the media element
         */
        updateMediaElement: function(type, file){
        	this.getPageBuilder().currentMediaElm().imgType(type);
        	this.getPageBuilder().currentMediaElm().imgFile(file);
        },
        
        /**
         * Get the list of images based on showing limit
         */
        getImages: function(){
        	var count = 0;
        	var limit = this.showingImgsLimit();
        	var result = [];
        	this.value().each(function(file){
        		if(++count > limit) return false;
        		result.push(file);
        	});
        	return result;
        },
        
        /**
         * Can show more image button
         */
        canShowMoreImages: function(){
        	return this.showingImgsLimit() < this.value().size();
        },
        
        /**
         * Get pexels template
         */
        getPexelsTemplate: function(){
        	return this.pexelsComponentTemplate;
        },
        
        /**
         * Returns path to the file's preview template.
         *
         * @returns {String}
         */
        getSelectedPreviewTmpl: function () {
            return this.selectedPreviewTmpl;
        },
        
        /**
         * when the selected preview image is clicked
         */
        selectedPreviewImageClick: function(){
        	return this;
        },
        
        /**
         * Lazy Load Image
         */
        loadImage: function (file, imageUrlField, isLoadingField){
        	if(typeof(imageUrl) != 'string' || !imageUrl){
        		imageUrlField = 'url';
        	}
        	if(typeof(isLoadingField) != 'string' || !isLoadingField){
        		isLoadingField= 'isLoading';
        	}
        	var img = new Image();
            img.onload = function() {
            	file[isLoadingField](false);
            }
            img.src = file[imageUrlField];
        },
        
        /**
         * Bind scroll event to image content
         */
        bindScrollEvent: function(){
        	var self = this;
        	$('#'+this.getFieldId()+' .vpb-im-content').scroll(function(event){
        		if(!self.canShowMoreImages()) return;
        		
        		var height = $('#'+self.getFieldId()+' .vpb-im-content .vpb-im-drop-zone').height() - 20;
        		if($(this).scrollTop() + $(this).height() >= height){
        			self.showMoreImages();
        		}
        	});
        },
        /**
         * Show more images
         */
        showMoreImages: function(){
        	this.showingImgsLimit(this.showingImgsLimit()+ this.imagePageSize);
        },
        /**
         * Handler of the file upload complete event.
         *
         * @param {Event} e
         * @param {Object} data
         */
        onFileUploaded: function (e, data) {
            var file    = data.result,
                error   = file.error;

            error ?
                this.notifyError(error) :
                this.addFile(file, true);
        },
        
        /**
         * Adds provided file to the files list.
         *
         * @param {Object} file
         * @returns {FileUploder} Chainable.
         */
        addFile: function (file, isAddFirst) {
            file = this.processFile(file);
            file.isDeleting = ko.observable(false);
            file.isLoading = ko.observable(true);
            file.isSelected = ko.observable(false);

            this.isMultipleFiles ?
        		(isAddFirst?this.value.unshift(file):this.value.push(file)) :
                this.value([file]);
        	this.imageFileNames.push(file.name);
            return this;
        },
        
        /**
         * Removes provided file from thes files list.
         *
         * @param {Object} file
         * @returns {FileUploader} Chainable.
         */
        removeFile: function (file) {
        	var self = this;
        	file.isDeleting(true);
        	
        	$.ajax({
      		  url: self.removeUrl,
      		  method: "POST",
      		  data: { 
      			  filename: file.name
  			  },
      		  dataType: "json"
	  		}).done(function( response ){
	  	  	  	if(response.ajaxExpired){
	  	  	  	  	window.location = response.ajaxRedirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	if(response.redirect){
	  	  	  	  	window.location = response.redirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	
	  	  	  	if(response.success){
	  	  	  		/*Unset the selected image if it's deleted*/
	  	  	  		if(self.selectedImage().img_file == file.img_file){
	  	  	  			self.selectedImage(false);
	  	  	  		}
	  	  	  		self.imageFileNames.splice(self.imageFileNames.indexOf(file.name), 1);
	  	  	  		self.value.remove(file);
	  	  	  	}else{
	  	  	  		alert({
	  	  	  			title: $t('Error'),
	  	  	  			content: response.error
	  	  	  		});
	  	  	  	}
	  		});
        	
            return this;
        },
        
        /**
         * Validate image URL
         */
        validateImage: function(){
        	var self = this;
        	this.isValidUrl(0);
        	this.isCheckingImageUrl(true);
        	$.ajax({
        		  url: self.validateUrl,
        		  method: "POST",
        		  data: { 
        			  url: self.imageUrl()
    			  },
        		  dataType: "json"
  	  		}).done(function( response ){
  	  			self.isCheckingImageUrl(false);
  	  	  	  	if(response.ajaxExpired){
  	  	  	  	  	window.location = response.ajaxRedirect;
  	  	  	  	  	return;
  	  	  	  	}
  	  	  	  	if(response.redirect){
  	  	  	  	  	window.location = response.redirect;
  	  	  	  	  	return;
  	  	  	  	}
  	  	  	  	
  	  	  	  	if(response.valid){
  	  	  	  		self.isValidUrl(true);
  	  	  	  	}else{
  	  	  	  		self.isValidUrl(false);
  	  	  	  	}
  	  		});
        },
        
        /**
         * Select Pexels category
         */
        selectPexelsCategory: function(category){
        	this.pexelsSelectedCategory(category);
        	this.pexelsCurrentPage(1);
        	this.loadPexelsImages(true);
        	this.pexelsCanShowMoreImages(true);
        },
		/**
		 * Get protocol
		 */
        getProtocol: function(){
			return window.location.protocol != 'https:'?'http:':'https:';
		},
        /**
         * Load PexelsImages
         */
        loadPexelsImages: function(resetImage){
        	this.pexelsLoadingImages(true);
        	var self = this;
        	var url = this.pexelAllUrl;
        	var data = {
    			page: this.pexelsCurrentPage(),
    			per_page: this.pexelsPerPage
			};
        	if(this.pexelsSelectedCategory() != 'all'){
        		url = this.pexelSearchUrl;
        		data['query'] = this.pexelsSelectedCategory();
        	}
        	url = this.getProtocol()+url;
        	$.ajax({
        		url: url,
        		method: "GET",
        		data: data,
        		dataType: 'json',
        		headers: {
        			'Authorization': self.pexelsAPI
        		},
        		error: function (xhr,ajaxOptions,throwError){
        			console.log('Error HERE !');
        		}
        	}).done(function( response ){
        		self.pexelsLoadingImages(false);
        		if(resetImage){
    				self.pexelsImages([]);
    			}
        		if(response['photos'] && response['photos'].size()){
        			response['photos'].each(function(image){
        				self.addPexelsImage(image);
        			});
        		}else{
        			self.pexelsCanShowMoreImages(false);
        		}
        		
        		self.bindTooltipEvent();
        	});
        },
        bindTooltipEvent: function(){
        	$('#'+this.getFieldId()+' .vpb-im-pexels-images .vpb-file-uploader-summary').tooltip({
        	      content: function() {
        	          var element = $( this ).children('.vpb-pexels-image');
        	          return  element?element.html():'';
        	      }
        	});
        },
        /**
         * Show more pexels images
         */
        showMorePexelsImages: function(){
        	this.pexelsCurrentPage(this.pexelsCurrentPage()+1);
        	this.loadPexelsImages(false);
        },
        /**
         * Can show loading
         */
        canShowLoading: function(){
        	return this.pexelsCurrentPage() == 1 && this.pexelsLoadingImages();
        },
        /**
         * Can show load more
         */
        canShowLoadMore: function(){
        	return this.pexelsCurrentPage() != 1 && this.pexelsLoadingImages();
        },
        /**
         * Add a pexels image to the list
         */
        addPexelsImage: function(image){
        	var images = this.pexelsImages();
        	var fileName = image.src.original.split('/');
        	fileName = fileName[fileName.length-1];
        	var downloadStatus = this.imageFileNames.indexOf(fileName) == -1?false:2;
        	var file = {
    			isLoading: ko.observable(true),
    			isLoadingLarge: ko.observable(true),
    			downloadStatus: ko.observable(downloadStatus),
    			width: image.width,
    			height: image.height,
    			url: image.src.small,
    			url_medium: image.src.medium,
    			url_large: image.src.large,
    			url_original: image.src.original,
    			author: image.photographer,
    			source_url: image.url,
    			image_id: image.id,
    			name: fileName
        	};
        	images.push(file);
        	this.pexelsImages(images);
        },

        /**
         * Bind scroll event to image content
         */
        bindPexelsScrollEvent: function(){
        	var self = this;
        	$('#'+this.getFieldId()+' .vpb-im-pexels-images').scroll(function(event){
        		if(self.pexelsLoadingImages()) return;
        		if(!self.pexelsCanShowMoreImages()) return;
        		
        		var height = $('#'+self.getFieldId()+' .vpb-im-pexels-images .vpb-pexels-images').height() - 20;
        		if($(this).scrollTop() + $(this).height() >= height){
        			self.showMorePexelsImages();
        		}
        	});
        },
        /**
         * Pexels searchbox on enter
         */
        pexelsSearchboxKeyPress: function(self, e){
        	if(e.keyCode == 13){
        		this.doPexelsSearch();
        		return false;
        	}
        	return true;
        },
        
        /**
         * Apply pexel image
         */
        downloadPexelImage: function (file) {
        	var self = this;
        	file.downloadStatus(1);
        	$.ajax({
      		  url: self.downloadUrl,
      		  method: "POST",
      		  data: { 
      			  image: file.url_original
  			  },
      		  dataType: "json"
	  		}).done(function( response ){
	  	  	  	if(response.ajaxExpired){
	  	  	  	  	window.location = response.ajaxRedirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	if(response.redirect){
	  	  	  	  	window.location = response.redirect;
	  	  	  	  	return;
	  	  	  	}
	  	  	  	file.downloadStatus(2);
	  	  	  	if(!response.error){
	  	  	  		self.addFile(response, true);
	  	  	  	}else{
	  	  	  		alert({
	  	  	  			title: $t('Error'),
	  	  	  			content: response.error
	  	  	  		});
	  	  	  	}
	  		});
        	
            return this;
        },
        /**
         * Pexels image is click event
         */
        pexelsImageClick: function(file){
        	if(!file.downloadStatus()){
        		this.downloadPexelImage(file);
    		}else if(file.downloadStatus() == 1){
    			alert({
        			'title': $t('Error'),
        			'content': $t('Please wait until the image is downloaded')
        		});
        	}else{
        		console.log('vnecoms_pagebuilder/media/'+file.name);
        		this.selectImageByImageFile('vnecoms_pagebuilder/media/'+file.name);
        	}
        },
        /**
         * Do pexels search
         */
        doPexelsSearch: function(){
        	if(!this.pexelsSearch()) return;
        	this.selectPexelsCategory(this.pexelsSearch());        	
        }
    });
});
