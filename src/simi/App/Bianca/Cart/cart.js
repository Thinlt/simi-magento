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
                <div
                    key={Identify.randomString(5)}
                    className="cart-item-header"
                >
                    <div style={{ width: '52.5%' }}>{Identify.__('Items')}</div>
                    <div style={{ width: '17%', textAlign: 'left' }}>
                        {Identify.__('Price')}
                    </div>
                    <div style={{ width: '16%', textAlign: 'left' }}>
                        {Identify.__('Quantity')}
                    </div>
                    <div style={{ width: '14%', textAlign: 'right' }}>
                        {Identify.__('Subtotal')}
                    </div>
                    {/* <div style={{width: '7%'}}>{Identify.__('').toUpperCase()}</div> */}
                </div>
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
                            />
                        );
                        obj.push(element);
                    }
                }
            }
            return (
                <div className="cart-list">
                    {obj}
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

    get couponCode() {
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

    get giftVoucher() {
        const { cart, toggleMessages, getCartDetails } = this.props;
        let giftCode = '';
        if (cart.totals.total_segments) {
            const segment = cart.totals.total_segments.find(item => {
                if (
                    item.extension_attributes &&
                    item.extension_attributes.aw_giftcard_codes
                )
                    return true;
                return false;
            });
            if (segment) {
                const aw_giftcard_codes = segment.extension_attributes
                    .aw_giftcard_codes[0]
                    ? segment.extension_attributes.aw_giftcard_codes[0]
                    : '';
                if (aw_giftcard_codes) {
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
            checkoutButton,
            couponCode,
            giftVoucher
        } = this;
        const {
            isCartEmpty,
            cart
        } = props;
        if (
            isCartEmpty ||
            !cart.details.items ||
            !parseInt(cart.details.items_count)
        ) {
            if (isLoading) return <Loading />;
            else
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

        if (isLoading) showFogLoading();
        else hideFogLoading();

        return (
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
                        <div>{Identify.__('Summary'.toUpperCase())}</div>
                        {isLoading ? <Loading/>
                        :
                            <div>
                                {couponCode}
                                {giftVoucher}
                                {total}
                                {checkoutButton}
                            </div>
                        }
                    </div>
                </div>

                {/* {couponView}
                {total} */}
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
    editOrder,
};

export default connect(
    mapStateToProps,
    mapDispatchToProps
)(Cart);
