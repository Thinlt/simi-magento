/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
define([
    'underscore',
    'Magento_Ui/js/grid/columns/column'
], function (_, Column) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'ui/grid/cells/html'
        },
        /*eslint-disable eqeqeq*/
        /**
         * Retrieves label associated with a provided value.
         *
         * @returns {String}
         */
        getLabel: function (record) {
            var value = record[this.index];
            var statusClass = ['vendor-status'];
            if (value == 0) {
                statusClass.push('vendor-status-disabled');
            } else if (value == 1) {
                statusClass.push('vendor-status-pending');
            } else if (value == 2) {
                statusClass.push('vendor-status-approved');
            } else if (value == 3) {
                statusClass.push('vendor-status-disabled');
            }
            
            var options = this.options || [],
                values = this._super(),
                label = [];

            if (!Array.isArray(values)) {
                values = [values];
            }

            values = values.map(function (value) {
                return value + '';
            });

            options.forEach(function (item) {
                if (_.contains(values, item.value + '')) {
                    label.push(item.label);
                }
            });

            return '<div class="' + statusClass.join(" ") + '">' + label.join(', ') + '</div>';
        }

        /*eslint-enable eqeqeq*/
    });
});
