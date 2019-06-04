function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import classify from "../../classify";
import ForgotPasswordForm from "./ForgotPasswordForm";
import FormSubmissionSuccessful from "./FormSubmissionSuccessful";
import defaultClasses from "./forgotPassword.css";

class ForgotPassword extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleFormSubmit", async ({
      email
    }) => {
      this.props.resetPassword({
        email
      });
    });

    _defineProperty(this, "handleContinue", () => {
      const {
        completePasswordReset,
        email,
        onClose
      } = this.props;
      completePasswordReset({
        email
      });
      onClose();
    });
  }

  render() {
    const {
      classes,
      email,
      initialValues,
      isInProgress
    } = this.props;

    if (isInProgress) {
      return React.createElement(FormSubmissionSuccessful, {
        email: email,
        onContinue: this.handleContinue
      });
    }

    return React.createElement(Fragment, null, React.createElement("p", {
      className: classes.instructions
    }, "Enter your email below to receive a password reset link"), React.createElement(ForgotPasswordForm, {
      initialValues: initialValues,
      onSubmit: this.handleFormSubmit
    }));
  }

}

_defineProperty(ForgotPassword, "propTypes", {
  classes: PropTypes.shape({
    instructions: PropTypes.string
  }),
  completePasswordReset: PropTypes.func.isRequired,
  email: PropTypes.string,
  initialValues: PropTypes.shape({
    email: PropTypes.string
  }),
  isInProgress: PropTypes.bool,
  onClose: PropTypes.func.isRequired,
  resetPassword: PropTypes.func.isRequired
});

export default classify(defaultClasses)(ForgotPassword);
//# sourceMappingURL=forgotPassword.js.map