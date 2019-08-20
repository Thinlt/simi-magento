/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

define(
    [
        'jquery',
        'underscore',
        'Magento_Ui/js/form/element/abstract',
        'Magento_Ui/js/modal/alert',
        'Vnecoms_PdfPro/jquery-owlcarousel/owl.carousel.min'
    ],function($, _, Abstract, uiAlert) {
    return Abstract.extend({
        defaults: {
            links: {

            },
            sliderConfig: {
                dataType: 'json',
            },
            themeData:[]
        },

        /**
         * Initializes component, invokes initialize method of Abstract class.
         *
         *  @returns {Object} Chainable.
         */
        initialize: function () {
            return this._super();
        },

        /**
         * input slider
         * @param input
         * @returns {exports}
         */
        initSlider: function(input) {
            this.$input = input;

            $(input).owlCarousel({
                lazyLoad : true,
                items: 4,
                lazyEffect : "fade",
                lazyFollow : true,
                navigation : true,
                responsive:{
                    0:{
                        items:1
                    },
                    600:{
                        items:3
                    },
                    1000:{
                        items:5
                    }
                }
            });
            return this;
        },

        /**
         * Handler function which is supposed to be invoked when
         * file input element has been rendered.
         *
         * @param {HTMLInputElement} fileInput
         */
        onElementRender: function (input) {
            this.initSlider(input);
        },

        /**
         * Defines initial value of the instance.
         *
         * @returns {FileUploader} Chainable.
         */
        setInitialValue: function () {
            this.initialValue = this.getInitialValue();
            this.value(this.initialValue);

           // console.log(JSON.stringify(this.value));
            return this;
        },

        /**
         * Empties files list.
         *
         * @returns {FileUploader} Chainable.
         */
        clear: function () {
            this.value.removeAll();

            return this;
        },

        /**
         * Checks if files list contains any items.
         *
         * @returns {Boolean}
         */
        hasData: function () {
            return !!this.value().length;
        },

        /**
         * Resets files list to its' initial value.
         *
         * @returns {FileUploader}
         */
        reset: function () {
            var value = this.initialValue.slice();

            this.value(value);

            return this;
        },

        /**
         * Displays provided error message.
         *
         * @param {String} msg
         * @returns {FileUploader} Chainable.
         */
        notifyError: function (msg) {
            uiAlert({
                content: msg
            });

            return this;
        },
    });
});