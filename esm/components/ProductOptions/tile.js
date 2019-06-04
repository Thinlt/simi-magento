function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, number, shape, string } from 'prop-types';
import classify from "../../classify";
import Tooltip from "./toolTip";
import defaultClasses from "./tile.css";

const getClassName = (name, isSelected, hasFocus) => `${name}${isSelected ? '_selected' : ''}${hasFocus ? '_focused' : ''}`;

class Tile extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      hasFocus,
      isSelected,
      item,
      // eslint-disable-next-line
      itemIndex
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["classes", "hasFocus", "isSelected", "item", "itemIndex"]);

    const className = classes[getClassName('root', isSelected, hasFocus)];
    const {
      label
    } = item;
    return React.createElement(Tooltip, {
      text: label
    }, React.createElement("button", _extends({}, restProps, {
      className: className
    }), React.createElement("span", null, label)));
  }

}

_defineProperty(Tile, "propTypes", {
  classes: shape({
    root: string
  }),
  hasFocus: bool,
  isSelected: bool,
  item: shape({
    label: string.isRequired
  }).isRequired,
  itemIndex: number
});

_defineProperty(Tile, "defaultProps", {
  hasFocus: false,
  isSelected: false
});

export default classify(defaultClasses)(Tile);
//# sourceMappingURL=tile.js.map