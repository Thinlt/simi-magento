import React, { Component, Fragment } from 'react';
import { connect } from 'src/drivers';
import { bool, object, shape, string } from 'prop-types';
import {
    getCartDetails,
    updateItemInCart
} from 'src/actions/cart';
import { isEmptyCartVisible } from 'src/selectors/cart';

import BreadCrumb from "src/simi/BaseComponents/BreadCrumb"
import Loading from 'src/simi/BaseComponents/Loading'

import Identify from 'src/simi/Helper/Identify'
// import CartItem from 'src/simi/App/core/Cart/cartItem'
import CartItem from './cartItem'
import Total from 'src/simi/BaseComponents/Total'
import {Colorbtn, Whitebtn} from 'src/simi/BaseComponents/Button'
import {configColor} from 'src/simi/Config'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import {removeItemFromCart} from 'src/simi/Model/Cart'
import Coupon from 'src/simi/App/Bianca/BaseComponents/Coupon'
import GiftVoucher from 'src/simi/App/Bianca/Cart/Components/GiftVoucher'
import Estimate from 'src/simi/App/Bianca/Cart/Components/Estimate'

require('./cart.scss')

class Cart extends Component {
    constructor(...args) {
        super(...args)
        const isPhone = window.innerWidth < 1024
        this.state = {
            isPhone: isPhone,
            focusItem: null
        };
    }

    setIsPhone(){
        const obj = this;
        window.onresize = function () {
            const width = window.innerWidth;
            const isPhone = width < 1024
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone: isPhone})
            }
        }
    }

    componentDidMount() {
        showFogLoading()
        this.setIsPhone()
        const { getCartDetails } = this.props;
        getCartDetails();
    }

    get cartId() {
        const { cart } = this.props;

        return cart && cart.details && cart.details.id;
    }

    get cartCurrencyCode() {
        const { cart } = this.props;
        return (
            cart &&
            cart.details &&
            cart.details.currency &&
            cart.details.currency.quote_currency_code
        );
    }

    get productList() {
        const { cart } = this.props;
        if (!cart)
            return
        const { cartCurrencyCode, cartId } = this;
        if (cartId) {
            const obj = [];
            obj.push(
                <div key={Identify.randomString(5)} className='cart-item-header'>
                    <div style={{width: '54%'}}>{Identify.__('Items')}</div>
                    <div style={{width: '14%', textAlign: 'center'}}>{Identify.__('Price')}</div>
                    <div style={{width: '14%', textAlign: 'center'}}>{Identify.__('Quantity')}</div>
                    <div style={{width: '20%', textAlign: 'center'}}>{Identify.__('Subtotal')}</div>
                    {/* <div style={{width: '7%'}}>{Identify.__('').toUpperCase()}</div> */}
                </div>
            );
            for (const i in cart.details.items) {
                const item = cart.details.items[i];
                let itemTotal = null
                if (cart.totals && cart.totals.items) {
                    cart.totals.items.every(function(total) {
                        if (total.item_id === item.item_id) {
                            itemTotal = total
                            return false
                        }
                        else return true
                    })
                }
                if (itemTotal) {
                    const element = <CartItem
                        key={Identify.randomString(5)}
                        item={item}
                        isPhone={this.state.isPhone}
                        currencyCode={cartCurrencyCode}
                        itemTotal={itemTotal}
                        removeFromCart={this.removeFromCart.bind(this)}
                        updateCartItem={this.props.updateItemInCart}
                        history={this.props.history}
                        handleLink={this.handleLink.bind(this)}/>;
                    obj.push(element);
                }
            }
            return <div className='cart-list'>
                        {obj}
                        <div className='cart-list-footer'>
                            <div 
                                role="button"
                                tabIndex="0"
                                onClick={this.handleBack}
                                onKeyDown={this.handleBack}
                            >
                                {Identify.__('Continue Shopping')}
                            </div>
                            <div 
                                role="button"
                                tabIndex="0"
                                onClick={this.handleBack}
                                onKeyDown={this.handleBack}
                            >
                                {Identify.__('Clear all items')}
                            </div>
                        </div>
                    </div>;
        }
    }

    get totalsSummary() {
        const { cart } = this.props;
        const { cartCurrencyCode } = this;
        if (!cart.totals)
            return
        return (<Total data={cart.totals} currencyCode={cartCurrencyCode} />)
    }


    get total() {
        const { totalsSummary } = this;

        return (
            <div>
                <div className="summary">{totalsSummary}</div>
            </div>
        );
    }

    get checkoutButton() {
        return (
            <div className='cart-btn-section'>
                <Whitebtn className='continue-shopping' onClick={() => this.handleBack()} text={Identify.__('Continue shopping')}/>
                <Colorbtn
                    id="go-checkout"
                    style={{backgroundColor: configColor.button_background, color: configColor.button_text_color}}
                    className="go-checkout"
                    onClick={() => this.handleGoCheckout()} text={Identify.__('Pay Securely')}/>
            </div>
        )
    }

    get breadcrumb() {
        return <BreadCrumb breadcrumb={[{name:'Home',link:'/'},{name:'Basket',link:'/checkout/cart'}]}/>
    }

    handleLink(link) {
        this.props.history.push(link)
    }

    handleBack() {
        this.props.history.goBack()
    }

    handleGoCheckout() {
        this.props.history.push('/checkout.html')
    }

    removeFromCart(item) {
        if (confirm(Identify.__("Are you sure?")) === true) {
            showFogLoading()
            removeItemFromCart(()=>{this.props.getCartDetails()},item.item_id, this.props.isSignedIn)
        }
    }

    get couponCode() {
        const { cart, toggleMessages, getCartDetails } = this.props;
        let value = "";
        if (cart.totals.coupon_code) {
            value = cart.totals.coupon_code;
        }

        const childCPProps = {
            value,
            toggleMessages,
            getCartDetails
        }
        return <div className={`cart-coupon-form`}><Coupon {...childCPProps} /></div>
    }

    get giftVoucher() {
        const { cart, toggleMessages, getCartDetails } = this.props;
        let giftCode = ''
        if (cart.totals.total_segments) {
            const segment = cart.totals.total_segments.find((item) => {
                if (item.extension_attributes && item.extension_attributes.aw_giftcard_codes) return true;
                return false;
            });
            if (segment) {
                const aw_giftcard_codes = segment.extension_attributes.aw_giftcard_codes[0] ? segment.extension_attributes.aw_giftcard_codes[0] : '';
                if(aw_giftcard_codes){
                    const value = JSON.parse(aw_giftcard_codes);
                    giftCode = value.giftcard_code;
                }
            }
        }

        const childCPProps = {
            giftCode,
            toggleMessages,
            getCartDetails,
            cart
        }
        return <div className={`cart-voucher-form`}><GiftVoucher {...childCPProps} /></div>
    }

    get estimateShipAndTax() {
        const { cart, toggleMessages, getCartDetails, submitShippingMethod, editOrder, availableShippingMethods,getShippingMethods } = this.props;
        const childCPProps = {
            toggleMessages,
            getCartDetails,
            cart,
            submitShippingMethod,
            editOrder,
            availableShippingMethods,
            getShippingMethods
        }
        return <div className={`estimate-form`}><Estimate {...childCPProps} /></div>
    }

    get miniCartInner() {
        const { productList, props, total, checkoutButton, couponCode, giftVoucher, estimateShipAndTax } = this;
        const { cart: { isLoading }, isCartEmpty,cart } = props;
        if (isCartEmpty || !cart.details || !parseInt(cart.details.items_count)) {
            if (isLoading)
                return <Loading />
            else
                return (
                    <div className='cart-page-siminia'>
                        <div className='empty-cart'>
                        {Identify.__('You have no items in your shopping cart')}
                        </div>
                    </div>
                );
        }

        if (isLoading)
            showFogLoading()
        else
            hideFogLoading()
            

        return (
            <Fragment>
                {this.state.isPhone && this.breadcrumb}
                <div className='cart-header'>
                    {   (cart.details && parseInt(cart.details.items_count))?
                        <div className='cart-title'>
                            <div>
                                {Identify.__('Shopping cart')}
                            </div>
                        </div>:''
                    }
                </div>
            
                <div className="body">
                    {productList}
                    <div className="summary-zone">
                        <div>{Identify.__('Summary')}</div>
                        {couponCode}
                        {giftVoucher}
                        {estimateShipAndTax}
                        {total}
                    </div>
                </div>
                
                {/* {couponView}
                {total} */}
                {checkoutButton}
            </Fragment>
        );
    }

    render() {
        hideFogLoading()
        return (
            <div className="container cart-page">
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Shopping Cart')
                })}
                {this.miniCartInner}
            </div>
        );
    }
}

Cart.propTypes = {
    cart: shape({
        details: object,
        cartId: string,
        totals: object,
        isLoading: bool,
        isUpdatingItem: bool
    }),
    isCartEmpty: bool
}

const mapStateToProps = state => {
    const { cart, user } = state;
    const { isSignedIn } = user;
    return {
        cart,
        isCartEmpty: isEmptyCartVisible(state),
        isSignedIn,
    };
};

const mapDispatchToProps = {
    getCartDetails,
    toggleMessages,
    updateItemInCart,
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Cart);
