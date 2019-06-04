function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, node, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./field.css";

class Field extends Component {
  get requiredSymbol() {
    const {
      classes,
      required
    } = this.props;
    return required ? React.createElement("span", {
      className: classes.requiredSymbol
    }) : null;
  }

  render() {
    const {
      children,
      classes,
      label
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("span", {
      className: classes.label
    }, this.requiredSymbol, label), children);
  }

}

_defineProperty(Field, "propTypes", {
  children: node,
  classes: shape({
    label: string,
    root: string
  }),
  label: node,
  required: bool
});

export default classify(defaultClasses)(Field);
//# sourceMappingURL=field.js.map