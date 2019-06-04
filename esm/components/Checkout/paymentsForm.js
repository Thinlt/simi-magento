function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { Form } from 'informed';
import { array, bool, func, shape, string } from 'prop-types';
import BraintreeDropin from "./braintreeDropin";
import Button from "../Button";
import Checkbox from "../Checkbox";
import Field from "../Field";
import TextInput from "../TextInput";
import classify from "../../classify";
import defaultClasses from "./paymentsForm.css";
import isObjectEmpty from "../../util/isObjectEmpty";
import { isRequired, hasLengthExactly, validateRegionCode } from "../../util/formValidators";
import combine from "../../util/combineValidators";
const DEFAULT_FORM_VALUES = {
  addresses_same: true
};

class PaymentsForm extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "state", {
      isRequestingPaymentNonce: false
    });

    _defineProperty(this, "billingAddressFields", () => {
      const {
        classes,
        countries
      } = this.props;
      return React.createElement(Fragment, null, React.createElement("div", {
        className: classes.street0
      }, React.createElement(Field, {
        label: "Street"
      }, React.createElement(TextInput, {
        id: classes.street0,
        field: "street[0]",
        validate: isRequired
      }))), React.createElement("div", {
        className: classes.city
      }, React.createElement(Field, {
        label: "City"
      }, React.createElement(TextInput, {
        id: classes.city,
        field: "city",
        validate: isRequired
      }))), React.createElement("div", {
        className: classes.region_code
      }, React.createElement(Field, {
        label: "State"
      }, React.createElement(TextInput, {
        id: classes.region_code,
        field: "region_code",
        validate: combine([isRequired, [hasLengthExactly, 2], [validateRegionCode, countries]])
      }))), React.createElement("div", {
        className: classes.postcode
      }, React.createElement(Field, {
        label: "ZIP"
      }, React.createElement(TextInput, {
        id: classes.postcode,
        field: "postcode",
        validate: isRequired
      }))));
    });

    _defineProperty(this, "formChildren", ({
      formState
    }) => {
      const {
        classes,
        submitting
      } = this.props;
      return React.createElement(Fragment, null, React.createElement("div", {
        className: classes.body
      }, React.createElement("h2", {
        className: classes.heading
      }, "Billing Information"), React.createElement("div", {
        className: classes.braintree
      }, React.createElement(BraintreeDropin, {
        isRequestingPaymentNonce: this.state.isRequestingPaymentNonce,
        onError: this.cancelPaymentNonceRequest,
        onSuccess: this.setPaymentNonce
      })), React.createElement("div", {
        className: classes.address_check
      }, React.createElement(Checkbox, {
        field: "addresses_same",
        label: "Billing address same as shipping address"
      })), !formState.values.addresses_same && this.billingAddressFields()), React.createElement("div", {
        className: classes.footer
      }, React.createElement(Button, {
        className: classes.button,
        onClick: this.cancel
      }, "Cancel"), React.createElement(Button, {
        className: classes.button,
        priority: "high",
        type: "submit",
        disabled: submitting
      }, "Use Card")));
    });

    _defineProperty(this, "setFormApi", formApi => {
      this.formApi = formApi;
    });

    _defineProperty(this, "cancel", () => {
      this.props.cancel();
    });

    _defineProperty(this, "submit", () => {
      this.setState({
        isRequestingPaymentNonce: true
      });
    });

    _defineProperty(this, "setPaymentNonce", value => {
      this.setState({
        isRequestingPaymentNonce: false
      }); // Build up the billing address payload.

      const formValue = this.formApi.getValue;
      const sameAsShippingAddress = formValue('addresses_same') || false;
      let billingAddress;

      if (!sameAsShippingAddress) {
        billingAddress = {
          city: formValue('city'),
          postcode: formValue('postcode'),
          region_code: formValue('region_code'),
          street: formValue('street')
        };
      } else {
        billingAddress = {
          sameAsShippingAddress
        };
      } // Submit the payment method and billing address payload.


      this.props.submit({
        billingAddress,
        paymentMethod: {
          code: 'braintree',
          data: value
        }
      });
    });

    _defineProperty(this, "cancelPaymentNonceRequest", () => {
      this.setState({
        isRequestingPaymentNonce: false
      });
    });
  }

  render() {
    const {
      classes,
      initialValues
    } = this.props;
    const {
      formChildren
    } = this;
    let initialFormValues;

    if (isObjectEmpty(initialValues)) {
      initialFormValues = DEFAULT_FORM_VALUES;
    } // We have some initial values, use them.
    else {
        if (initialValues.sameAsShippingAddress) {
          // If the addresses are the same, don't populate any fields
          // other than the checkbox with an initial value.
          initialFormValues = {
            addresses_same: true
          };
        } else {
          // The addresses are not the same, populate the other fields.
          initialFormValues = _objectSpread({
            addresses_same: false
          }, initialValues);
          delete initialFormValues.sameAsShippingAddress;
        }
      }

    return React.createElement(Form, {
      className: classes.root,
      getApi: this.setFormApi,
      initialValues: initialFormValues,
      onSubmit: this.submit
    }, formChildren);
  }
  /*
   *  Class Properties.
   */


}

_defineProperty(PaymentsForm, "propTypes", {
  cancel: func.isRequired,
  classes: shape({
    address_check: string,
    body: string,
    button: string,
    braintree: string,
    city: string,
    footer: string,
    heading: string,
    postcode: string,
    region_code: string,
    street0: string,
    textInput: string
  }),
  initialValues: shape({
    city: string,
    postcode: string,
    region_code: string,
    sameAsShippingAddress: bool,
    street0: array
  }),
  submit: func.isRequired,
  submitting: bool,
  countries: array
});

_defineProperty(PaymentsForm, "defaultProps", {
  initialValues: {}
});

export default classify(defaultClasses)(PaymentsForm);
//# sourceMappingURL=paymentsForm.js.map