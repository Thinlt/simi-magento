function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import Button from "../../Button";
import defaultClasses from "./formSubmissionSuccessful.css";

class FormSubmissionSuccessful extends Component {
  get textMessage() {
    const {
      email
    } = this.props;
    return `If there is an account associated with
            ${email} you will receive an
            email with a link to change your password`;
  }

  render() {
    const {
      textMessage
    } = this;
    const {
      classes,
      onContinue
    } = this.props;
    return React.createElement("div", null, React.createElement("p", {
      className: classes.text
    }, textMessage), React.createElement("div", {
      className: classes.buttonContainer
    }, React.createElement(Button, {
      onClick: onContinue
    }, "Continue Shopping")));
  }

}

_defineProperty(FormSubmissionSuccessful, "propTypes", {
  classes: PropTypes.shape({
    text: PropTypes.string,
    buttonContainer: PropTypes.string
  }),
  email: PropTypes.string,
  onContinue: PropTypes.func.isRequired
});

export default classify(defaultClasses)(FormSubmissionSuccessful);
//# sourceMappingURL=formSubmissionSuccessful.js.map