function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { arrayOf, node, number, oneOfType, shape, string } from 'prop-types';
import { BasicSelect, Option, asField } from 'informed';
import { compose } from 'redux';
import classify from "../../classify";
import { FieldIcons, Message } from "../Field";
import defaultClasses from "./select.css";
import Icon from "../Icon";
import ChevronDownIcon from 'react-feather/dist/icons/chevron-down';
const arrow = React.createElement(Icon, {
  src: ChevronDownIcon,
  size: 18
});

class Select extends Component {
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
    }) => React.createElement(Option, {
      key: value,
      value: value
    }, label || (value != null ? value : '')));
    return React.createElement(Fragment, null, React.createElement(FieldIcons, {
      after: arrow
    }, React.createElement(BasicSelect, _extends({}, rest, {
      fieldState: fieldState,
      className: classes.input
    }), options)), React.createElement(Message, {
      fieldState: fieldState
    }, message));
  }

}

_defineProperty(Select, "propTypes", {
  classes: shape({
    input: string
  }),
  field: string.isRequired,
  fieldState: shape({
    value: oneOfType([number, string])
  }),
  items: arrayOf(shape({
    label: string,
    value: oneOfType([number, string])
  })),
  message: node
});

export default compose(classify(defaultClasses), asField)(Select);
//# sourceMappingURL=select.js.map