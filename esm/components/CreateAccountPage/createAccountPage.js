function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { withRouter } from "@magento/venia-drivers";
import { compose } from 'redux';
import CreateAccountForm from "../CreateAccount";
import classify from "../../classify";
import defaultClasses from "./createAccountPage.css";
import { getCreateAccountInitialValues } from "./helpers";

class CreateAccountPage extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "createAccount", accountInfo => {
      const {
        createAccount,
        history
      } = this.props;
      createAccount({
        accountInfo,
        history
      });
    });
  }

  render() {
    const initialValues = getCreateAccountInitialValues(window.location.search);
    return React.createElement("div", {
      className: this.props.classes.container
    }, React.createElement(CreateAccountForm, {
      initialValues: initialValues,
      onSubmit: this.createAccount
    }));
  }

}

_defineProperty(CreateAccountPage, "propTypes", {
  createAccount: PropTypes.func,
  initialValues: PropTypes.shape({}),
  history: PropTypes.shape({})
});

export default compose(withRouter, classify(defaultClasses))(CreateAccountPage);
//# sourceMappingURL=createAccountPage.js.map