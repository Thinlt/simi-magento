function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { arrayOf, node, shape, string } from 'prop-types';
import { BasicRadioGroup, asField } from 'informed';
import { compose } from 'redux';
import classify from "../../classify";
import { Message } from "../Field";
import Radio from "./radio";
import defaultClasses from "./radioGroup.css";
export class RadioGroup extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      fieldState,
      items,
      message
    } = _this$props,
          rest = _objectWithoutProperties(_this$props, ["classes", "fieldState", "items", "message"]);

    const options = items.map(({
      label,
      value
    }) => React.createElement(Radio, {
      key: value,
      label: label,
      value: value
    }));
    return React.createElement(Fragment, null, React.createElement("div", {
      className: classes.root
    }, React.createElement(BasicRadioGroup, _extends({}, rest, {
      fieldState: fieldState
    }), options)), React.createElement(Message, {
      fieldState: fieldState
    }, message));
  }

}

_defineProperty(RadioGroup, "propTypes", {
  classes: shape({
    message: string,
    root: string
  }),
  fieldState: shape({
    value: string
  }),
  items: arrayOf(shape({
    label: string,
    value: string
  })),
  message: node
});

export default compose(classify(defaultClasses), asField)(RadioGroup);
//# sourceMappingURL=radioGroup.js.map