/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*jshint browser:true jquery:true*/
define([
    "jquery",
    "jquery/ui"
], function($){
    "use strict";
    
    $.widget('mage.vnecomsCartCredit', {
        options: {
        },
        _create: function () {
            this.creditAmount = $(this.options.creditAmountSelector);
            this.removeCredit = $(this.options.removeCreditSelector);

            $(this.options.applyButton).on('click', $.proxy(function () {
                this.creditAmount.attr('data-validate', '{required:true}');
                this.removeCredit.attr('value', '0');
                $(this.element).validation().submit();
            }, this));

            $(this.options.cancelButton).on('click', $.proxy(function () {
                this.creditAmount.removeAttr('data-validate');
                this.removeCredit.attr('value', '1');
                this.element.submit();
            }, this));
        }
    });

    return $.mage.vnecomsCartCredit;
});