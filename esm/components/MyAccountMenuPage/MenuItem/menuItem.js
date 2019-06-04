function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import defaultClasses from "./menuItem.css";

class MenuItem extends Component {
  render() {
    const _this$props = this.props,
          {
      classes,
      component: ContainerComponent,
      title,
      badge
    } = _this$props,
          props = _objectWithoutProperties(_this$props, ["classes", "component", "title", "badge"]);

    return React.createElement(ContainerComponent, _extends({}, props, {
      className: classes.item
    }), title, badge);
  }

}

_defineProperty(MenuItem, "propTypes", {
  classes: PropTypes.shape({
    item: PropTypes.string
  }),
  component: PropTypes.oneOfType([PropTypes.func, PropTypes.string]),
  title: PropTypes.node,
  badge: PropTypes.node
});

_defineProperty(MenuItem, "defaultProps", {
  component: 'button'
});

export default classify(defaultClasses)(MenuItem);
//# sourceMappingURL=menuItem.js.map