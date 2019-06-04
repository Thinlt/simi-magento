function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Form } from 'informed';
import Button from "../../Button";
import Field from "../../Field";
import TextInput from "../../TextInput";
import { isRequired } from "../../../util/formValidators";
import classify from "../../../classify";
import defaultClasses from "./forgotPasswordForm.css";

class ForgotPasswordForm extends Component {
  render() {
    const {
      classes,
      initialValues,
      onSubmit
    } = this.props;
    return React.createElement(Form, {
      className: classes.root,
      initialValues: initialValues,
      onSubmit: onSubmit
    }, React.createElement(Field, {
      label: "Email Address",
      required: true
    }, React.createElement(TextInput, {
      autoComplete: "email",
      field: "email",
      validate: isRequired,
      validateOnBlur: true
    })), React.createElement("div", {
      className: classes.buttonContainer
    }, React.createElement(Button, {
      type: "submit",
      priority: "high"
    }, "Submit")));
  }

}

_defineProperty(ForgotPasswordForm, "propTypes", {
  classes: PropTypes.shape({
    form: PropTypes.string,
    buttonContainer: PropTypes.string
  }),
  initialValues: PropTypes.shape({
    email: PropTypes.string
  }),
  onSubmit: PropTypes.func.isRequired
});

_defineProperty(ForgotPasswordForm, "defaultProps", {
  initialValues: {}
});

export default classify(defaultClasses)(ForgotPasswordForm);
//# sourceMappingURL=forgotPasswordForm.js.map