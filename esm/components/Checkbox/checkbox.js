function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { bool, node, shape, string } from 'prop-types';
import { BasicCheckbox, asField } from 'informed';
import { compose } from 'redux';
import classify from "../../classify";
import { Message } from "../Field";
import Icon from "../Icon";
import CheckIcon from 'react-feather/dist/icons/check';
import defaultClasses from "./checkbox.css";
/* TODO: change lint config to use `label-has-associated-control` */

/* eslint-disable jsx-a11y/label-has-for */

export class Checkbox extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      fieldState,
      id,
      label,
      message
    } = _this$props,
          rest = _objectWithoutProperties(_this$props, ["classes", "fieldState", "id", "label", "message"]);

    const {
      value: checked
    } = fieldState;
    return React.createElement(Fragment, null, React.createElement("label", {
      className: classes.root,
      htmlFor: id
    }, React.createElement("span", {
      className: classes.icon
    }, checked && React.createElement(Icon, {
      src: CheckIcon,
      size: 18
    })), React.createElement(BasicCheckbox, _extends({}, rest, {
      className: classes.input,
      fieldState: fieldState,
      id: id
    })), React.createElement("span", {
      className: classes.label
    }, label)), React.createElement(Message, {
      fieldState: fieldState
    }, message));
  }

}
/* eslint-enable jsx-a11y/label-has-for */

_defineProperty(Checkbox, "propTypes", {
  classes: shape({
    icon: string,
    input: string,
    label: string,
    message: string,
    root: string
  }),
  field: string.isRequired,
  fieldState: shape({
    value: bool
  }).isRequired,
  id: string,
  label: node.isRequired,
  message: node
});

export default compose(classify(defaultClasses), asField)(Checkbox);
//# sourceMappingURL=checkbox.js.map