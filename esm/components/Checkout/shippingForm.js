function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { Form } from 'informed';
import { array, bool, func, shape, string } from 'prop-types';
import Button from "../Button";
import Label from "./label";
import Select from "../Select";
import classify from "../../classify";
import defaultClasses from "./shippingForm.css";

class ShippingForm extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "cancel", () => {
      this.props.cancel();
    });

    _defineProperty(this, "submit", ({
      shippingMethod
    }) => {
      const selectedShippingMethod = this.props.availableShippingMethods.find(({
        carrier_code
      }) => carrier_code === shippingMethod);

      if (!selectedShippingMethod) {
        console.warn(`Could not find the selected shipping method ${selectedShippingMethod} in the list of available shipping methods.`);
        this.cancel();
        return;
      }

      this.props.submit({
        shippingMethod: selectedShippingMethod
      });
    });
  }

  render() {
    const {
      availableShippingMethods,
      classes,
      shippingMethod,
      submitting
    } = this.props;
    let initialValue;
    let selectableShippingMethods;

    if (availableShippingMethods.length) {
      selectableShippingMethods = availableShippingMethods.map(({
        carrier_code,
        carrier_title
      }) => ({
        label: carrier_title,
        value: carrier_code
      }));
      initialValue = shippingMethod || availableShippingMethods[0].carrier_code;
    } else {
      selectableShippingMethods = [];
      initialValue = '';
    }

    return React.createElement(Form, {
      className: classes.root,
      onSubmit: this.submit
    }, React.createElement("div", {
      className: classes.body
    }, React.createElement("h2", {
      className: classes.heading
    }, "Shipping Information"), React.createElement("div", {
      className: classes.shippingMethod
    }, React.createElement(Label, {
      htmlFor: classes.shippingMethod
    }, "Shipping Method"), React.createElement(Select, {
      field: "shippingMethod",
      initialValue: initialValue,
      items: selectableShippingMethods
    }))), React.createElement("div", {
      className: classes.footer
    }, React.createElement(Button, {
      className: classes.button,
      onClick: this.cancel
    }, "Cancel"), React.createElement(Button, {
      className: classes.button,
      priority: "high",
      type: "submit",
      disabled: submitting
    }, "Use Method")));
  }

}

_defineProperty(ShippingForm, "propTypes", {
  availableShippingMethods: array.isRequired,
  cancel: func.isRequired,
  classes: shape({
    body: string,
    button: string,
    footer: string,
    heading: string,
    shippingMethod: string
  }),
  shippingMethod: string,
  submit: func.isRequired,
  submitting: bool
});

_defineProperty(ShippingForm, "defaultProps", {
  availableShippingMethods: [{}]
});

export default classify(defaultClasses)(ShippingForm);
//# sourceMappingURL=shippingForm.js.map