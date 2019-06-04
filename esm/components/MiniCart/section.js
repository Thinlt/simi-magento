function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func, object, oneOf, shape, string } from 'prop-types';
import Icon from "../Icon";
import HeartIcon from 'react-feather/dist/icons/heart';
import Edit2Icon from 'react-feather/dist/icons/edit-2';
import TrashIcon from 'react-feather/dist/icons/trash';
import classify from "../../classify";
import defaultClasses from "./section.css";
const SectionIcons = {
  Heart: HeartIcon,
  Edit2: Edit2Icon,
  Trash: TrashIcon
};

class Section extends Component {
  get icon() {
    const {
      icon
    } = this.props;
    const defaultAttributes = {
      color: 'rgb(var(--venia-teal))',
      width: '14px',
      height: '14px'
    };
    const iconAttributes = this.props.iconAttributes ? Object.assign(defaultAttributes, this.props.iconAttributes) : defaultAttributes;
    return icon ? React.createElement(Icon, {
      src: SectionIcons[icon],
      attrs: iconAttributes
    }) : null;
  }

  render() {
    const {
      icon
    } = this;
    const {
      classes,
      onClick,
      text
    } = this.props;
    return React.createElement("li", {
      className: classes.menuItem
    }, React.createElement("button", {
      onClick: onClick
    }, icon, React.createElement("span", {
      className: classes.text
    }, text)));
  }

}

_defineProperty(Section, "propTypes", {
  classes: shape({
    menuItem: string,
    text: string
  }),
  icon: oneOf(['Heart', 'Edit2', 'Trash']),
  iconAttributes: object,
  onClick: func,
  text: string
});

export default classify(defaultClasses)(Section);
//# sourceMappingURL=section.js.map