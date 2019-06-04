function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, func, shape, string } from 'prop-types';
import Mask from "../Mask";
import classify from "../../classify";
import defaultClasses from "./mask.css";

class MiniCartMask extends Component {
  render() {
    const {
      classes,
      dismiss,
      isActive
    } = this.props; // We're rendering the shared Mask component but passing it
    // our own custom class for its active state.

    return React.createElement(Mask, {
      classes: {
        root_active: classes.root_active
      },
      dismiss: dismiss,
      isActive: isActive
    });
  }

}

_defineProperty(MiniCartMask, "propTypes", {
  classes: shape({
    root_active: string
  }),
  dismiss: func,
  isActive: bool
});

export default classify(defaultClasses)(MiniCartMask);
//# sourceMappingURL=mask.js.map