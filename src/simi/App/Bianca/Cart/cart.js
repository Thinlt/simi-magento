import React, { Component, Fragment } from 'react';
import { connect } from 'src/drivers';
import { bool, object, shape, string } from 'prop-types';
import { getCartDetails, updateItemInCart } from 'src/actions/cart';
import {
    editOrder,
} from 'src/actions/checkout';
import { isEmptyCartVisible } from 'src/selectors/cart';
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb';
import Loading from 'src/simi/BaseComponents/Loading';

import Identify from 'src/simi/Helper/Identify';
// import CartItem from 'src/simi/App/core/Cart/cartItem'
import CartItem from './cartItem';
import { Price } from '@magento/peregrine';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {
    showFogLoading,
    hideFogLoading
} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { removeItemFromCart, removeAllItems } from 'src/simi/Model/Cart';
import Coupon from 'src/simi/App/Bianca/BaseComponents/Coupon';
import GiftVoucher from 'src/simi/App/Bianca/Cart/Components/GiftVoucher';
import EmptyMiniCart from '../Components/MiniCart/emptyMiniCart';
import { isArray } from 'util';

require('./cart.scss');

class Cart extends Component {
    constructor(...args) {
        super(...args);
        const isPhone = window.innerWidth < 1024;
        this.state = {
            isPhone: isPhone,
            focusItem: null,
            items: this.props.cart.details.items,
            isLoading: true
        };
    }

    setIsPhone() {
        const obj = this;
        window.onresize = function() {
            const width = window.innerWidth;
            const isPhone = width < 1024;
            if (obj.state.isPhone !== isPhone) {
                obj.setState({ isPhone: isPhone });
            }
        };
    }

    componentDidMount() {
        const {props} = this
        if (this.props && this.props.location && this.props.location.search && this.props.location.search.indexOf('payment=false') !== -1) {
            if (!this.toggledErrMessOnce) {
                this.toggledErrMessOnce = true
                if (props.toggleMessages){
                    props.toggleMessages([{ type: 'error', message: Identify.__('An error occurred while making the transaction. Please try again.'), auto_dismiss: false }])
                }
            }
        }
        this.setState({isLoading: false})
        showFogLoading();
        this.setIsPhone();
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

    updateItemCart = (item,quantity) => {
        showFogLoading()
        const payload = {
            item,
            quantity
        };
        this.props.updateItemInCart(payload, item.item_id)
    }

    removeAllItemsInCart = () => {
        const { cart } = this.props;
        if (confirm(Identify.__('Are you sure?')) === true) {
            showFogLoading();
            const initialValue = {};
            const allItems = cart.details.items.reduce((obj,item) => {
                return {
                    ...obj,
                    [item.item_id]: "0",
                };
            },initialValue);
            this.setState({isLoading: true})
            removeAllItems(this.removeAllCallBack, allItems);
        }
    }

    removeAllCallBack = (data) => {
        this.setState({isLoading: false})
        this.setState({items: data.quoteitems})
        getCartDetails();
    }

    get productList() {
        const { cart } = this.props;
        if (!cart) return;
        const { cartCurrencyCode, cartId } = this;
        if (cartId) {
            const obj = [];
            obj.push(
                <Fragment>
                    {!this.state.isPhone
                    ?
                        <div
                            key={Identify.randomString(5)}
                            className="cart-item-header"
                        >
                            <div style={{ width: '52.32%' }}>{Identify.__('Items')}</div>
                            <div style={{ width: '18.5%', textAlign: 'left' }}>
                                {Identify.__('Price')}
                            </div>
                            <div style={{ width: '17.38%', textAlign: 'left' }}>
                                {Identify.__('Quantity')}
                            </div>
                            <div style={{ width: '12.8%', textAlign: 'right' }}>
                                {Identify.__('Subtotal')}
                            </div>
                            {/* <div style={{width: '7%'}}>{Identify.__('').toUpperCase()}</div> */}
                        </div>
                    :   null
                    }
                </Fragment>
            );
            if(cart.details.items){
                for (const i in cart.details.items) {
                    const item = cart.details.items[i];
                    let itemTotal = null;
                    if (cart.totals && cart.totals.items) {
                        cart.totals.items.every(function(total) {
                            if (total.item_id === item.item_id) {
                                itemTotal = total;
                                return false;
                            } else return true;
                        });
                    }
                    if (itemTotal) {
                        const element = (
                            <CartItem
                                key={Identify.randomString(5)}
                                item={item}
                                isPhone={this.state.isPhone}
                                currencyCode={cartCurrencyCode}
                                itemTotal={itemTotal}
                                removeFromCart={this.removeFromCart.bind(this)}
                                updateCartItem={this.updateItemCart}
                                history={this.props.history}
                                handleLink={this.handleLink.bind(this)}
                                email={this.props.email}
                            />
                        );
                        obj.push(element);
                    }
                }
            }
            return (
                <div className="cart-list">
                    {obj}
                    {!this.state.isPhone 
                    ?
                        <div className="cart-list-footer">
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
                                onClick={this.removeAllItemsInCart}
                                onKeyDown={this.removeAllItemsInCart}
                            >
                                {Identify.__('Clear all items')}
                            </div>
                        </div>
                    :   null
                    }
                </div>
            );
        }
    }

    get totalsSummary() {
        const { cart } = this.props;
        const { cartCurrencyCode, cartId } = this;
        const hasSubtotal = cartId && cart.totals && 'subtotal' in cart.totals;
        const totalPrice = cart.totals.subtotal;
        const hasGrandtotal =
            cartId && cart.totals && 'grand_total' in cart.totals;
        const grandTotal = cart.totals.grand_total;
        const hasDiscount =
            cartId && cart.totals.discount_amount;
        const discount =
            (Math.abs(cart.totals.discount_amount) / totalPrice) * 100;
        let hasGiftVoucher;
        let giftCardObj;
        let giftCard;

        
        if(cart.totals.total_segments){
            giftCardObj = cart.totals.total_segments.filter(obj => obj.code === 'aw_giftcard');
            if (giftCardObj && giftCardObj.length) {
                giftCard = JSON.parse(giftCardObj[0].extension_attributes.aw_giftcard_codes[0]);
            }
            hasGiftVoucher = cartId && cart.totals.total_segments && giftCard || null;
        }
        return (
            <div>
                {hasSubtotal ? (
                    <div className="subtotal">
                        <div className="subtotal-label">Subtotal</div>
                        <div>
                            <Price
                                currencyCode={cartCurrencyCode}
                                value={totalPrice}
                            />
                        </div>
                    </div>
                ) : null}
                {hasDiscount ? (
                    <div className="subtotal">
                        <div className="subtotal-label">
                            Discount {discount}%
                        </div>
                        <div>
                            <Price
                                currencyCode={cartCurrencyCode}
                                value={discount}
                            />
                        </div>
                    </div>
                ) : null}
                {
                    hasGiftVoucher ?
                    <div className='subtotal'>
                    <div className='subtotal-label'>Discount({giftCard.giftcard_code})</div>
                        <div>
                            <Price
                                currencyCode={cartCurrencyCode}
                                value={giftCard.value}
                            />
                        </div>
                    </div>
                    : null
                }
                {hasGrandtotal ? (
                    <div className="grandtotal">
                        <div className="grandtotal-label">Grand Total</div>
                        <div className="grandtotal-price">
                            <Price
                                currencyCode={cartCurrencyCode}
                                value={grandTotal}
                            />
                        </div>
                    </div>
                ) : null}
            </div>
        );
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
            <div className="cart-btn-section">
                <Colorbtn
                    id="go-checkout"
                    className="go-checkout"
                    onClick={() => this.handleGoCheckout()}
                    text={Identify.__('Proceed to checkout')}
                />
            </div>
        );
    }

    get breadcrumb() {
        return (
            <BreadCrumb
                breadcrumb={[
                    { name: 'Home', link: '/' },
                    { name: 'Basket', link: '/checkout/cart' }
                ]}
            />
        );
    }

    handleLink(link) {
        this.props.history.push(link);
    }

    handleBack = () => {
        this.props.history.push('/');
    };

    handleGoCheckout() {
        this.props.history.push('/checkout.html');
    }

    removeFromCart(item) {
        if (confirm(Identify.__('Are you sure?')) === true) {
            showFogLoading();
            removeItemFromCart(
                () => {
                    this.props.getCartDetails();
                },
                item.item_id,
                this.props.isSignedIn
            );
        }
    }

    couponCode() {
        const { cart, toggleMessages, getCartDetails } = this.props;
        let value = '';
        if (cart.totals.coupon_code) {
            value = cart.totals.coupon_code;
        }

        const childCPProps = {
            value,
            toggleMessages,
            getCartDetails
        };
        return (
            <div className={`cart-coupon-form`}>
                <Coupon {...childCPProps} />
            </div>
        );
    }

    giftVoucher(giftCartValue) {
        const { cart, toggleMessages, getCartDetails, isSignedIn } = this.props;
        const childCPProps = {
            toggleMessages,
            getCartDetails,
            cart,
            isSignedIn,
            giftCartValue
        };
        return (
            <div className={`cart-voucher-form`}>
                <GiftVoucher {...childCPProps} />
            </div>
        );
    }

    get miniCartInner() {
        const {isLoading} = this.props.cart;
        const {
            productList,
            props,
            total,
            checkoutButton
        } = this;
        const {
            isCartEmpty,
            cart
        } = props;
        if (isCartEmpty || !cart.details.items || !parseInt(cart.details.items_count)) {
            if(isLoading){
                return <Loading />;
            }
            else{
                if(this.state.isPhone){
                    return(
                        <div className="cart-page-siminia">
                            <div className="cart-title-mobile">
                                {Identify.__("SHOPPING CART")}
                            </div>
                            <EmptyMiniCart/>
                        </div>
                    )
                }
                else{
                    return (
                        <div className="cart-page-siminia">
                            <div className="empty-cart">
                                {Identify.__(
                                    'You have no items in your shopping cart'
                                )}
                            </div>
                        </div>
                    );
                }
            }
        }

        if (isLoading) showFogLoading();
        else hideFogLoading();

        let is_pre_order = false
        let is_try_to_buy = false
        let is_all_gift_card = true //cart only contains giftcard wont show coupon/giftcard
        if (cart && cart.totals && cart.totals.items && isArray(cart.totals.items)) {
            cart.totals.items.forEach(cartTotalItem => {
                if (cartTotalItem.attribute_values && cartTotalItem.attribute_values.type_id !== "aw_giftcard") {
                    is_all_gift_card = false
                }
                if (cartTotalItem.simi_pre_order_option && cartTotalItem.simi_pre_order_option!== '[]') {
                    is_pre_order = true
                } else if (cartTotalItem.simi_trytobuy_option && cartTotalItem.simi_trytobuy_option!== '[]') {
                    is_try_to_buy = true
                }
            });
        }
        let cpValue = "";
        if (cart.totals.coupon_code) {
            cpValue = cart.totals.coupon_code;
        }
        let giftCartValue = "";
        if (cart.totals && cart.totals.total_segments) {
            cart.totals.total_segments.map(total_segment => {
                if ((total_segment.code === 'aw_giftcard') && total_segment.value) {
                    giftCartValue = total_segment.extension_attributes
                }
            })
        }

        return (
            <Fragment>
                {this.state.isPhone
                ?
                    <Fragment>
                        <div className="cart-header">
                            {cart.details && parseInt(cart.details.items_count) ? (
                                <div className="cart-title">
                                    <div>{Identify.__('Shopping cart')}</div>
                                </div>
                            ) : (
                                ''
                            )}
                        </div>
                        <div className="body">
                            {productList}
                            <div className="summary-zone row">
                                <div className="summary-title">{Identify.__('Summary'.toUpperCase())}</div>
                                {isLoading ? <Loading/>
                                :
                                    <div>
                                        {(!is_all_gift_card && !giftCartValue && !is_try_to_buy && !is_pre_order) && this.couponCode()}
                                        {(!is_all_gift_card && !cpValue && !is_try_to_buy && !is_pre_order) && this.giftVoucher(giftCartValue)}
                                        {total}
                                        {checkoutButton}
                                    </div>
                                }
                            </div>
                        </div>
                    </Fragment>
                :
                    <Fragment>
                        {this.state.isPhone && this.breadcrumb}
                        <div className="cart-header">
                            {cart.details && parseInt(cart.details.items_count) ? (
                                <div className="cart-title">
                                    <div>{Identify.__('Shopping cart')}</div>
                                </div>
                            ) : (
                                ''
                            )}
                        </div>

                        <div className="body">
                            {productList}
                            <div className="summary-zone">
                                <div className="summary-title">{Identify.__('Summary'.toUpperCase())}</div>
                                {isLoading ? <Loading/>
                                :
                                    <div>
                                        {(!is_all_gift_card && !giftCartValue && !is_try_to_buy && !is_pre_order) && this.couponCode()}
                                        {(!is_all_gift_card && !cpValue && !is_try_to_buy && !is_pre_order) && this.giftVoucher(giftCartValue)}
                                        {total}
                                        {checkoutButton}
                                    </div>
                                }
                            </div>
                        </div>
                    </Fragment>
                }
            </Fragment>
        );
    }

    render() {
        hideFogLoading();
        return (
            <div className="container cart-page">
                {TitleHelper.renderMetaHeader({
                    title: Identify.__('Shopping Cart')
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
};

const mapStateToProps = state => {
    const { cart, user} = state;
    const { isSignedIn, currentUser } = user;
    const { firstname, lastname, email } = currentUser;
    return {
        cart,
        isCartEmpty: isEmptyCartVisible(state),
        isSignedIn,
        firstname,
        lastname,
        email,
    };
};

const mapDispatchToProps = {
    getCartDetails,
    toggleMessages,
    updateItemInCart,
    editOrder,
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Cart);
