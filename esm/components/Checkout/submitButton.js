function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, func } from 'prop-types';
import Button from "../Button";

const isDisabled = (busy, valid) => busy || !valid;

class SubmitButton extends Component {
  render() {
    const {
      submitOrder,
      submitting,
      valid
    } = this.props;
    const disabled = isDisabled(submitting, valid);
    return React.createElement(Button, {
      priority: "high",
      disabled: disabled,
      onClick: submitOrder
    }, "Confirm Order");
  }

}

_defineProperty(SubmitButton, "propTypes", {
  submitOrder: func.isRequired,
  submitting: bool.isRequired,
  valid: bool.isRequired
});

export default SubmitButton;
//# sourceMappingURL=submitButton.js.map