/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define(['Magento_Ui/js/form/element/textarea'],function(Abstract) {
    return Abstract.extend({
        defaults: {
            elementTmpl: 'Vnecoms_PdfPro/form/element/test',
            value1: 'valu1',
            templates1: '${ $.provider }:data.templates',
            links: {
                templates1: '${ $.provider }:data.templates'
            },
            hiep: {
                test: 'test',
            }
        },

        initialize: function() {

            this._super();
           // console.log(this.items);

            console.log(this.hiep);
        }

    });
});