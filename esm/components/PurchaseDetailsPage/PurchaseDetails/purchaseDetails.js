function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { any, array, bool, func, shape, string } from 'prop-types';
import classify from "../../../classify";
import Button from "../../Button";
import { loadingIndicator } from "../../LoadingIndicator";
import OrderItem from "../OrderItem";
import OrderItemsList from "../OrderItemsList";
import DetailsBlock from "../DetailsBlock";
import defaultClasses from "./purchaseDetails.css";

class PurchaseDetails extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleBuyItem", () => {// TODO: wire up
    });

    _defineProperty(this, "handleReviewItem", () => {// TODO: wire up
    });

    _defineProperty(this, "handleShareItem", () => {// TODO: wire up
    });
  }

  componentDidMount() {
    // TODO: include orderId, itemId
    this.props.fetchOrderDetails({});
  }

  render() {
    const {
      classes,
      isFetching,
      item,
      orderDetails,
      orderSummary,
      otherItems,
      paymentDetails,
      shipmentDetails
    } = this.props;

    if (isFetching) {
      return loadingIndicator;
    }

    return React.createElement("div", {
      className: classes.root
    }, React.createElement(OrderItem, {
      item: item,
      onBuyItem: this.handleBuyItem,
      onReviewItem: this.handleReviewItem,
      onShareItem: this.handleShareItem
    }), React.createElement("h2", {
      className: classes.heading
    }, "Order Details"), React.createElement(DetailsBlock, {
      rows: orderDetails
    }), React.createElement(OrderItemsList, {
      items: otherItems,
      title: "Other Items in this Order",
      onBuyItem: this.handleBuyItem,
      onReviewItem: this.handleReviewItem,
      onShareItem: this.handleShareItem
    }), React.createElement("h3", {
      className: classes.heading
    }, "Shipment Details"), React.createElement(DetailsBlock, {
      rows: shipmentDetails
    }), React.createElement("div", {
      className: classes.shipmentActions
    }, React.createElement(Button, null, "Track Order")), React.createElement("h3", {
      className: classes.heading
    }, "Payment Details"), React.createElement(DetailsBlock, {
      rows: paymentDetails
    }), React.createElement("h3", {
      className: classes.heading
    }, "Order Summary"), React.createElement(DetailsBlock, {
      rows: orderSummary
    }));
  }

}

_defineProperty(PurchaseDetails, "propTypes", {
  classes: shape({
    heading: string,
    root: string,
    shipmentActions: string
  }).isRequired,
  fetchOrderDetails: func.isRequired,
  isFetching: bool,
  item: any,
  orderDetails: array,
  orderSummary: array,
  otherItems: array,
  paymentDetails: array,
  shipmentDetails: array
});

export default classify(defaultClasses)(PurchaseDetails);
//# sourceMappingURL=purchaseDetails.js.map