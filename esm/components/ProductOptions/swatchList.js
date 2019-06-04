function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, object, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';
import classify from "../../classify";
import Swatch from "./swatch";
import defaultClasses from "./swatchList.css";

class SwatchList extends Component {
  render() {
    return React.createElement(List, _extends({
      renderItem: Swatch
    }, this.props));
  }

}

_defineProperty(SwatchList, "propTypes", {
  classes: shape({
    root: string
  }),
  items: arrayOf(object)
});

export default classify(defaultClasses)(SwatchList);
//# sourceMappingURL=swatchList.js.map