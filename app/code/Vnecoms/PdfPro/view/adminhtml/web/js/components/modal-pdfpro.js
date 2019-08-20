/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'Magento_Ui/js/modal/modal-component',
    'underscore'
], function (Modal, _) {
    'use strict';

    return Modal.extend({
        defaults :{
            links:{
                title: 'Add theme',
            }
        },

        actionDone: function () {
            alert(1);
        }
    });
});
