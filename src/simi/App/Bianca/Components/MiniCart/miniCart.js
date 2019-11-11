import React, { Component, Fragment } from 'react';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import { bool, func, object, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from 'src/classify';
import {
    getCartDetails,
    updateItemInCart,
    openOptionsDrawer,
    closeOptionsDrawer
} from 'src/actions/cart';
import {
    submitShippingMethod,
    editOrder,
    getShippingMethods
} from 'src/actions/checkout';
import { cancelCheckout } from 'src/actions/checkout';
import { getCountries } from 'src/actions/directory';
import { submitShippingAddress } from 'src/simi/Redux/actions/simiactions';
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
import Trigger from './trigger';
import Icon from 'src/components/Icon';
import CloseIcon from 'react-feather/dist/icons/x';
import Coupon from 'src/simi/App/Bianca/BaseComponents/Coupon';
import GiftVoucher from 'src/simi/App/Bianca/Cart/Components/GiftVoucher';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { removeItemFromCart } from 'src/simi/Model/Cart';
import Estimate from 'src/simi/App/Bianca/Cart/Components/Estimate';
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
        openOptionsDrawer: func.isRequired,
        closeOptionsDrawer: func.isRequired,
        isMiniCartMaskOpen: bool,
        closeDrawer: func.isRequired
    };

    constructor(...args) {
        super(...args);
        this.state = {
            focusItem: null
        };
        this.wrapperMiniCart = React.createRef();
    }

    // async componentDidMount() {
    //     const { getCartDetails, getCountries } = this.props;
    //     console.log('loaded')
    //     // await getCartDetails();
    //     // await getShippingMethods();
    //     // await getCountries();
    // }

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

    removeFromCart(item) {
        if (confirm(Identify.__('Are you sure?')) === true) {
            // <Loading/>
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
        const { cart, isOpen, updateItemInCart } = this.props;

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
                            updateCartItem={updateItemInCart}
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
        this.props.history.push(link);
    }

    get totalsSummary() {
        const { cart, classes } = this.props;
        const { cartCurrencyCode, cartId } = this;
        const hasSubtotal = cartId && cart.totals && 'subtotal' in cart.totals;
        const totalPrice = cart.totals.subtotal;

        return hasSubtotal ? (
            <div className={classes.subtotal}>
                <div className={classes.subtotalLabel}>Subtotal</div>
                <div>
                    <Price currencyCode={cartCurrencyCode} value={totalPrice} />
                </div>
            </div>
        ) : null;
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
                <div>
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
            let segment = cart.totals.total_segments.find(item => {
                if (
                    item.extension_attributes &&
                    item.extension_attributes.aw_giftcard_codes
                )
                    return true;
                return false;
            });
            if (segment) {
                let aw_giftcard_codes = segment.extension_attributes
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

    get estimateShipAndTax() {
        const {
            cart,
            toggleMessages,
            getCartDetails,
            submitShippingMethod,
            editOrder,
            availableShippingMethods,
            getShippingMethods,
            shippingAddress,
            submitShippingAddress,
            countries
        } = this.props;
        const childCPProps = {
            toggleMessages,
            getCartDetails,
            cart,
            submitShippingMethod,
            editOrder,
            availableShippingMethods,
            getShippingMethods,
            shippingAddress,
            submitShippingAddress,
            countries
        };
        return (
            <div className={`estimate-form`}>
                <Estimate {...childCPProps} />
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
            estimateShipAndTax,
            grandTotal
        } = this;
        const { classes, closeDrawer } = props;

        return (
            <div className={classes.summary}>
                <h2 className={classes.titleSummary}>
                    <span>{Identify.__('Summary')}</span>
                </h2>
                {totalsSummary}
                {couponCode}
                {giftVoucher}
                {/* {estimateShipAndTax} */}
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

    openOptionsDrawer = item => {
        this.setState({
            focusItem: item
        });
        this.props.openOptionsDrawer();
    };

    // closeDrawer = () => {
    //     // const {target} = e;

    //     // if(!this.wrapperMiniCart.current.contains(target)){
    //     //     this.props.closeDrawer();
    //     // }
    //     this.props.closeDrawer();
    //     // this.handleClick();
    // };

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
                <div className={classes.body}>{productList}</div>
                <div className={footerClassName}>{footer}</div>
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
            isOpen
        } = props;

        const className = isOpen ? classes.root_open : classes.root;
        const body = isOptionsDrawerOpen ? productOptions : miniCartInner;
        const title = isOptionsDrawerOpen ? 'Edit Cart Item' : 'My Cart';

        return (
            <aside className={`${className} minicart`}>
                <div className={classes.header}>
                    <h2 className={classes.title}>
                        <span>{title}</span>
                    </h2>
                    <Trigger>
                        <Icon src={CloseIcon} />
                    </Trigger>
                </div>
                {isLoading ? <Loading /> : body}
                <Mask isActive={isMiniCartMaskOpen} dismiss={cancelCheckout} />
            </aside>
        );
    }
}

const mapStateToProps = state => {
    const { cart, user, checkout, directory } = state;
    const { isSignedIn } = user;
    const { availableShippingMethods, shippingAddress } = checkout;
    const { countries } = directory
    return {
        cart,
        isCartEmpty: isEmptyCartVisible(state),
        isMiniCartMaskOpen: isMiniCartMaskOpen(state),
        isSignedIn,
        availableShippingMethods,
        shippingAddress,
        countries
    };
};

const mapDispatchToProps = {
    getCartDetails,
    updateItemInCart,
    removeItemFromCart,
    openOptionsDrawer,
    closeOptionsDrawer,
    cancelCheckout,
    closeDrawer,
    toggleMessages,
    submitShippingMethod,
    editOrder,
    getShippingMethods,
    getCountries,
    submitShippingAddress
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(MiniCart);