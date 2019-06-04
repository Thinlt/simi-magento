function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, node, shape, string } from 'prop-types';
import classify from "../../classify";
import Button from "./button";
import defaultClasses from "./buttonGroup.css";

class ButtonGroup extends Component {
  render() {
    const {
      classes,
      items
    } = this.props;
    const children = Array.from(items, (_ref) => {
      let {
        key
      } = _ref,
          itemProps = _objectWithoutProperties(_ref, ["key"]);

      return React.createElement(Button, _extends({
        key: key
      }, itemProps));
    });
    return React.createElement("div", {
      className: classes.root
    }, children);
  }

}

_defineProperty(ButtonGroup, "propTypes", {
  classes: shape({
    root: string
  }).isRequired,
  items: arrayOf(shape({
    children: node.isRequired,
    key: string.isRequired
  })).isRequired
});

_defineProperty(ButtonGroup, "defaultProps", {
  items: []
});

export default classify(defaultClasses)(ButtonGroup);
//# sourceMappingURL=buttonGroup.js.map