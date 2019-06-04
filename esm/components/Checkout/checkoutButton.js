function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, func } from 'prop-types';
import Button from "../Button";
import Icon from "../Icon";
import LockIcon from 'react-feather/dist/icons/lock';

const isDisabled = (busy, valid) => busy || !valid;

class CheckoutButton extends Component {
  render() {
    const {
      ready,
      submit,
      submitting
    } = this.props;
    const disabled = isDisabled(submitting, ready);
    return React.createElement(Button, {
      priority: "high",
      disabled: disabled,
      onClick: submit
    }, React.createElement(Icon, {
      src: LockIcon,
      size: 16
    }), React.createElement("span", null, "Checkout"));
  }

}

_defineProperty(CheckoutButton, "propTypes", {
  ready: bool.isRequired,
  submit: func,
  submitting: bool
});

export default CheckoutButton;
//# sourceMappingURL=checkoutButton.js.map