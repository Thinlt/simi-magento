function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { string, shape, array, number } from 'prop-types';
import classify from "../../classify";
import GalleryItems, { emptyData } from "./items";
import defaultClasses from "./gallery.css";

class Gallery extends Component {
  render() {
    const {
      classes,
      data,
      pageSize
    } = this.props;
    const hasData = Array.isArray(data) && data.length;
    const items = hasData ? data : emptyData;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.items
    }, React.createElement(GalleryItems, {
      items: items,
      pageSize: pageSize
    })));
  }

}

_defineProperty(Gallery, "propTypes", {
  classes: shape({
    filters: string,
    items: string,
    pagination: string,
    root: string
  }),
  data: array,
  pageSize: number
});

_defineProperty(Gallery, "defaultProps", {
  data: emptyData
});

export default classify(defaultClasses)(Gallery);
//# sourceMappingURL=gallery.js.map