function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./trigger.css";

class Trigger extends Component {
  render() {
    const {
      action,
      children,
      classes
    } = this.props;
    return React.createElement("button", {
      className: classes.root,
      type: "button",
      onClick: action
    }, children);
  }

}

_defineProperty(Trigger, "propTypes", {
  action: PropTypes.func.isRequired,
  children: PropTypes.node,
  classes: PropTypes.shape({
    root: PropTypes.string
  })
});

export default classify(defaultClasses)(Trigger);
//# sourceMappingURL=trigger.js.map