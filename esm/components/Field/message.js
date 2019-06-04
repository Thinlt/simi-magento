function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { node, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./message.css";
export class Message extends Component {
  render() {
    const {
      children,
      classes,
      fieldState
    } = this.props;
    const {
      asyncError,
      error
    } = fieldState;
    const errorMessage = error || asyncError;
    const className = errorMessage ? classes.root_error : classes.root;
    return React.createElement("p", {
      className: className
    }, errorMessage || children);
  }

}

_defineProperty(Message, "propTypes", {
  children: node,
  classes: shape({
    root: string,
    root_error: string
  }),
  fieldState: shape({
    asyncError: string,
    error: string
  })
});

export default classify(defaultClasses)(Message);
//# sourceMappingURL=message.js.map