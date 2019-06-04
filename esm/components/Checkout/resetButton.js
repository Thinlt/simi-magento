function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func } from 'prop-types';
import Button from "../Button";

class ResetButton extends Component {
  render() {
    const {
      resetCheckout
    } = this.props;
    return React.createElement(Button, {
      onClick: resetCheckout
    }, "Continue Shopping");
  }

}

_defineProperty(ResetButton, "propTypes", {
  resetCheckout: func.isRequired
});

export default ResetButton;
//# sourceMappingURL=resetButton.js.map