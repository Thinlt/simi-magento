import React, { Component, Fragment } from 'react';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import { bool, func, object, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from 'src/classify';
import {
    getCartDetails,
    updateItemInCart,
    removeItemFromCart
} from 'src/actions/cart';
import defaultClasses from './cart.css';
import { isEmptyCartVisible } from 'src/selectors/cart';

import Breadcrumb from "src/simi/core/BaseComponents/Breadcrumb";
import Loading from 'src/simi/core/BaseComponents/Loading'

import Identify from 'src/simi/Helper/Identify'
import Arrowup from 'src/simi/core/BaseComponents/Icon/Arrowup'
import Basket from 'src/simi/core/BaseComponents/Icon/Basket'
import CartItem from './cartItem'

class Cart extends Component {
    static propTypes = {
        cart: shape({
            details: object,
            cartId: string,
            totals: object,
            isLoading: bool,
            isUpdatingItem: bool
        }),
        classes: shape({
            body: string,
            header: string,
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
    };

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

    async componentDidMount() {
        this.setIsPhone()
        const { getCartDetails } = this.props;
        await getCartDetails();
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
        const { cart, removeItemFromCart, classes, updateItemInCart } = this.props;
        if (!cart)
            return
        const { cartCurrencyCode, cartId } = this;
        if (cartId) {
            const obj = [];
            obj.push(
                <div key={Identify.randomString(5)} className={classes['cart-item-header']}>
                    <div style={{width: '60%', borderRight: 'solid #DCDCDC 1px'}}>{Identify.__('Items')}</div>
                    <div style={{width: '11%', borderRight: 'solid #DCDCDC 1px', textAlign: 'center'}}>{Identify.__('Unit Price')}</div>
                    <div style={{width: '11%', borderRight: 'solid #DCDCDC 1px', textAlign: 'center'}}>{Identify.__('Qty')}</div>
                    <div style={{width: '11%', borderRight: 'solid #DCDCDC 1px', textAlign: 'center'}}>{Identify.__('Total Price')}</div>
                    <div style={{width: '7%'}}>{Identify.__('').toUpperCase()}</div>
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
                const element = <CartItem   
                                    key={Identify.randomString(5)} 
                                    item={item} 
                                    isPhone={this.state.isPhone}
                                    currencyCode={cartCurrencyCode}
                                    itemTotal={itemTotal}
                                    removeItemFromCart={removeItemFromCart}
                                    updateItemInCart={updateItemInCart}/>;
                obj.push(element);
            }
            return <div className={classes['cart-list']}>{obj}</div>;
        }
    }

    get totalsSummary() {
        const { cart, classes } = this.props;
        const { cartCurrencyCode, cartId } = this;
        const hasSubtotal = cartId && cart.totals && 'subtotal' in cart.totals;
        const itemsQuantity = cart.details.items_qty;
        const itemQuantityText = itemsQuantity === 1 ? 'item' : 'items';
        const totalPrice = cart.totals.subtotal;

        return hasSubtotal ? (
            <dl className={classes.totals}>
                <dt className={classes.subtotalLabel}>
                    <span>
                        Cart Total :&nbsp;
                        <Price
                            currencyCode={cartCurrencyCode}
                            value={totalPrice}
                        />
                    </span>
                </dt>
                <dd className={classes.subtotalValue}>
                    ({itemsQuantity} {itemQuantityText})
                </dd>
            </dl>
        ) : null;
    }

    renderBreadcrumb =()=>{
        return <Breadcrumb breadcrumb={[{name:'Home',link:'/'},{name:'Basket',link:'/checkout/cart'}]}/>
    }

    handleBack() {
        this.props.history.goBack()
    }

    get miniCartInner() {
        const { productList, props } = this;
        const { cart: { isLoading },classes, isCartEmpty,cart } = props;

        const loading = isLoading?
            <div 
                className={classes['siminia-cart-page-loading']}
                style={{borderBottom: `solid 1px #eaeaea`}}
                >
                <Loading 
                    loadingStyle={{width:25,height:25}}
                    divStyle={{marginTop: 0}}
                />
            </div>:''
            
        if (isCartEmpty) {
            return (
                <div className={classes['cart-page-siminia']}>
                    {loading}
                    <div className={classes['empty-cart']}>
                    {Identify.__('You have no items in your shopping cart')}
                    </div>
                </div>
            );
        }

        return (
            <Fragment>
                {loading}
                {this.state.isPhone && this.renderBreadcrumb()}
                <div className={classes['cart-header']}>
                    <div role="presentation" className={classes['cart-back-btn']} onClick={() => this.handleBack()} onKeyUp={() => this.handleBack()} >
                        <Arrowup style={{width: 25}}/>
                        <span>{Identify.__('Continue shopping')}</span>
                    </div>
                    {   cart.details && cart.details.items_count &&
                        <div className={classes['cart-title']}>
                            <Basket/> 
                            <div>
                                {Identify.__('Your basket contains: %s item(s)').replace('%s', cart.details.items_count)}
                            </div>
                        </div>
                    }
                </div>
                <div className={classes.body}>{productList}</div>
            </Fragment>
        );
    }

    render() {
        return (
            <div className="container">
                {this.miniCartInner}
            </div>
        );
    }
}

const mapStateToProps = state => {
    const { cart } = state;
    return {
        cart,
        isCartEmpty: isEmptyCartVisible(state),
    };
};

const mapDispatchToProps = {
    getCartDetails,
    updateItemInCart,
    removeItemFromCart
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(Cart);