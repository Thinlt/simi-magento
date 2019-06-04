function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

import React from 'react';
import MenuItem from "./menuItem";
import { Link } from "@magento/venia-drivers";

const MenuItemButton = props => React.createElement(MenuItem, _extends({
  component: "button",
  type: "button"
}, props));

const MenuItemLink = props => React.createElement(MenuItem, _extends({
  component: Link
}, props));

export default {
  Link: MenuItemLink,
  Button: MenuItemButton
};
//# sourceMappingURL=index.js.map