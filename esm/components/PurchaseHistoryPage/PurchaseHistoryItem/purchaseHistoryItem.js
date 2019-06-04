function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { number, oneOfType, shape, string } from 'prop-types';
import { Link, resourceUrl } from "@magento/venia-drivers";
import Icon from "../../Icon";
import ChevronRightIcon from 'react-feather/dist/icons/chevron-right';
import classify from "../../../classify";
import defaultClasses from "./purchaseHistoryItem.css";
import { processDate } from "./helpers";
const CHEVRON_ICON_ATTRS = {
  width: 18,
  'stroke-width': 2
};

class PurchaseHistoryItem extends Component {
  render() {
    const {
      classes,
      item
    } = this.props;
    const {
      imageSrc,
      title,
      date,
      url
    } = item || {};
    return React.createElement(Link, {
      className: classes.body,
      to: resourceUrl(url)
    }, React.createElement("img", {
      className: classes.image,
      src: imageSrc,
      alt: "item"
    }), React.createElement("div", {
      className: classes.textBlock
    }, React.createElement("div", {
      className: classes.textBlockTitle
    }, title), React.createElement("div", {
      className: classes.textBlockDate
    }, processDate(date))), React.createElement("div", {
      className: classes.chevronContainer
    }, React.createElement(Icon, {
      src: ChevronRightIcon,
      attrs: CHEVRON_ICON_ATTRS
    })));
  }

}

_defineProperty(PurchaseHistoryItem, "propTypes", {
  classes: shape({
    body: string,
    textBlock: string,
    textBlockTitle: string,
    textBlockDate: string,
    chevronContainer: string
  }),
  item: shape({
    id: number.isRequired,
    imageSrc: string.isRequired,
    title: string.isRequired,
    date: oneOfType([number, string]),
    url: string
  }).isRequired
});

export default classify(defaultClasses)(PurchaseHistoryItem);
//# sourceMappingURL=purchaseHistoryItem.js.map