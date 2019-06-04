function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { oneOf, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./button.css";

const getRootClassName = priority => `root_${priority}Priority`;

export class Button extends Component {
  render() {
    const _this$props = this.props,
          {
      children,
      classes,
      priority,
      type
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["children", "classes", "priority", "type"]);

    const rootClassName = classes[getRootClassName(priority)];
    return React.createElement("button", _extends({
      className: rootClassName,
      type: type
    }, restProps), React.createElement("span", {
      className: classes.content
    }, children));
  }

}

_defineProperty(Button, "propTypes", {
  classes: shape({
    content: string,
    root: string,
    root_highPriority: string,
    root_normalPriority: string
  }).isRequired,
  priority: oneOf(['high', 'normal']).isRequired,
  type: oneOf(['button', 'reset', 'submit']).isRequired
});

_defineProperty(Button, "defaultProps", {
  priority: 'normal',
  type: 'button'
});

export default classify(defaultClasses)(Button);
//# sourceMappingURL=button.js.map