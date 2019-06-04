function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { Form } from 'informed';
import memoize from 'memoize-one';
import { bool, func, shape, string, array } from 'prop-types';
import classify from "../../classify";
import Button from "../Button";
import defaultClasses from "./addressForm.css";
import { validateEmail, isRequired, hasLengthExactly, validateRegionCode } from "../../util/formValidators";
import combine from "../../util/combineValidators";
import TextInput from "../TextInput";
import Field from "../Field";
const fields = ['city', 'email', 'firstname', 'lastname', 'postcode', 'region_code', 'street', 'telephone'];
const filterInitialValues = memoize(values => fields.reduce((acc, key) => {
  acc[key] = values[key];
  return acc;
}, {}));

class AddressForm extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "validationBlock", () => {
      const {
        isAddressIncorrect,
        incorrectAddressMessage
      } = this.props;

      if (isAddressIncorrect) {
        return incorrectAddressMessage;
      } else {
        return null;
      }
    });

    _defineProperty(this, "children", () => {
      const {
        classes,
        submitting,
        countries
      } = this.props;
      return React.createElement(Fragment, null, React.createElement("div", {
        className: classes.body
      }, React.createElement("h2", {
        className: classes.heading
      }, "Shipping Address"), React.createElement("div", {
        className: classes.firstname
      }, React.createElement(Field, {
        label: "First Name"
      }, React.createElement(TextInput, {
        id: classes.firstname,
        field: "firstname",
        validate: isRequired
      }))), React.createElement("div", {
        className: classes.lastname
      }, React.createElement(Field, {
        label: "Last Name"
      }, React.createElement(TextInput, {
        id: classes.lastname,
        field: "lastname",
        validate: isRequired
      }))), React.createElement("div", {
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
        className: classes.postcode
      }, React.createElement(Field, {
        label: "ZIP"
      }, React.createElement(TextInput, {
        id: classes.postcode,
        field: "postcode",
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
        className: classes.telephone
      }, React.createElement(Field, {
        label: "Phone"
      }, React.createElement(TextInput, {
        id: classes.telephone,
        field: "telephone",
        validate: isRequired
      }))), React.createElement("div", {
        className: classes.email
      }, React.createElement(Field, {
        label: "Email"
      }, React.createElement(TextInput, {
        id: classes.email,
        field: "email",
        validate: combine([isRequired, validateEmail])
      }))), React.createElement("div", {
        className: classes.validation
      }, this.validationBlock())), React.createElement("div", {
        className: classes.footer
      }, React.createElement(Button, {
        className: classes.button,
        onClick: this.cancel
      }, "Cancel"), React.createElement(Button, {
        className: classes.button,
        type: "submit",
        priority: "high",
        disabled: submitting
      }, "Use Address")));
    });

    _defineProperty(this, "cancel", () => {
      this.props.cancel();
    });

    _defineProperty(this, "submit", values => {
      this.props.submit(values);
    });
  }

  render() {
    const {
      children,
      props
    } = this;
    const {
      classes,
      initialValues
    } = props;
    const values = filterInitialValues(initialValues);
    return React.createElement(Form, {
      className: classes.root,
      initialValues: values,
      onSubmit: this.submit
    }, children);
  }

}

_defineProperty(AddressForm, "propTypes", {
  cancel: func.isRequired,
  classes: shape({
    body: string,
    button: string,
    city: string,
    email: string,
    firstname: string,
    footer: string,
    lastname: string,
    postcode: string,
    region_code: string,
    street0: string,
    telephone: string,
    textInput: string,
    validation: string
  }),
  incorrectAddressMessage: string,
  submit: func.isRequired,
  submitting: bool,
  countries: array
});

_defineProperty(AddressForm, "defaultProps", {
  initialValues: {}
});

export default classify(defaultClasses)(AddressForm);
/*
const mockAddress = {
    country_id: 'US',
    firstname: 'Veronica',
    lastname: 'Costello',
    street: ['6146 Honey Bluff Parkway'],
    city: 'Calder',
    postcode: '49628-7978',
    region_id: 33,
    region_code: 'MI',
    region: 'Michigan',
    telephone: '(555) 229-3326',
    email: 'veronica@example.com'
};
*/
//# sourceMappingURL=addressForm.js.map