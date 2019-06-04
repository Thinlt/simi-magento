function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import classify from "../../classify";
import defaultClasses from "./navButton.css";
import Icon from "../Icon";
import RewindIcon from 'react-feather/dist/icons/rewind';
import ChevronLeftIcon from 'react-feather/dist/icons/chevron-left';
import ChevronRightIcon from 'react-feather/dist/icons/chevron-right';
import FastForwardIcon from 'react-feather/dist/icons/fast-forward';
const NavIcons = {
  Rewind: RewindIcon,
  ChevronLeft: ChevronLeftIcon,
  ChevronRight: ChevronRightIcon,
  FastForward: FastForwardIcon
};
const defaultSkipAttributes = {
  width: '1.2rem',
  height: '1.2rem'
};
const activeFill = {
  fill: '#000'
};
const inactiveFill = {
  fill: '#999'
};

class NavButton extends Component {
  render() {
    const {
      classes,
      name,
      active,
      onClick,
      buttonLabel
    } = this.props;
    let attrs; // The chevron icon does not have a fill or any sizing issues that
    // need to be handled with attributes in props

    if (name.includes('Chevron')) {
      attrs = {};
    } else {
      attrs = active ? _objectSpread({}, defaultSkipAttributes, activeFill) : _objectSpread({}, defaultSkipAttributes, inactiveFill);
    }

    const className = active ? classes.buttonArrow : classes.buttonInactive;
    return React.createElement("button", {
      className: className,
      "aria-label": buttonLabel,
      onClick: onClick
    }, React.createElement(Icon, {
      src: NavIcons[name],
      attrs: attrs
    }));
  }

}

_defineProperty(NavButton, "defaultProps", {
  buttonLabel: 'move to another page'
});

export default classify(defaultClasses)(NavButton);
//# sourceMappingURL=navButton.js.map