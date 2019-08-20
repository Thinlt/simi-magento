define([
    'prototype',
    'mage/adminhtml/wysiwyg/widget'
], function () {

    WysiwygWidget.Widget.prototype.loadOptions = function() {
        /*console.log('WysiwygWidget.Widget.prototype.loadOptions');*/
        if (!this.widgetEl.value) {
            this.switchOptionsContainer();
            return;
        }

        var optionsContainerId = this.getOptionsContainerId();
        if ($(optionsContainerId) != undefined) {
            this.switchOptionsContainer(optionsContainerId);
            return;
        }

        this._showWidgetDescription();

        var params = {widget_type: this.widgetEl.value, values: this.optionValues, editor: this.widgetTargetId};
        new Ajax.Request(this.optionsUrl,
            {
                parameters: {widget: Object.toJSON(params)},
                onSuccess: function(transport) {
                    try {
                        widgetTools.onAjaxSuccess(transport);
                        this.switchOptionsContainer();
                        if ($(optionsContainerId) == undefined) {
                            this.widgetOptionsEl.insert({bottom: widgetTools.getDivHtml(optionsContainerId, transport.responseText)});
                        } else {
                            this.switchOptionsContainer(optionsContainerId);
                        }
                    } catch(e) {
                        alert({
                            content: e.message
                        });
                    }
                }.bind(this)
            }
        );
    }
});
