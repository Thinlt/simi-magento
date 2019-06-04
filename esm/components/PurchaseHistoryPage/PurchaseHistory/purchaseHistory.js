function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, bool, func, number, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';
import PurchaseHistoryItem from "../PurchaseHistoryItem";
import classify from "../../../classify";
import defaultClasses from "./purchaseHistory.css";
import Filter from "../Filter";

class PurchaseHistory extends Component {
  componentDidMount() {
    const {
      getPurchaseHistory
    } = this.props;
    getPurchaseHistory();
  }

  componentWillUnmount() {
    const {
      resetPurchaseHistory
    } = this.props;
    resetPurchaseHistory();
  }

  get purchaseHistoryList() {
    const {
      classes,
      items,
      isFetching
    } = this.props;

    if (isFetching) {
      return 'Loading...';
    }

    return React.createElement(List, {
      items: items,
      getItemKey: ({
        id
      }) => id,
      render: props => React.createElement("ul", {
        className: classes.itemsContainer
      }, props.children),
      renderItem: props => React.createElement("li", {
        className: classes.item
      }, React.createElement(PurchaseHistoryItem, props))
    });
  }

  render() {
    const {
      purchaseHistoryList
    } = this;
    const {
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.body
    }, React.createElement("div", {
      className: classes.filterContainer
    }, React.createElement(Filter, null)), purchaseHistoryList);
  }

}

_defineProperty(PurchaseHistory, "propTypes", {
  classes: shape({
    body: string,
    item: string,
    filterContainer: string,
    itemsContainer: string
  }),
  getPurchaseHistory: func.isRequired,
  isFetching: bool,
  items: arrayOf(shape({
    id: number.isRequired
  })),
  resetPurchaseHistory: func.isRequired
});

export default classify(defaultClasses)(PurchaseHistory);
//# sourceMappingURL=purchaseHistory.js.map