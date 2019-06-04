function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./mask.css";

class Mask extends Component {
  render() {
    const {
      classes,
      dismiss,
      isActive
    } = this.props;
    const className = isActive ? classes.root_active : classes.root;
    return React.createElement("button", {
      className: className,
      onClick: dismiss
    });
  }

}

_defineProperty(Mask, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string,
    root_active: PropTypes.string
  }),
  dismiss: PropTypes.func,
  isActive: PropTypes.bool
});

export default classify(defaultClasses)(Mask);
//# sourceMappingURL=mask.js.map