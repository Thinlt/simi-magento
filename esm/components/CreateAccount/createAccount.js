function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func, shape, string } from 'prop-types';
import { Form } from 'informed';
import classify from "../../classify";
import Button from "../Button";
import Checkbox from "../Checkbox";
import Field from "../Field";
import TextInput from "../TextInput";
import combine from "../../util/combineValidators";
import { validateEmail, isRequired, validatePassword, validateConfirmPassword, hasLengthAtLeast } from "../../util/formValidators";
import defaultClasses from "./createAccount.css";

class CreateAccount extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleSubmit", values => {
      const {
        onSubmit
      } = this.props;

      if (typeof onSubmit === 'function') {
        onSubmit(values);
      }
    });
  }

  get errorMessage() {
    const {
      createAccountError
    } = this.props;

    if (createAccountError) {
      const errorIsEmpty = Object.keys(createAccountError).length === 0;

      if (!errorIsEmpty) {
        return 'An error occurred. Please try again.';
      }
    }
  }

  get initialValues() {
    const {
      initialValues
    } = this.props;

    const {
      email,
      firstName,
      lastName
    } = initialValues,
          rest = _objectWithoutProperties(initialValues, ["email", "firstName", "lastName"]);

    return _objectSpread({
      customer: {
        email,
        firstname: firstName,
        lastname: lastName
      }
    }, rest);
  }

  render() {
    const {
      errorMessage,
      handleSubmit,
      initialValues,
      props
    } = this;
    const {
      classes
    } = props;
    return React.createElement(Form, {
      className: classes.root,
      initialValues: initialValues,
      onSubmit: handleSubmit
    }, React.createElement("h3", {
      className: classes.lead
    }, `Check out faster, use multiple addresses, track
                         orders and more by creating an account!`), React.createElement(Field, {
      label: "First Name",
      required: true
    }, React.createElement(TextInput, {
      field: "customer.firstname",
      autoComplete: "given-name",
      validate: isRequired,
      validateOnBlur: true
    })), React.createElement(Field, {
      label: "Last Name",
      required: true
    }, React.createElement(TextInput, {
      field: "customer.lastname",
      autoComplete: "family-name",
      validate: isRequired,
      validateOnBlur: true
    })), React.createElement(Field, {
      label: "Email",
      required: true
    }, React.createElement(TextInput, {
      field: "customer.email",
      autoComplete: "email",
      validate: combine([isRequired, validateEmail]),
      validateOnBlur: true
    })), React.createElement(Field, {
      label: "Password",
      required: true
    }, React.createElement(TextInput, {
      field: "password",
      type: "password",
      autoComplete: "new-password",
      validate: combine([isRequired, [hasLengthAtLeast, 8], validatePassword]),
      validateOnBlur: true
    })), React.createElement(Field, {
      label: "Confirm Password",
      required: true
    }, React.createElement(TextInput, {
      field: "confirm",
      type: "password",
      validate: combine([isRequired, validateConfirmPassword]),
      validateOnBlur: true
    })), React.createElement("div", {
      className: classes.subscribe
    }, React.createElement(Checkbox, {
      field: "subscribe",
      label: "Subscribe to news and updates"
    })), React.createElement("div", {
      className: classes.error
    }, errorMessage), React.createElement("div", {
      className: classes.actions
    }, React.createElement(Button, {
      type: "submit",
      priority: "high"
    }, 'Submit')));
  }

}

_defineProperty(CreateAccount, "propTypes", {
  classes: shape({
    actions: string,
    error: string,
    lead: string,
    root: string,
    subscribe: string
  }),
  createAccountError: shape({
    message: string
  }),
  initialValues: shape({
    email: string,
    firstName: string,
    lastName: string
  }),
  onSubmit: func
});

_defineProperty(CreateAccount, "defaultProps", {
  initialValues: {}
});

export default classify(defaultClasses)(CreateAccount);
//# sourceMappingURL=createAccount.js.map