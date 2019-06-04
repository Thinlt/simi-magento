function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, object, shape, string } from 'prop-types';
import { List } from '@magento/peregrine';
import classify from "../../classify";
import Tile from "./tile";
import defaultClasses from "./tileList.css";

class TileList extends Component {
  render() {
    return React.createElement(List, _extends({
      renderItem: Tile
    }, this.props));
  }

}

_defineProperty(TileList, "propTypes", {
  classes: shape({
    root: string
  }),
  items: arrayOf(object)
});

export default classify(defaultClasses)(TileList);
//# sourceMappingURL=tileList.js.map