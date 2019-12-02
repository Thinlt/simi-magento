import React from 'react';
import { connect } from 'src/drivers';
import Loading from 'src/simi/BaseComponents/Loading'
import Identify from 'src/simi/Helper/Identify'
import {showToastMessage} from 'src/simi/Helper/Message';
import {startpreorderscomplete} from 'src/simi/Model/Preorder'

const PreorderSecondOrder = props => {
    console.log(props)
    const {history, isSignedIn, cartId, user} = props
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
        if (data.startpreorderscompletes || data.startpreorderscomplete)
            history.push('/checkout.html');
    }
    if (customer_id && customer_email) { //need to signin first
        if (!isSignedIn || (user && user.currentUser && user.currentUser.email && (user.currentUser.email !== customer_email))) {
            const location = {
                pathname: '/login.html',
                pushTo: props.location.pathname + props.location.search
            };
            history.push(location);
        } else {
            startpreorderscomplete(startPreorderCompleted, deposit_order_id, cartId)
        }
    } else { //no need to signin first
        if (isSignedIn || cartId)
            startpreorderscomplete(startPreorderCompleted, deposit_order_id, cartId)
    }
    return <Loading />
};


const mapStateToProps = ({ user, cart }) => {
    const { isSignedIn } = user;
    const { cartId } = cart
    return {
        isSignedIn,
        cartId,
        user
    }
}

export default connect(
    mapStateToProps,
    null
)(PreorderSecondOrder);
