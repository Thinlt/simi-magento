function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, number, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';
import classify from "../../classify";
import Product from "./product";
import defaultClasses from "./productList.css";

class ProductList extends Component {
  render() {
    const _this$props = this.props,
          {
      currencyCode,
      removeItemFromCart,
      openOptionsDrawer,
      totalsItems
    } = _this$props,
          otherProps = _objectWithoutProperties(_this$props, ["currencyCode", "removeItemFromCart", "openOptionsDrawer", "totalsItems"]);

    return React.createElement(List, _extends({
      render: "ul",
      getItemKey: item => item.item_id,
      renderItem: props => React.createElement(Product, _extends({
        currencyCode: currencyCode,
        removeItemFromCart: removeItemFromCart,
        openOptionsDrawer: openOptionsDrawer,
        totalsItems: totalsItems
      }, props))
    }, otherProps));
  }

}

_defineProperty(ProductList, "propTypes", {
  classes: shape({
    root: string
  }),
  items: arrayOf(shape({
    item_id: number.isRequired,
    name: string.isRequired,
    price: number.isRequired,
    product_type: string,
    qty: number.isRequired,
    quote_id: string,
    sku: string.isRequired
  })).isRequired,
  currencyCode: string.isRequired
});

export default classify(defaultClasses)(ProductList);
//# sourceMappingURL=productList.js.map