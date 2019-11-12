define([
    'jquery',
    'mage/template',
    'jquery/ui',
], function($, mageTemplate) {

    $.widget('simi.regionUpdater', {
        options: {
            regionJson: {},
            regionTemplate: '<option value="<%- data.value %>" title="<%- data.title %>" <% if (data.isSelected) { %>selected="selected"<% } %>>' +
            '<%- data.title %>' +
            '</option>',
            currentRegion: null,
            regionListId: '',
            regionInputId: ''
        },

        _create: function () {
            var options = this.options;
            this._initCountryElement();

            this.currentRegionOption = options.currentRegion;
            this.regionTmpl = mageTemplate(options.regionTemplate);

            this._updateRegion(this.element.find('option:selected').val());

            $(options.regionListId).on('change', $.proxy(function (e) {
                this.setOption = false;
                this.currentRegionOption = $(e.target).val();
            }, this));

            $(options.regionInputId).on('focusout', $.proxy(function () {
                this.setOption = true;
            }, this));
        },

        /**
         * init country element
         * @private
         */
        _initCountryElement: function() {
            this.element.on('change', $.proxy(function (e) {
                this._updateRegion($(e.target).val());
            }, this));
        },

        /**
         * Remove options from dropdown list
         * @param {Object} selectElement - jQuery object for dropdown list
         * @private
         */
        _removeSelectOptions: function (selectElement) {
            selectElement.find('option').each(function (index) {
                if (index) {
                    $(this).remove();
                }
            });
        },

        /**
         * Render dropdown list
         * @param {Object} selectElement - jQuery object for dropdown list
         * @param {String} key - region code
         * @param {Object} value - region object
         * @private
         */
        _renderSelectOption: function (selectElement, key, value) {
            selectElement.append($.proxy(function () {
                var name = value.name.replace(/[!"#$%&'()*+,.\/:;<=>?@[\\\]^`{|}~]/g, '\\$&'),
                    tmplData,
                    tmpl;

                if (value.code && $(name).is('span')) {
                    key = value.code;
                    value.name = $(name).text();
                }

                tmplData = {
                    value: key,
                    title: value.name,
                    isSelected: false
                };

                if (this.options.defaultRegion === key) {
                    tmplData.isSelected = true;
                }

                tmpl = this.regionTmpl({
                    data: tmplData
                });

                return $(tmpl);
            }, this));
        },

        /**
         * Update dropdown list based on the country selected
         * @param {String} country - 2 uppercase letter for country code
         * @private
         */
        _updateRegion: function (country) {
            // Clear validation error messages
            var options = this.options,
                regionList = $(options.regionListId),
                regionInput = $(options.regionInputId);

            // Populate state/province dropdown list if available or use input box
            if (options.regionJson[country]) {
                this._removeSelectOptions(regionList);
                $.each(options.regionJson[country], $.proxy(function (key, value) {
                    this._renderSelectOption(regionList, key, value);
                }, this));

                if (this.currentRegionOption) {
                    regionList.val(this.currentRegionOption);
                }

                if (this.setOption) {
                    regionList.find('option').filter(function () {
                        return this.text === regionInput.val();
                    }).prop('selected', true);
                }

                if(regionList.find('option:selected').length == 0) {
                    regionList.find('option').first().prop('selected', true);
                }

                regionList.prop('disabled', false).show();
                regionInput.prop('disabled', true).hide();
            } else {
                regionList.prop('disabled', true).hide();
                regionInput.prop('disabled', false).show();
            }

            // Add defaultvalue attribute to state/province select element
            regionList.attr('defaultvalue', this.options.defaultRegion);
        },

    });

    return $.simi.regionUpdater;
});
