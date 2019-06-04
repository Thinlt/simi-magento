function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { func, number, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import MessageSquareIcon from 'react-feather/dist/icons/message-square';
import ShoppingCartIcon from 'react-feather/dist/icons/shopping-cart';
import Share2Icon from 'react-feather/dist/icons/share-2';
import classify from "../../../classify";
import ButtonGroup from "../../ButtonGroup";
import Icon from "../../Icon";
import defaultClasses from "./orderItem.css";

const noop = () => {};

class OrderItem extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "buyItem", () => {
      const {
        item,
        onBuyItem
      } = this.props;
      onBuyItem(item);
    });

    _defineProperty(this, "reviewItem", () => {
      const {
        item,
        onReviewItem
      } = this.props;
      onReviewItem(item);
    });

    _defineProperty(this, "shareItem", () => {
      const {
        item,
        onShareItem
      } = this.props;
      onShareItem(item);
    });
  }

  get buyContent() {
    return React.createElement(Fragment, null, React.createElement(Icon, {
      src: ShoppingCartIcon,
      size: 12
    }), React.createElement("span", null, "Buy"));
  }

  get reviewContent() {
    return React.createElement(Fragment, null, React.createElement(Icon, {
      src: MessageSquareIcon,
      size: 12
    }), React.createElement("span", null, "Review"));
  }

  get shareContent() {
    return React.createElement(Fragment, null, React.createElement(Icon, {
      src: Share2Icon,
      size: 12
    }), React.createElement("span", null, "Share"));
  }

  render() {
    const {
      buyContent,
      buyItem,
      props,
      reviewContent,
      reviewItem,
      shareContent,
      shareItem
    } = this;
    const {
      classes,
      currencyCode,
      item
    } = props;
    const {
      color,
      name,
      price,
      qty,
      size,
      titleImageSrc
    } = item;
    const buttonGroupItems = [{
      key: 'buy',
      onClick: buyItem,
      children: buyContent
    }, {
      key: 'share',
      onClick: shareItem,
      children: shareContent
    }, {
      key: 'review',
      onClick: reviewItem,
      children: reviewContent
    }];
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.main
    }, React.createElement("img", {
      className: classes.image,
      src: titleImageSrc,
      alt: "itemOfClothes"
    }), React.createElement("dl", {
      className: classes.propsList
    }, React.createElement("dt", {
      className: classes.propLabel
    }, "Name"), React.createElement("dd", {
      className: classes.propValue
    }, name), React.createElement("dt", {
      className: classes.propLabel
    }, "Size"), React.createElement("dd", {
      className: classes.propValue
    }, size), React.createElement("dt", {
      className: classes.propLabel
    }, "Color"), React.createElement("dd", {
      className: classes.propValue
    }, color), React.createElement("dt", {
      className: classes.propLabel
    }, "Quantity"), React.createElement("dd", {
      className: classes.propValue
    }, qty)), React.createElement("div", {
      className: classes.price
    }, React.createElement(Price, {
      value: price || 0,
      currencyCode: currencyCode
    }))), React.createElement(ButtonGroup, {
      items: buttonGroupItems
    }));
  }

}

_defineProperty(OrderItem, "propTypes", {
  classes: shape({
    image: string,
    main: string,
    price: string,
    propLabel: string,
    propValue: string,
    propsList: string,
    root: string
  }),
  currencyCode: string,
  item: shape({
    color: string,
    id: number,
    name: string,
    price: number,
    qty: number,
    size: string,
    sku: string,
    titleImageSrc: string
  }),
  onBuyItem: func,
  onReviewItem: func,
  onShareItem: func
});

_defineProperty(OrderItem, "defaultProps", {
  item: {},
  currencyCode: 'USD',
  onBuyItem: noop,
  onReviewItem: noop,
  onShareItem: noop
});

export default classify(defaultClasses)(OrderItem);
//# sourceMappingURL=orderItem.js.map