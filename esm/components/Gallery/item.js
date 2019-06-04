function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { string, number, shape } from 'prop-types';
import { Link, resourceUrl } from "@magento/venia-drivers";
import { Price } from '@magento/peregrine';
import classify from "../../classify";
import { transparentPlaceholder } from "../../shared/images";
import defaultClasses from "./item.css";
const imageWidth = '300';
const imageHeight = '372';

const ItemPlaceholder = ({
  children,
  classes
}) => React.createElement("div", {
  className: classes.root_pending
}, React.createElement("div", {
  className: classes.images_pending
}, children), React.createElement("div", {
  className: classes.name_pending
}), React.createElement("div", {
  className: classes.price_pending
})); // TODO: get productUrlSuffix from graphql when it is ready


const productUrlSuffix = '.html';

class GalleryItem extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "renderImagePlaceholder", () => {
      const {
        classes,
        item
      } = this.props;
      const className = item ? classes.imagePlaceholder : classes.imagePlaceholder_pending;
      return React.createElement("img", {
        className: className,
        src: transparentPlaceholder,
        alt: "",
        width: imageWidth,
        height: imageHeight
      });
    });

    _defineProperty(this, "renderImage", () => {
      const {
        classes,
        item
      } = this.props;

      if (!item) {
        return null;
      }

      const {
        small_image,
        name
      } = item;
      return React.createElement("img", {
        className: classes.image,
        src: resourceUrl(small_image, {
          type: 'image-product',
          width: imageWidth
        }),
        alt: name,
        width: imageWidth,
        height: imageHeight
      });
    });
  }

  render() {
    const {
      classes,
      item
    } = this.props;

    if (!item) {
      return React.createElement(ItemPlaceholder, {
        classes: classes
      }, this.renderImagePlaceholder());
    }

    const {
      name,
      price,
      url_key
    } = item;
    const productLink = `/${url_key}${productUrlSuffix}`;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement(Link, {
      to: resourceUrl(productLink),
      className: classes.images
    }, this.renderImagePlaceholder(), this.renderImage()), React.createElement(Link, {
      to: resourceUrl(productLink),
      className: classes.name
    }, React.createElement("span", null, name)), React.createElement("div", {
      className: classes.price
    }, React.createElement(Price, {
      value: price.regularPrice.amount.value,
      currencyCode: price.regularPrice.amount.currency
    })));
  }

}

_defineProperty(GalleryItem, "propTypes", {
  classes: shape({
    image: string,
    image_pending: string,
    imagePlaceholder: string,
    imagePlaceholder_pending: string,
    images: string,
    images_pending: string,
    name: string,
    name_pending: string,
    price: string,
    price_pending: string,
    root: string,
    root_pending: string
  }),
  item: shape({
    id: number.isRequired,
    name: string.isRequired,
    small_image: string.isRequired,
    url_key: string.isRequired,
    price: shape({
      regularPrice: shape({
        amount: shape({
          value: number.isRequired,
          currency: string.isRequired
        }).isRequired
      }).isRequired
    }).isRequired
  })
});

export default classify(defaultClasses)(GalleryItem);
//# sourceMappingURL=item.js.map