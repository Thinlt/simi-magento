/**
 * Copyright 2019 SimiCart. All rights reserved.
 * See LICENSE.txt for license details.
 */

define([
    'uiCollection',
    'uiRegistry'
], function (Collection, registry) {
    'use strict';

    return Collection.extend({
        defaults: {
            template: 'Simi_VendorMapping/ui/giftcard/grid/import',
            displayArea: 'dataGridActions'
        },

        /**
         * Apply action on target component
         */
        applyAction: function () {
            var targetName = this.action.targetName,
                params = this.action.params || [],
                actionName = this.action.actionName,
                target;

            if (registry.has(targetName)) {
                target = registry.async(targetName);

                if (target && typeof target === 'function' && actionName) {
                    params.unshift(actionName);
                    target.apply(target, params);
                }
            }
        }
    });
});
