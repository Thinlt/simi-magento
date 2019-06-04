function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, node, shape, string } from 'prop-types';
import EditIcon from 'react-feather/dist/icons/edit-2';
import classify from "../../classify";
import Icon from "../Icon";
import defaultClasses from "./section.css"; // TODO: move these attributes to CSS.

const editIconAttrs = {
  color: 'black',
  width: 18
};
const EDIT_ICON = React.createElement(Icon, {
  src: EditIcon,
  attrs: editIconAttrs
});

class Section extends Component {
  render() {
    const _this$props = this.props,
          {
      children,
      classes,
      label,
      showEditIcon
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["children", "classes", "label", "showEditIcon"]);

    const icon = showEditIcon ? EDIT_ICON : null;
    return React.createElement("button", _extends({
      className: classes.root
    }, restProps), React.createElement("span", {
      className: classes.content
    }, React.createElement("span", {
      className: classes.label
    }, React.createElement("span", null, label)), React.createElement("span", {
      className: classes.summary
    }, children), React.createElement("span", {
      className: classes.icon
    }, icon)));
  }

}

_defineProperty(Section, "propTypes", {
  classes: shape({
    content: string,
    icon: string,
    label: string,
    root: string,
    summary: string
  }),
  label: node,
  showEditIcon: bool
});

export default classify(defaultClasses)(Section);
//# sourceMappingURL=section.js.map