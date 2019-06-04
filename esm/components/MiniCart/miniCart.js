function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment, Suspense } from 'react';
import { compose } from 'redux';
import { connect } from "@magento/venia-drivers";
import { bool, func, object, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from "../../classify";
import { getCartDetails, updateItemInCart, removeItemFromCart, openOptionsDrawer, closeOptionsDrawer } from "../../actions/cart";
import { cancelCheckout } from "../../actions/checkout";
import Icon from "../Icon";
import CloseIcon from 'react-feather/dist/icons/x';
import CheckoutButton from "../Checkout/checkoutButton";
import EmptyMiniCart from "./emptyMiniCart";
import Mask from "./mask";
import ProductList from "./productList";
import Trigger from "./trigger";
import defaultClasses from "./miniCart.css";
import { isEmptyCartVisible, isMiniCartMaskOpen } from "../../selectors/cart";
import CartOptions from "./cartOptions";
import getProductDetailByName from "../../queries/getProductDetailByName.graphql";
import { loadingIndicator } from "../LoadingIndicator";
import { Query } from 'react-apollo';
const Checkout = React.lazy(() => import("../Checkout"));

class MiniCart extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "openOptionsDrawer", item => {
      this.setState({
        focusItem: item
      });
      this.props.openOptionsDrawer();
    });

    _defineProperty(this, "closeOptionsDrawer", () => {
      this.props.closeOptionsDrawer();
    });

    this.state = {
      focusItem: null
    };
  }

  async componentDidMount() {
    const {
      getCartDetails
    } = this.props;
    await getCartDetails();
  }

  get cartId() {
    const {
      cart
    } = this.props;
    return cart && cart.details && cart.details.id;
  }

  get cartCurrencyCode() {
    const {
      cart
    } = this.props;
    return cart && cart.details && cart.details.currency && cart.details.currency.quote_currency_code;
  }

  get productList() {
    const {
      cart,
      removeItemFromCart
    } = this.props;
    const {
      cartCurrencyCode,
      cartId
    } = this;
    return cartId ? React.createElement(ProductList, {
      removeItemFromCart: removeItemFromCart,
      openOptionsDrawer: this.openOptionsDrawer,
      currencyCode: cartCurrencyCode,
      items: cart.details.items,
      totalsItems: cart.totals.items
    }) : null;
  }

  get totalsSummary() {
    const {
      cart,
      classes
    } = this.props;
    const {
      cartCurrencyCode,
      cartId
    } = this;
    const hasSubtotal = cartId && cart.totals && 'subtotal' in cart.totals;
    const itemsQuantity = cart.details.items_qty;
    const itemQuantityText = itemsQuantity === 1 ? 'item' : 'items';
    const totalPrice = cart.totals.subtotal;
    return hasSubtotal ? React.createElement("dl", {
      className: classes.totals
    }, React.createElement("dt", {
      className: classes.subtotalLabel
    }, React.createElement("span", null, "Cart Total :\xA0", React.createElement(Price, {
      currencyCode: cartCurrencyCode,
      value: totalPrice
    }))), React.createElement("dd", {
      className: classes.subtotalValue
    }, "(", itemsQuantity, " ", itemQuantityText, ")")) : null;
  }

  get placeholderButton() {
    const {
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.placeholderButton
    }, React.createElement(CheckoutButton, {
      ready: false
    }));
  }

  get checkout() {
    const {
      props,
      totalsSummary,
      placeholderButton
    } = this;
    const {
      classes,
      cart
    } = props;
    return React.createElement("div", null, React.createElement("div", {
      className: classes.summary
    }, totalsSummary), React.createElement(Suspense, {
      fallback: placeholderButton
    }, React.createElement(Checkout, {
      cart: cart
    })));
  }

  get productOptions() {
    const {
      props,
      state,
      closeOptionsDrawer
    } = this;
    const {
      updateItemInCart,
      cart
    } = props;
    const {
      focusItem
    } = state;
    if (focusItem === null) return;
    const hasOptions = focusItem.options.length !== 0;
    return hasOptions ? // `Name` is being used here because GraphQL does not allow
    // filtering products by id, and sku is unreliable without
    // a reference to the base product. Additionally, `url-key`
    // cannot be used because we don't have page context in cart.
    React.createElement(Query, {
      query: getProductDetailByName,
      variables: {
        name: focusItem.name,
        onServer: false
      }
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) return React.createElement("div", null, "Data Fetch Error");
      if (loading) return loadingIndicator;
      const itemWithOptions = data.products.items[0];
      return React.createElement(CartOptions, {
        cartItem: focusItem,
        configItem: itemWithOptions,
        closeOptionsDrawer: closeOptionsDrawer,
        isUpdatingItem: cart.isUpdatingItem,
        updateCart: updateItemInCart
      });
    }) : React.createElement(CartOptions, {
      cartItem: focusItem,
      configItem: {},
      closeOptionsDrawer: closeOptionsDrawer,
      isUpdatingItem: cart.isUpdatingItem,
      updateCart: updateItemInCart
    });
  }

  get miniCartInner() {
    const {
      checkout,
      productList,
      props
    } = this;
    const {
      classes,
      isCartEmpty,
      isMiniCartMaskOpen
    } = props;

    if (isCartEmpty) {
      return React.createElement(EmptyMiniCart, null);
    }

    const footer = checkout;
    const footerClassName = isMiniCartMaskOpen ? classes.footerMaskOpen : classes.footer;
    return React.createElement(Fragment, null, React.createElement("div", {
      className: classes.body
    }, productList), React.createElement("div", {
      className: footerClassName
    }, footer));
  }

  render() {
    const {
      miniCartInner,
      productOptions,
      props
    } = this;
    const {
      cancelCheckout,
      cart: {
        isOptionsDrawerOpen,
        isLoading
      },
      classes,
      isMiniCartMaskOpen,
      isOpen
    } = props;
    const className = isOpen ? classes.root_open : classes.root;
    const body = isOptionsDrawerOpen ? productOptions : miniCartInner;
    const title = isOptionsDrawerOpen ? 'Edit Cart Item' : 'Shopping Cart';
    return React.createElement("aside", {
      className: className
    }, React.createElement("div", {
      className: classes.header
    }, React.createElement("h2", {
      className: classes.title
    }, React.createElement("span", null, title)), React.createElement(Trigger, null, React.createElement(Icon, {
      src: CloseIcon
    }))), isLoading ? loadingIndicator : body, React.createElement(Mask, {
      isActive: isMiniCartMaskOpen,
      dismiss: cancelCheckout
    }));
  }

}

_defineProperty(MiniCart, "propTypes", {
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
  isMiniCartMaskOpen: bool
});

const mapStateToProps = state => {
  const {
    cart
  } = state;
  return {
    cart,
    isCartEmpty: isEmptyCartVisible(state),
    isMiniCartMaskOpen: isMiniCartMaskOpen(state)
  };
};

const mapDispatchToProps = {
  getCartDetails,
  updateItemInCart,
  removeItemFromCart,
  openOptionsDrawer,
  closeOptionsDrawer,
  cancelCheckout
};
export default compose(classify(defaultClasses), connect(mapStateToProps, mapDispatchToProps))(MiniCart);
//# sourceMappingURL=miniCart.js.map