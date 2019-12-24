import React from 'react';
import { callUrlWebview } from 'src/simi/Model/Payment';
import Loading from 'src/simi/BaseComponents/Loading';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify'
import { getOrderInformation } from 'src/selectors/checkoutReceipt';
import isObjectEmpty from 'src/util/isObjectEmpty';
const $ = window.$;

const Webviews = props => {

    const setData = (data) => {
        if (data.errors) {
            if (data.errors.length) {
                data.errors.map(error => {
                    alert(error.message)
                });
                props.history.push('/')
            }
        } else {
            if (data.hasOwnProperty('url') && data.url && Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data') && !isObjectEmpty(Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data'))) {
                const card = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data');
                const form_url = data.url;
                const { params } = data;
                params['card_number'] = card.number.replace(/[ -]/g, "");
                params['card_security_code'] = card.cvc
                params['card_holder_name'] = card.name
                const year = card.exp_year[2] + card.exp_year[3];
                params['expiry_date'] = year.toString() + card.exp_month.toString()
                const formId = 'frm_payfort_fort_payment';
                if ($("#" + formId).is()) {
                    $("#" + formId).remove();
                }
                $('<form id="' + formId + '" action="#" method="POST"></form>').appendTo('body');
                $.each(params, function (k, v) {
                    $('<input>').attr({
                        type: 'hidden',
                        id: k,
                        name: k,
                        value: v
                    }).appendTo($('#' + formId));
                });
                $("#" + formId).attr('action', form_url);
                $("#" + formId).submit();
                sessionStorage.removeItem('payfort_cc_card_data');
                return
            } else if (data.hasOwnProperty('url_action') && data.url_action) {
                window.location.replace(data.url_action);
                return
            } else if (data instanceof Array && data[0].hasOwnProperty('url_action') && data[0].url_action) {
                window.location.replace(data[0].url_action);
                return
            }

        }
    }

    if (props.order) {

        // const token = Identify.findGetParameter('token')
        if (Identify.findGetParameter('paymentFaled')) {
            props.toggleMessages([{ type: 'error', message: Identify.__('Payment Failed'), auto_dismiss: true }])
            props.history.push('/')
        } else {
            const order_information = props.order;
            if (!order_information) {
                props.toggleMessages([{ type: 'error', message: Identify.__('Payment Failed'), auto_dismiss: true }])
                props.history.push('/')
                return;
            }
            callUrlWebview(setData, { order_information, simiSessId: '' })
        }

    }
    return <Loading />
}

const mapDispatchToProps = {
    toggleMessages
};

const mapStateToProps = (state) => ({
    order: getOrderInformation(state)
});

export default connect(mapStateToProps, mapDispatchToProps)(Webviews);
