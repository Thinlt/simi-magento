import React from 'react';
import { connect } from 'src/drivers';
import Loading from 'src/simi/BaseComponents/Loading'
import Identify from 'src/simi/Helper/Identify'
import {showToastMessage} from 'src/simi/Helper/Message';
import {startpreorderscomplete} from 'src/simi/Model/Preorder'
import {
    getCartDetails
} from 'src/actions/cart';

var openCheckoutPage = false
var processingPreorderAPI = false

const PreorderSecondOrder = props => {
    const {history, isSignedIn, cartId, user, getCartDetails, totals} = props
    const deposit_order_id = Identify.findGetParameter('deposit_order_id')
    if (!deposit_order_id) {
        showToastMessage(Identify.__('Sorry, your deposit Order Id is not valid.'))
        history.push('/');   
    }
    const customer_id =  Identify.findGetParameter('customer_id')
    const customer_email =  Identify.findGetParameter('customer_email')
    const startPreorderCompleted = data => {
        if (data.errors && data.errors.length) {
            let errorsMessage =  ''
            data.errors.map(error => {
                errorsMessage += error.message
            });
            showToastMessage(errorsMessage)
        }
        if (data.startpreorderscompletes || data.startpreorderscomplete) {
            getCartDetails()
            openCheckoutPage = true
        }
    }
    
    if (openCheckoutPage) {
        if (totals && totals.items && totals.items.length) {
            history.push('/checkout.html'); 
        }
    }
    
    if (customer_id && customer_email) { //need to signin first
        if (!isSignedIn) {
            const location = {
                pathname: '/login.html',
                pushTo: props.location.pathname + props.location.search
            };
            history.push(location);
        } else if (user && user.currentUser && user.currentUser.email && user.currentUser.email !== customer_email) {
            showToastMessage(Identify.__('Sorry. Your account does not match Pre-order deposit information, please signout and open this page again'))
            history.push({pathname: '/logout.html'});
        } else if (user && user.currentUser && user.currentUser.email && user.currentUser.email === customer_email) {
            console.log(cartId)
            console.log(totals)
            if (cartId && totals && !processingPreorderAPI) { //get cart done then request start pre order
                processingPreorderAPI = true
                startpreorderscomplete((data) => startPreorderCompleted(data), deposit_order_id, cartId)
            }
        }
    } else {
        showToastMessage(Identify.__('The url seems to be wrong, please contact us.'))
    }
    return <Loading />
};


const mapStateToProps = ({ user, cart }) => {
    const { isSignedIn } = user;
    const { cartId, totals } = cart
    return {
        isSignedIn,
        cartId,
        totals,
        user
    }
}
const mapDispatchToProps = {
    getCartDetails
};


export default connect(
    mapStateToProps,
    mapDispatchToProps
)(PreorderSecondOrder);
