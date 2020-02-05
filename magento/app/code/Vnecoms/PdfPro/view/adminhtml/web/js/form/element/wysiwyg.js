/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['Magento_Ui/js/form/element/wysiwyg', 'mage/adminhtml/events', 'tinymce'],function(Abstract, _events, tinyMCE) {
    return Abstract.extend({

        /**
         * Initializes component, invokes initialize method of Abstract class.
         *
         *  @returns {Object} Chainable.
         */
        initialize: function () {
            this._super();

        },
    });
});