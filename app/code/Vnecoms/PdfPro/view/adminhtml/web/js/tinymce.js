define([
    'prototype',
    'mage/adminhtml/wysiwyg/tiny_mce/setup'
], function () {
    tinyMceWysiwygSetup.prototype.body = '';

    tinyMceWysiwygSetup.prototype.customWidgetPlaceholderExist = function(filename) {
        return this.config.ves_widget_placeholders.indexOf(filename) != -1;
    }

    tinyMceWysiwygSetup.prototype.decodeWidgets = function(content) {
        return content.gsub(/<div class="widget"><img([^>]+id=\"[^>]+)><\/div>/i, function(match) {
            var attributes = this.parseAttributesString(match[1]);
            if(attributes.id) {
                var widgetCode = Base64.idDecode(attributes.id);
                if (widgetCode.indexOf('{{widget') != -1) {
                    return widgetCode;
                }
                return match[0];
            }
            return match[0];
        }.bind(this));
    };

    tinyMceWysiwygSetup.prototype.encodeDirectives =  function(content) {
        return content.gsub(/<([a-z0-9\-\_]+.+?)([a-z0-9\-\_]+=".*?\{\{.+?\}\}.*?".+?)>/i, function(match) {
            var attributesString = match[2];
            /* process tag attributes string
            string not start with var .Ex {{var order.xxx}} is not matching
            string in {{#string}} start with media */
            attributesString = attributesString.gsub(/([a-z0-9\-\_]+)="(.*?)(\{\{media.*\}\})(.*?)"/i, function(m) {
                return m[1] + '="' + m[2] + this.makeDirectiveUrl(Base64.mageEncode(m[3])) + m[4] + '"';
            }.bind(this));
            return '<' + match[1] + attributesString + '>';

        }.bind(this));
    }

    tinyMceWysiwygSetup.prototype.encodeWidgets = function(content) {
        return content.gsub(/\{\{widget(.*?)\}\}/i, function(match){

            var placeholderFilename = this.config.custom_image_filename;
            if (!this.customWidgetPlaceholderExist(placeholderFilename))
            {
                placeholderFilename = 'default.gif';
            }
            if(this.customWidgetPlaceholderExist(placeholderFilename))
            {
                var imageSrc = this.config.ves_widget_images_url + placeholderFilename;
            }
            else
            {
                var imageSrc = this.config.widget_images_url + placeholderFilename;
            }

            var imageHtml = '<div class="widget"><img';
            imageHtml+= ' id="' + Base64.idEncode(match[0]) + '"';
            imageHtml+= ' src="' + imageSrc + '"';
            imageHtml+= ' title="' + match[0].replace(/\{\{/g, '{').replace(/\}\}/g, '}').replace(/\"/g, '&quot;') + '"';
            imageHtml+= '></div>';

            return imageHtml;

        }.bind(this));
    }

    tinyMceWysiwygSetup.prototype.setBodyClass = function(body) {
        this.body = body;
    }

    tinyMceWysiwygSetup.prototype.getSettings = function(mode) {
        var plugins = 'inlinepopups,safari,pagebreak,style,layer,table,advhr,advimage,emotions,iespell,media,searchreplace,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras';

        if (this.config.widget_plugin_src) {
            plugins = 'magentowidget,' + plugins;
        }

        var magentoPluginsOptions = $H({});
        var magentoPlugins = '';

        if (this.config.plugins) {
            this.config.plugins.each(function(plugin) {
                magentoPlugins = plugin.name + ',' + magentoPlugins;
                magentoPluginsOptions.set(plugin.name, plugin.options);
            });
            if (magentoPlugins) {
                plugins = '-' + magentoPlugins + plugins;
            }
        }


        var settings = {
            mode: (mode != undefined ? mode : 'none'),
            elements: this.id,
            body_class:this.body,
            theme: 'advanced',
            plugins: plugins,
            theme_advanced_buttons1: magentoPlugins + 'magentowidget,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect',
            theme_advanced_buttons2: 'cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,forecolor,backcolor',
            theme_advanced_buttons3: 'tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,iespell,advhr,|,ltr,rtl,|,fullscreen',
            theme_advanced_buttons4: 'moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,pagebreak',
            theme_advanced_toolbar_location: 'top',
            theme_advanced_toolbar_align: 'left',
            theme_advanced_statusbar_location: 'bottom',
            theme_advanced_resizing: true,
            theme_advanced_resize_horizontal: false,
            convert_urls: false,
            relative_urls: false,
            content_css: this.config.content_css,
            custom_popup_css: this.config.popup_css,
            magentowidget_url: this.config.widget_window_url,
            magentoPluginsOptions: magentoPluginsOptions,
            doctype: '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">',
            setup: function(ed){
                ed.onSubmit.add(function(ed, e) {
                    varienGlobalEvents.fireEvent('tinymceSubmit', e);
                });

                ed.onPaste.add(function(ed, e, o) {
                    varienGlobalEvents.fireEvent('tinymcePaste', o);
                });

                ed.onBeforeSetContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceBeforeSetContent', o);
                });

                ed.onSetContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceSetContent', o);
                });

                ed.onSaveContent.add(function(ed, o) {
                    varienGlobalEvents.fireEvent('tinymceSaveContent', o);
                });

                var onChange = function(ed, l) {
                    varienGlobalEvents.fireEvent('tinymceChange', l);
                };

                ed.onChange.add(onChange);
                ed.onKeyUp.add(onChange);

                ed.onExecCommand.add(function(ed, cmd, ui, val) {
                    varienGlobalEvents.fireEvent('tinymceExecCommand', cmd);
                });
            }
        };

        /* Set the document base URL */
        if (this.config.document_base_url) {
            settings.document_base_url = this.config.document_base_url;
        }

        if (this.config.files_browser_window_url) {
            settings.file_browser_callback = function(fieldName, url, objectType, w) {
                varienGlobalEvents.fireEvent("open_browser_callback", {
                    win: w,
                    type: objectType,
                    field: fieldName
                });
            };
        }

        if (this.config.width) {
            settings.width = this.config.width;
        }

        if (this.config.height) {
            settings.height = this.config.height;
        }

        if (this.config.settings) {
            Object.extend(settings, this.config.settings)
        }

        return settings;
    };

});