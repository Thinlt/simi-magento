function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, number, shape } from 'prop-types';
import GalleryItem from "./item";
const pageSize = 12;
const emptyData = Array.from({
  length: pageSize
}).fill(null); // inline the placeholder elements, since they're constant

const defaultPlaceholders = emptyData.map((_, index) => React.createElement(GalleryItem, {
  key: index,
  placeholder: true
}));

class GalleryItems extends Component {
  get placeholders() {
    const {
      pageSize
    } = this.props;
    return pageSize ? Array.from({
      length: pageSize
    }).fill(null).map((_, index) => React.createElement(GalleryItem, {
      key: index,
      placeholder: true
    })) : defaultPlaceholders;
  } // map Magento 2.3.1 schema changes to Venia 2.0.0 proptype shape to maintain backwards compatibility


  mapGalleryItem(item) {
    const {
      small_image
    } = item;
    return _objectSpread({}, item, {
      small_image: typeof small_image === 'object' ? small_image.url : small_image
    });
  }

  render() {
    const {
      items
    } = this.props;

    if (items === emptyData) {
      return this.placeholders;
    }

    return items.map(item => React.createElement(GalleryItem, {
      key: item.id,
      item: this.mapGalleryItem(item)
    }));
  }

}

_defineProperty(GalleryItems, "propTypes", {
  items: arrayOf(shape({
    id: number.isRequired
  })).isRequired,
  pageSize: number
});

export { GalleryItems as default, emptyData };
//# sourceMappingURL=items.js.map