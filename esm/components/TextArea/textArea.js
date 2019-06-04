function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { number, node, oneOf, oneOfType, shape, string } from 'prop-types';
import { BasicTextArea, asField } from 'informed';
import { compose } from 'redux';
import classify from "../../classify";
import { Message } from "../Field";
import defaultClasses from "./textArea.css";
export class TextArea extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      fieldState,
      message
    } = _this$props,
          rest = _objectWithoutProperties(_this$props, ["classes", "fieldState", "message"]);

    return React.createElement(Fragment, null, React.createElement(BasicTextArea, _extends({}, rest, {
      fieldState: fieldState,
      className: classes.input
    })), React.createElement(Message, {
      fieldState: fieldState
    }, message));
  }

}

_defineProperty(TextArea, "propTypes", {
  classes: shape({
    input: string
  }),
  cols: oneOfType([number, string]),
  field: string.isRequired,
  fieldState: shape({
    value: string
  }),
  message: node,
  rows: oneOfType([number, string]),
  wrap: oneOf(['hard', 'soft'])
});

_defineProperty(TextArea, "defaultProps", {
  cols: 40,
  rows: 4,
  wrap: 'hard'
});

export default compose(classify(defaultClasses), asField)(TextArea);
//# sourceMappingURL=textArea.js.map