/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*global define*/
define(
    [
        'Vnecoms_Credit/js/view/summary/credit'
    ],
    function (Component) {
        "use strict";
        return Component.extend({
            defaults: {
                template: 'Vnecoms_Credit/cart/totals/credit'
            },
            /**
             * @override
             *
             * @returns {boolean}
             */
            isDisplayed: function () {
                return this.getPureValue() != 0;
            }
        });
    }
);
