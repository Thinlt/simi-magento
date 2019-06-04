function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, number, shape, string } from 'prop-types';
import classify from "../../classify";
import Select from "../Select";
import mockData from "./mockData";
import defaultClasses from "./quantity.css";

class Quantity extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      selectLabel
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["classes", "selectLabel"]);

    return React.createElement("div", {
      className: classes.root
    }, React.createElement(Select, _extends({}, restProps, {
      field: "quantity",
      "aria-label": selectLabel,
      items: mockData
    })));
  }

}

_defineProperty(Quantity, "propTypes", {
  classes: shape({
    root: string
  }),
  items: arrayOf(shape({
    value: number
  }))
});

_defineProperty(Quantity, "defaultProps", {
  selectLabel: "product's quantity"
});

export default classify(defaultClasses)(Quantity);
//# sourceMappingURL=quantity.js.map