import React, { Component, Fragment } from 'react';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import { bool, func, object, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from 'src/classify';
import {
    getCartDetails,
    updateItemInCart,
} from 'src/actions/cart';
import { cancelCheckout } from 'src/actions/checkout';
import CheckoutButton from 'src/components/Checkout/checkoutButton';
import EmptyMiniCart from './emptyMiniCart';
import Mask from './mask';
import defaultClasses from './miniCart.css';
import { isEmptyCartVisible, isMiniCartMaskOpen } from 'src/selectors/cart';
import Loading from 'src/simi/BaseComponents/Loading';
import CartItem from '../../Cart/cartItem';
import Identify from 'src/simi/Helper/Identify';
import { closeDrawer } from 'src/actions/app';
import { Link } from 'react-router-dom';
import Coupon from 'src/simi/App/Bianca/BaseComponents/Coupon';
import GiftVoucher from 'src/simi/App/Bianca/Cart/Components/GiftVoucher';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { removeItemFromCart } from 'src/simi/Model/Cart';
import {
    showFogLoading,
    hideFogLoading
} from 'src/simi/BaseComponents/Loading/GlobalLoading';
class MiniCart extends Component {
    static propTypes = {
        cancelCheckout: func.isRequired,
        cart: shape({
            details: object,
            cartId: string,
            totals: object,
            isLoading: bool,
            isOptionsDrawerOpen: bool,
            isUpdatingItem: bool
        }),
        classes: shape({
            body: string,
            footer: string,
            footerMaskOpen: string,
            header: string,
            placeholderButton: string,
            root_open: string,
            root: string,
            subtotalLabel: string,
            subtotalValue: string,
            summary: string,
            title: string,
            totals: string
        }),
        isCartEmpty: bool,
        updateItemInCart: func,
        isMiniCartMaskOpen: bool,
        closeDrawer: func.isRequired
    };

    constructor(...args) {
        super(...args);
        this.wrapperMiniCart = React.createRef();
        this.state = {
            data: ''
        }
    }

    handleClickOutside = (event) => {
        if (this.wrapperMiniCart && this.wrapperMiniCart.current && !this.wrapperMiniCart.current.contains(event.target)) {
            this.props.closeDrawer()
            $('body').css('overflow','auto')
        }
    }

    componentDidMount(){
        document.addEventListener("mousedown", this.handleClickOutside);
        this.props.getCartDetails();
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

    removeFromCart(item) {
        if (confirm(Identify.__('Are you sure?')) === true) {
            showFogLoading()
            removeItemFromCart(
                () => {
                    this.props.getCartDetails();
                },
                item.item_id,
                this.props.isSignedIn
                );
        }
    }

    get productList() {
        const { cart, isOpen } = this.props;

        const { cartCurrencyCode, cartId } = this;

        if (cartId) {
            const obj = [];
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
                            removeFromCart={this.removeFromCart.bind(this)}
                            updateCartItem={this.updateItemCart}
                            currencyCode={cartCurrencyCode}
                            item={item}
                            itemTotal={itemTotal}
                            handleLink={this.handleLink.bind(this)}
                            isOpen={isOpen}
                        />
                    );
                    obj.push(element);
                }
            }
            return <div className="cart-list">{obj}</div>;
        }
    }

    handleLink(link) {
        const {closeDrawer, history} = this.props;
        closeDrawer();
        history.push(link);
    }

    get totalsSummary() {
        const { cart, classes } = this.props;
        const { cartCurrencyCode, cartId } = this;
        const hasSubtotal = cartId && cart.totals && 'subtotal' in cart.totals;
        const totalPrice = cart.totals.subtotal;
        const hasDiscount = cartId && cart.totals.discount_amount;
        const discount = (Math.abs(cart.totals.discount_amount)/totalPrice) * 100;
        return (
            <div>
                {hasDiscount ? 
                    <div className={classes.subtotal}>
                        <div className={classes.subtotalLabel}>Discount {discount}%</div>
                        <div>
                            <Price
                                currencyCode={cartCurrencyCode}
                                value={discount}
                            />
                        </div>
                    </div>
                    : null
                }
                {hasSubtotal ? (
                <div className={classes.subtotal}>
                    <div className={classes.subtotalLabel}>Subtotal</div>
                    <div>
                        <Price currencyCode={cartCurrencyCode} value={totalPrice} />
                    </div>
                </div>
                ) : null}
            </div>
        ) 
        
    }

    get grandTotal() {
        const { cart, classes } = this.props;
        const { cartCurrencyCode, cartId } = this;
        const hasGrandtotal =
            cartId && cart.totals && 'grand_total' in cart.totals;
        const grandTotal = cart.totals.grand_total;
        return hasGrandtotal ? (
            <div className={classes.grandTotal}>
                <div className={classes.grandTotalLabel}>Grand Total</div>
                <div className={classes.grandPrice}>
                    <Price currencyCode={cartCurrencyCode} value={grandTotal} />
                </div>
            </div>
        ) : null;
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

    get placeholderButton() {
        const { classes } = this.props;
        return (
            <div className={classes.placeholderButton}>
                <CheckoutButton ready={false} />
            </div>
        );
    }

    get checkout() {
        const {
            props,
            totalsSummary,
            couponCode,
            giftVoucher,
            grandTotal
        } = this;
        const { classes, closeDrawer } = props;

        return (
            <div className={classes.summary}>
                <h2 className={classes.titleSummary}>
                    <span>{Identify.__('Summary')}</span>
                </h2>
                {couponCode}
                {giftVoucher}
                {totalsSummary}
                {grandTotal}

                <div className={classes.minicartAction}>
                    <Link to="/cart.html" onClick={closeDrawer}>
                        <button className={classes.viewCartBtn}>
                            {Identify.__('VIEW & EDIT CART')}
                        </button>
                    </Link>
                    <Link to="/checkout.html" onClick={closeDrawer}>
                        <button className={classes.checkoutBtn}>
                            {Identify.__('PROCEED TO CHECKOUT')}
                        </button>
                    </Link>
                </div>
            </div>
        );
    }

    get miniCartInner() {
        const { checkout, productList, props } = this;
        const { classes, isCartEmpty, isMiniCartMaskOpen } = props;

        if (isCartEmpty) {
            return <EmptyMiniCart />;
        }

        const footer = checkout;

        const footerClassName = isMiniCartMaskOpen
            ? classes.footerMaskOpen
            : classes.footer;

        return (
            <Fragment>
                <div style={{overflow:'scroll', display:'flex', flexDirection:'column', justifyContent:'space-between'}}>
                    <div className={classes.body}>
                        {productList}
                    </div>
                    <div className={footerClassName}>{footer}</div>
                </div>
            </Fragment>
        );
    }

    render() {
        const { miniCartInner, productOptions, props } = this;
        const {
            cancelCheckout,
            cart: { isOptionsDrawerOpen, isLoading },
            classes,
            isMiniCartMaskOpen,
            isOpen,
            isCartEmpty
        } = props;

        if(isOpen){
            $('body').css('overflow','hidden');
        }

        const className = isOpen ? classes.root_open : classes.root;
        const body = isOptionsDrawerOpen ? productOptions : miniCartInner;
        const title = isOptionsDrawerOpen ? 'Edit Cart Item' : 'My Cart';

        hideFogLoading()
        return (
            <aside className={`${className} minicart`} ref={this.wrapperMiniCart}>
                {isCartEmpty
                ?
                    <div className={classes.header}></div>
                :
                    <div className={classes.header}>
                        <h2 className={classes.title}>
                            <span>{title}</span>
                        </h2>
                    </div>
                }
                {isLoading ? <Loading /> : body}
                {/* {body} */}
                <Mask isActive={isMiniCartMaskOpen} dismiss={cancelCheckout} />
            </aside>
        );
    }
}

const mapStateToProps = state => {
    const { cart, user, app } = state;
    const { isSignedIn } = user;
    const { drawer } = app 
    return {
        cart,
        drawer,
        isCartEmpty: isEmptyCartVisible(state),
        isMiniCartMaskOpen: isMiniCartMaskOpen(state),
        isSignedIn,
    };
};

const mapDispatchToProps = {
    getCartDetails,
    updateItemInCart,
    removeItemFromCart,
    cancelCheckout,
    closeDrawer,
    toggleMessages,
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(MiniCart);
