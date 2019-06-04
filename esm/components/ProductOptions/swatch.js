function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, number, object, oneOfType, shape, string } from 'prop-types';
import classify from "../../classify";
import Icon from "../Icon";
import Tooltip from "./toolTip";
import CheckIcon from 'react-feather/dist/icons/check';
import defaultClasses from "./swatch.css";
import { memoizedGetRandomColor } from "../../util/getRandomColor";

const getClassName = (name, isSelected, hasFocus) => `${name}${isSelected ? '_selected' : ''}${hasFocus ? '_focused' : ''}`;

class Swatch extends Component {
  get icon() {
    const {
      isSelected
    } = this.props;
    return isSelected ? React.createElement(Icon, {
      src: CheckIcon
    }) : null;
  }

  render() {
    const {
      icon,
      props
    } = this;

    const {
      classes,
      hasFocus,
      isSelected,
      item,
      // eslint-disable-next-line
      itemIndex,
      style
    } = props,
          restProps = _objectWithoutProperties(props, ["classes", "hasFocus", "isSelected", "item", "itemIndex", "style"]);

    const className = classes[getClassName('root', isSelected, hasFocus)];
    const {
      label,
      value_index
    } = item; // TODO: use the colors from graphQL when they become available.

    const randomColor = memoizedGetRandomColor(value_index); // We really want to avoid specifying presentation within JS.
    // Swatches are unusual in that their color is data, not presentation,
    // but applying color *is* presentational.
    // So we merely provide the color data here, and let the CSS decide
    // how to use that color (e.g., background, border).

    const finalStyle = Object.assign({}, style, {
      '--venia-swatch-bg': randomColor
    });
    return React.createElement(Tooltip, {
      text: label
    }, React.createElement("button", _extends({}, restProps, {
      className: className,
      style: finalStyle,
      title: label
    }), icon));
  }

}

_defineProperty(Swatch, "propTypes", {
  classes: shape({
    root: string
  }),
  hasFocus: bool,
  isSelected: bool,
  item: shape({
    label: string.isRequired,
    value_index: oneOfType([number, string]).isRequired
  }).isRequired,
  itemIndex: number,
  style: object
});

_defineProperty(Swatch, "defaultProps", {
  hasFocus: false,
  isSelected: false
});

export default classify(defaultClasses)(Swatch);
//# sourceMappingURL=swatch.js.map