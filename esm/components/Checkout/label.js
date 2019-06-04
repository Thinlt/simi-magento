function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, node, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./label.css";

class Label extends Component {
  render() {
    const _this$props = this.props,
          {
      children,
      classes,
      plain
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["children", "classes", "plain"]);

    const elementType = plain ? 'span' : 'label';

    const labelProps = _objectSpread({}, restProps, {
      className: classes.root
    });

    return React.createElement(elementType, labelProps, children);
  }

}

_defineProperty(Label, "propTypes", {
  children: node,
  classes: shape({
    root: string
  }),
  plain: bool
});

export default classify(defaultClasses)(Label);
//# sourceMappingURL=label.js.map