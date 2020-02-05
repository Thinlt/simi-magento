
define([
    'jquery',
    'underscore',
    'mage/template',
    'tinymce',
    'Magento_Ui/js/modal/alert',
    'mage/translate',
], function ($, _, mageTemplate, tinyMCE, alert) {
    'use strict';

    $.widget('vnecoms.easypdf', {
        /**
         * Creates widget
         * @private
        */
        _create: function () {
            this.init();

           /*Bind events*/
            this._bindEvents();
        },

        init:function() {
            var options = this.options;
            var currentTemplate = options.currentTemplate;
            var templateItems = $(this.options.templateItems);

            var loaderContainer = $('#checkout-loader');

            if (loaderContainer) {
                loaderContainer.hide();
            }
        },

        /**
         * Bind events
         */
        _bindEvents: function(){
            var options = this.options;
            var valueField = $(this.options.valueFieldSelector);
            var templateItems = $(this.options.templateItems);
            $(templateItems).on('click', this, this._updateTemplate);
        },

        /**
         * update Template When click image template Items
         * @private
         */
        _updateTemplate: function() {

            var loaderContainer = $('#checkout-loader');

            var options = $('#base_template_wrapper').easypdf('option');
            /*console.log(options);*/
            var previewImagesJson = JSON.parse(options.previewImagesJson);
            $(options.templateItems).removeClass('active');
            $(this).addClass('active');
            $(this).parent().children('.egcSwatch-arrow').css('display','block');

            var currentChoosenTemplateId = $(this).attr('template-id');
            var currentTemplateId = options.currentTemplateId;
            var hiddenTemplateElement = $(options.hiddenTemplateElement);
           
            if(currentChoosenTemplateId == hiddenTemplateElement.val()) return;
            
            hiddenTemplateElement.val(currentChoosenTemplateId);
            var valueTemplate = $(hiddenTemplateElement).val();

            $('body').trigger('processStart');
           
            if($('.no-image')) {
                /* the first chosen, remove no-image div and append new div */
                $('.no-image').hide();
                $('#img-preview-image').attr('src',previewImagesJson[hiddenTemplateElement.val()]['image'])
                    .removeClass('hidden');
               
                $('.preview-image-label')
                    .text(previewImagesJson[hiddenTemplateElement.val()]['label'])
                    .removeClass('hidden');
            }
            else {
                $(options.previewImageId).attr('src', previewImagesJson[hiddenTemplateElement.val()]['image']);
                $('.preview-image-label').html(previewImagesJson[hiddenTemplateElement.val()]['label']);
            }
            $.ajax({
                type: "POST",
                data: {form_key:window.FORM_KEY,id:valueTemplate},
                url: options.ajaxUrl,
                beforeSend: function () {
                    $('body').trigger('processStart');
                },
                complete: function() {
                    $('body').trigger('processStop');
                },
                success: function(result, status) {
                    $('body').trigger('processStop');
                    
                    var templateJSON = result;
                    $(options.editorIds).each(function (index,elm) {
                        
                        if(index < 0) {
                            (tinyMCE.get(elm) === undefined) ? $(elm).val('') : tinyMCE.get(elm).setContent('');
                        } else {
                            (tinyMCE.get(elm) === undefined) ? $(elm).val(templateJSON[elm]) : tinyMCE.get(elm).setContent(templateJSON[elm]);
                        }

                        var cssUrls = new Array();
                        var editor = window['wysiwyg'+elm];
                        editor.config.content_css.split(',').each(function(cssUrl){
                            if(cssUrl.indexOf('templates/default.css') != -1){
                                cssUrls.push(cssUrl);
                            }
                        });

                        if(templateJSON.id){
                            cssUrls.push(templateJSON.css_url);
                        }

                        editor.config.content_css = cssUrls.join(',');
                        if(templateJSON.id){
                            editor.setBodyClass(templateJSON.sku);
                        }

                        if(tinyMCE.get(elm)){
                            editor.turnOff();
                            editor.turnOn();
                        }
                    });

                },
                error: function () {
                    $('body').trigger('processStop');
                    alert({
                        content: 'An error occurred'
                    });
                },
                dataType: "json"
            });

        },

        /**
         * Change template dropdown.
         */
        _changeTemplateDropdown: function(){
            var options = $(this).easypdf('option');

            /* check if choosen id == current template, nothing to do */
            var currentTemplateId = options.currentTemplateId;
            var hiddenTemplateElement = $(options.hiddenTemplateElement);

            var valueTemplate = $(hiddenTemplateElement).value;

            if(valueTemplate == currentTemplateId || currentTemplateId == null) {
                alert({
                    content: $.mage.__('You need choose a template before edit.')
                });
                return;
            }

            var templateJSON = JSON.parse(options.templateJSON);console.log(templateJSON[valueTemplate]);
            $(options.editorIds).each(function (index,elm) {
                if(index == '') {
                    (tinyMCE.get(elm) === undefined) ? $(elm).value = '' : tinyMCE.get(elm).setContent('');
                } else {
                    (tinyMCE.get(elm) === undefined) ? $(elm).value =
                    templateJSON[valueTemplate][elm] : tinyMCE.get(elm).setContent(templateJSON[valueTemplate][elm]);
                }

                var cssUrls = new Array();
                var editor = window['wysiwyg'+elm];
                editor.config.content_css.split(',').each(function(cssUrl){
                    if(cssUrl.indexOf('templates/default.css') != -1){
                        cssUrls.push(cssUrl);
                    }
                });

                if(valueTemplate){
                    cssUrls.push(templateJSON[valueTemplate].css_url);
                }

                editor.config.content_css = cssUrls.join(',');
                
                if(valueTemplate){
                    editor.setBodyClass(templateJSON[valueTemplate].sku);
                }

                if(tinyMCE.get(elm)){
                    editor.turnOff();
                    editor.turnOn();
                }
            });
        }
    });

    return $.vnecoms.easypdf;
});
