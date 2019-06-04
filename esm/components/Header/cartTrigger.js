function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { connect } from "@magento/venia-drivers";
import { compose } from 'redux';
import PropTypes from 'prop-types';
import { toggleCart } from "../../actions/cart";
import CartCounter from "./cartCounter";
import Icon from "../Icon";
import ShoppingCartIcon from 'react-feather/dist/icons/shopping-cart';
import classify from "../../classify";
import defaultClasses from "./cartTrigger.css";
export class Trigger extends Component {
  get cartIcon() {
    const {
      cart: {
        details
      }
    } = this.props;
    const itemsQty = details.items_qty;
    const iconColor = 'rgb(var(--venia-text))';
    const svgAttributes = {
      stroke: iconColor
    };

    if (itemsQty > 0) {
      svgAttributes.fill = iconColor;
    }

    return React.createElement(Icon, {
      src: ShoppingCartIcon,
      attrs: svgAttributes
    });
  }

  render() {
    const {
      classes,
      toggleCart,
      cart: {
        details
      }
    } = this.props;
    const {
      cartIcon
    } = this;
    const itemsQty = details.items_qty;
    return React.createElement("button", {
      className: classes.root,
      "aria-label": "Toggle mini cart",
      onClick: toggleCart
    }, cartIcon, React.createElement(CartCounter, {
      counter: itemsQty ? itemsQty : 0
    }));
  }

}

_defineProperty(Trigger, "propTypes", {
  children: PropTypes.node,
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  toggleCart: PropTypes.func.isRequired,
  itemsQty: PropTypes.number
});

const mapStateToProps = ({
  cart
}) => ({
  cart
});

const mapDispatchToProps = {
  toggleCart
};
export default compose(classify(defaultClasses), connect(mapStateToProps, mapDispatchToProps))(Trigger);
//# sourceMappingURL=cartTrigger.js.map