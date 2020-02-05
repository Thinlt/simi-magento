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
            var statusClass = ['label'];
            if (value == 0) {
                statusClass.push('label-default');
                statusClass.push('withdrawal-status-canceled');
            } else if (value == 1) {
                statusClass.push('label-warning');
                statusClass.push('withdrawal-status-pending');
            } else if (value == 2) {
                statusClass.push('label-success');
                statusClass.push('withdrawal-status-completed');
            } else if (value == 3) {
                statusClass.push('label-danger');
                statusClass.push('withdrawal-status-rejected');
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
