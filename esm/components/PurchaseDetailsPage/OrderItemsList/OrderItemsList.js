function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { array, func, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';
import classify from "../../../classify";
import OrderItem from "../OrderItem";
import defaultClasses from "./orderItemsList.css";

class OrderItemsList extends Component {
  render() {
    const {
      classes,
      items,
      onBuyItem,
      onReviewItem,
      onShareItem,
      title
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("h3", {
      className: classes.heading
    }, title), React.createElement(List, {
      items: items,
      getItemKey: ({
        id
      }) => id,
      render: props => React.createElement("div", {
        className: classes.list
      }, props.children),
      renderItem: props => React.createElement(OrderItem, _extends({}, props, {
        onBuyItem: onBuyItem,
        onReviewItem: onReviewItem,
        onShareItem: onShareItem
      }))
    }));
  }

}

_defineProperty(OrderItemsList, "propTypes", {
  classes: shape({
    heading: string,
    list: string,
    root: string
  }),
  items: array,
  onBuyItem: func,
  onReviewItem: func,
  onShareItem: func,
  title: string
});

export default classify(defaultClasses)(OrderItemsList);
//# sourceMappingURL=OrderItemsList.js.map