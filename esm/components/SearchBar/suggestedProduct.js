function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func, number, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from "../../classify";
import { Link, resourceUrl } from "@magento/venia-drivers";
import defaultClasses from "./suggestedProduct.css";
const productUrlSuffix = '.html';

class SuggestedProduct extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleClick", () => {
      const {
        onNavigate
      } = this.props;

      if (typeof onNavigate === 'function') {
        onNavigate();
      }
    });
  }

  render() {
    const {
      handleClick,
      props
    } = this;
    const {
      classes,
      url_key,
      small_image,
      name,
      price
    } = props;
    const uri = resourceUrl(`/${url_key}${productUrlSuffix}`);
    return React.createElement(Link, {
      className: classes.root,
      to: uri,
      onClick: handleClick
    }, React.createElement("span", {
      className: classes.image
    }, React.createElement("img", {
      alt: name,
      src: resourceUrl(small_image, {
        type: 'image-product',
        width: 60
      })
    })), React.createElement("span", {
      className: classes.name
    }, name), React.createElement("span", {
      className: classes.price
    }, React.createElement(Price, {
      currencyCode: price.regularPrice.amount.currency,
      value: price.regularPrice.amount.value
    })));
  }

}

_defineProperty(SuggestedProduct, "propTypes", {
  url_key: string.isRequired,
  small_image: string.isRequired,
  name: string.isRequired,
  onNavigate: func,
  price: shape({
    regularPrice: shape({
      amount: shape({
        currency: string,
        value: number
      })
    })
  }).isRequired,
  classes: shape({
    root: string,
    image: string,
    name: string,
    price: string
  })
});

export default classify(defaultClasses)(SuggestedProduct);
//# sourceMappingURL=suggestedProduct.js.map