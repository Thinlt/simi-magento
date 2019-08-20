/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'Magento_Ui/js/grid/columns/column'
], function (Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Simi_VendorMapping/ui/giftcard/grid/columns/cells/product/templates'
        },

        /**
         * Retrieve template names
         *
         * @returns {Array}
         */
        getTemplateNames: function(row) {
            return row[this.index + '_names'];
        }
    });
});
