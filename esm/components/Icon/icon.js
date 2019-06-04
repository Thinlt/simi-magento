function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func, number, object, oneOfType, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./icon.css";
/**
 * The Icon component allows us to wrap each icon with some default styling.
 */

class Icon extends Component {
  render() {
    const _this$props = this.props,
          {
      attrs: {
        width
      } = {},
      size,
      classes,
      src: IconComponent
    } = _this$props,
          restAttrs = _objectWithoutProperties(_this$props.attrs, ["width"]); // Permit both prop styles:
    // <Icon src={Foo} attrs={{ width: 18 }} />
    // <Icon src={Foo} size={18} />


    return React.createElement("span", {
      className: classes.root
    }, React.createElement(IconComponent, _extends({
      size: size || width
    }, restAttrs)));
  }

}

_defineProperty(Icon, "propTypes", {
  classes: shape({
    root: string
  }),
  size: number,
  attrs: object,
  src: oneOfType([func, shape({
    render: func.isRequired
  })]).isRequired
});

export default classify(defaultClasses)(Icon);
//# sourceMappingURL=icon.js.map