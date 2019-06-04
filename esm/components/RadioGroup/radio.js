function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { node, shape, string } from 'prop-types';
import { Radio } from 'informed';
import classify from "../../classify";
import defaultClasses from "./radio.css";
/* TODO: change lint config to use `label-has-associated-control` */

/* eslint-disable jsx-a11y/label-has-for */

export class RadioOption extends Component {
  render() {
    const {
      props
    } = this;

    const {
      classes,
      id,
      label,
      value
    } = props,
          rest = _objectWithoutProperties(props, ["classes", "id", "label", "value"]);

    return React.createElement("label", {
      className: classes.root,
      htmlFor: id
    }, React.createElement(Radio, _extends({}, rest, {
      className: classes.input,
      id: id,
      value: value
    })), React.createElement("span", {
      className: classes.label
    }, label || (value != null ? value : '')));
  }

}
/* eslint-enable jsx-a11y/label-has-for */

_defineProperty(RadioOption, "propTypes", {
  classes: shape({
    input: string,
    label: string,
    root: string
  }),
  label: node.isRequired,
  value: node.isRequired
});

export default classify(defaultClasses)(RadioOption);
//# sourceMappingURL=radio.js.map