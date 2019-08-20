define(
    [
        'jquery',
        'Magento_Ui/js/modal/modal'
    ],
    function($) {
        "use strict";
        //creating jquery widget
        $.widget('Vnecoms_Credit.modalForm', {
            options: {
                modalForm: '#modal-form',
                modalButton: '#cancel_request'
            },
            _create: function() {
                this.options.modalOption = this._getModalOptions();
                this._bind();
            },
            _getModalOptions: function() {
                /**
                 * Modal options
                 */
                var options = {
                    type: 'popup',
                    responsive: true,
                    title: $.mage.__('Reason Cancel'),
                    buttons: [{
                        text: $.mage.__('Submit'),
                        class: 'action-default scalable save primary ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
                        click: function () {
                            $("#cancel_request_credit").submit();
                            this.closeModal();
                        }
                    }]
                };

                return options;
            },
            _bind: function(){
                var modalOption = this.options.modalOption;
                var modalForm = this.options.modalForm;

                $(document).on('click', this.options.modalButton,  function(){
                    //Initialize modal
                    $(modalForm).modal(modalOption);
                    //open modal
                    $(modalForm).trigger('openModal');
                });
            }
        });

        return $.Vnecoms_Credit.modalForm;
    }
);