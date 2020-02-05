/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

tinyMCE.addI18n({en:{
    magentovariable:
    {
        insert_variable : "Insert Variable"
    }
}});

(function() {
    tinymce.create('tinymce.plugins.EasyPdfvariablePlugin', {
        /**
         * @param {tinymce.Editor} ed Editor instance that the plugin is initialized in.
         * @param {string} url Absolute URL to where the plugin is located.
         */
        init : function(ed, url) {
            ed.addCommand('mceMagentovariable', function() {
                var pluginSettings = ed.settings.magentoPluginsOptions.get('magentovariable');
                EasyPdfvariablePlugin.setEditor(ed);
                EasyPdfvariablePlugin.loadChooser(pluginSettings.url, null);
            });

            /* Register Widget plugin button */
            ed.addButton('magentovariable', {
                title : 'magentovariable.insert_variable',
                cmd : 'mceMagentovariable',
                image : url + '/img/icon.gif'
            });
        },

        getInfo : function() {
            return {
                longname : 'Pdf Variable Manager Plugin for TinyMCE 3.x',
                author : 'Ves Team',
                authorurl : 'http://www.vnecoms.com',
                infourl : 'http://www.vnecoms.com',
                version : "2.0"
            };
        }
    });

    /* Register plugin */
    tinymce.PluginManager.add('magentovariable', tinymce.plugins.EasyPdfvariablePlugin);
})();
