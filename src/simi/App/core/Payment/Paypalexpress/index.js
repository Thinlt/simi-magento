import React from 'react';
import {paypalExpressStart, paypalPlaceOrder} from 'src/simi/Model/Payment';
import Loading from 'src/simi/BaseComponents/Loading';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify'

const PaypalExpress = props => {
    const setData = (data) => {
        if (data.errors) {
            if (data.errors.length) {
                data.errors.map(error => {
                    alert(error.message)
                });
            }
        } else {
            if (data.ppexpressapi &&
                data.ppexpressapi.url) {
                window.location.replace(data.ppexpressapi.url);
            }
        }
    }

    const placeOrderCallback = data => {
        console.log(data)
    }

    if (props.cartId) {
        if (Identify.findGetParameter('placeOrder')) {
            paypalPlaceOrder(placeOrderCallback, {quote_id: props.cartId})
        } else if (Identify.findGetParameter('paymentFaled')) {
            props.toggleMessages([{ type: 'error', message: Identify.__('Payment Failed'), auto_dismiss: true }])
            props.history.push('/')
        } else 
            paypalExpressStart(setData, {quote_id: props.cartId})
    }
    return <Loading />
}


const mapDispatchToProps = {
    toggleMessages
};

const mapStateToProps = ({ cart }) => {
    const { cartId } = cart
    return {
        cartId
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(PaypalExpress);
