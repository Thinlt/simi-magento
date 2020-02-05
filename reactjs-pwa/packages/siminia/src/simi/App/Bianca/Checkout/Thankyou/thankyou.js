import React, {useState} from 'react';
import {  func, shape, string } from 'prop-types';
import { getOrderInformation } from 'src/selectors/checkoutReceipt';
import { connect } from 'src/drivers';
import actions from 'src/actions/checkoutReceipt';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper'
import { Colorbtn } from 'src/simi/BaseComponents/Button'
import {getOrderDetail} from 'src/simi/Model/Orders'
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { analyticPurchaseGTM } from 'src/simi/Helper/Analytics'

require('./thankyou.scss')

const Thankyou = props => {
    const {  history, order, isSignedIn } = props;
    let padOrderId = null
    const last_cart_info = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'last_cart_info');
    const last_order_info = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'last_order_info');
    const [orderIncIdFromAPI, setOrderIncFromAPI] = useState(false)

    let isPreOrder = false
    try {
        const items = last_cart_info.cart.totals.items
        items.map(item => {
            if(item.simi_pre_order_option)
                isPreOrder = true
        })
    } catch (err) {

    }

    if (last_order_info) {
        if (orderIncIdFromAPI)
            padOrderId = orderIncIdFromAPI
        else {
            showFogLoading()
            getOrderDetail(last_order_info, (orderData) => {
                hideFogLoading()
                if (orderData && orderData.order && orderData.order.increment_id) {
                    analyticPurchaseGTM(orderData.order)
                    setOrderIncFromAPI(orderData.order.increment_id)
                }
            })
        }
    }

    const hasOrderId = () => {
        return (order && order.id) ||  Identify.findGetParameter('order_increment_id') || last_order_info;
    }

    const handleViewOrderDetails = () => {
        if (!hasOrderId()) {
            history.push('/');
            return;
        }
        const orderId = '/orderdetails.html/' + padOrderId;
        const orderLocate = {
            pathname: orderId,
            state: {
                orderData: {
                    increment_id: padOrderId
                }
            }
        }
        history.push(orderLocate);
    }
    
    return (
        <div className="container thankyou-container" style={{ marginTop: 40 }}>
            {TitleHelper.renderMetaHeader({
                title:Identify.__('Thank you for your purchase!')
            })}
            <div className="thankyou-root">
                <h2 className='header'>{Identify.__('Thank you for your purchase!')}</h2>
                <div  className="email-sending-message">
                    {padOrderId && <div className="order-number">{Identify.__('Order your number is #@').replace('@', padOrderId)}</div>}
                    {isPreOrder && <div className="order-preorder-note">{Identify.__('Please be aware that this is a preorder. You will be informed once they become available.')}</div>}
                    {Identify.__("We'll email you an order confirmation with details and tracking info.")}
                </div>
                <div className="order-actions">
                    {(isSignedIn && hasOrderId()) && <Colorbtn 
                        onClick={handleViewOrderDetails}
                        style={{ backgroundColor: '#101820', color: '#FFF' }}
                        className="view-order-details"
                        text={Identify.__('View Order Details')} />}
                    <Colorbtn 
                        onClick={()=>history.push('/')}
                        style={{ backgroundColor: '#101820', color: '#FFF' }}
                        className="continue-shopping"
                        text={Identify.__('Continue shopping')} />
                </div>
            </div>
        </div>
    );
};

Thankyou.propTypes = {
    order: shape({
        id: string
    }).isRequired,
    createAccount: func.isRequired,
    reset: func.isRequired
};

Thankyou.defaultProps = {
    order: {},
    reset: () => { },
    createAccount: () => { }
};

const { reset } = actions;

const mapStateToProps = state => {
    const { user} = state;
    const { isSignedIn } = user;
    return ({
        order: getOrderInformation(state),
        isSignedIn
    });
}

const mapDispatchToProps = {
    reset
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Thankyou);
