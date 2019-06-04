function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { bool, func, object, shape, string } from 'prop-types';
import { Form } from 'informed';
import Button from "../Button";
import Field from "../Field";
import LoadingIndicator from "../LoadingIndicator";
import TextInput from "../TextInput";
import { isRequired } from "../../util/formValidators";
import defaultClasses from "./signIn.css";
import classify from "../../classify";

class SignIn extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleForgotPassword", () => {
      const username = this.formApi.getValue('email');

      if (this.props.setDefaultUsername) {
        this.props.setDefaultUsername(username);
      }

      this.props.onForgotPassword();
    });

    _defineProperty(this, "onSignIn", () => {
      const username = this.formApi.getValue('email');
      const password = this.formApi.getValue('password');
      this.props.signIn({
        username,
        password
      });
    });

    _defineProperty(this, "setFormApi", formApi => {
      this.formApi = formApi;
    });

    _defineProperty(this, "showCreateAccountForm", () => {
      const username = this.formApi.getValue('email');

      if (this.props.setDefaultUsername) {
        this.props.setDefaultUsername(username);
      }

      this.props.showCreateAccountForm();
    });
  }

  get errorMessage() {
    const {
      signInError
    } = this.props;
    const hasError = signInError && Object.keys(signInError).length;

    if (hasError) {
      // Note: we can't access the actual message that comes back from the server
      // without doing some fragile string manipulation. Hardcoded for now.
      return 'The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.';
    }
  }

  render() {
    const {
      classes,
      isGettingDetails,
      isSigningIn
    } = this.props;
    const {
      onSignIn,
      errorMessage
    } = this;

    if (isGettingDetails || isSigningIn) {
      return React.createElement("div", {
        className: classes.modal_active
      }, React.createElement(LoadingIndicator, null, "Signing In"));
    } else {
      return React.createElement("div", {
        className: classes.root
      }, React.createElement(Form, {
        className: classes.form,
        getApi: this.setFormApi,
        onSubmit: onSignIn
      }, React.createElement(Field, {
        label: "Email",
        required: true
      }, React.createElement(TextInput, {
        autoComplete: "email",
        field: "email",
        validate: isRequired,
        validateOnBlur: true
      })), React.createElement(Field, {
        label: "Password",
        required: true
      }, React.createElement(TextInput, {
        autoComplete: "current-password",
        field: "password",
        type: "password",
        validate: isRequired,
        validateOnBlur: true
      })), React.createElement("div", {
        className: classes.signInButton
      }, React.createElement(Button, {
        priority: "high",
        type: "submit"
      }, "Sign In")), React.createElement("div", {
        className: classes.signInError
      }, errorMessage), React.createElement("button", {
        type: "button",
        className: classes.forgotPassword,
        onClick: this.handleForgotPassword
      }, "Forgot password?")), React.createElement("div", {
        className: classes.signInDivider
      }), React.createElement("div", {
        className: classes.showCreateAccountButton
      }, React.createElement(Button, {
        priority: "high",
        onClick: this.showCreateAccountForm
      }, "Create an Account")));
    }
  }

}

_defineProperty(SignIn, "propTypes", {
  classes: shape({
    forgotPassword: string,
    form: string,
    modal: string,
    modal_active: string,
    root: string,
    showCreateAccountButton: string,
    signInDivider: string,
    signInError: string,
    signInSection: string
  }),
  isGettingDetails: bool,
  isSigningIn: bool,
  onForgotPassword: func.isRequired,
  setDefaultUsername: func,
  signIn: func,
  signInError: object
});

export default classify(defaultClasses)(SignIn);
//# sourceMappingURL=signIn.js.map