function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, func, shape, string } from 'prop-types';
import classify from "../../classify";
import CheckoutButton from "./checkoutButton";
import defaultClasses from "./cart.css";

class Cart extends Component {
  render() {
    const {
      beginCheckout,
      classes,
      ready,
      submitting
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.footer
    }, React.createElement(CheckoutButton, {
      ready: ready,
      submitting: submitting,
      submit: beginCheckout
    })));
  }

}

_defineProperty(Cart, "propTypes", {
  beginCheckout: func.isRequired,
  classes: shape({
    root: string
  }),
  ready: bool.isRequired,
  submitting: bool.isRequired
});

export default classify(defaultClasses)(Cart);
//# sourceMappingURL=cart.js.map