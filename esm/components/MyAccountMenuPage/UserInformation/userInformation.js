function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import Icon from "../../Icon";
import UserIcon from 'react-feather/dist/icons/user';
import defaultClasses from "./userInformation.css";

class UserInformation extends Component {
  render() {
    const {
      user,
      classes
    } = this.props;
    const {
      fullname,
      email
    } = user || {};
    const display = fullname.trim() || 'Loading...';
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.iconContainer
    }, React.createElement(Icon, {
      src: UserIcon,
      size: 18
    })), React.createElement("div", {
      className: classes.userInformationContainer
    }, React.createElement("p", {
      className: classes.fullName
    }, display), React.createElement("p", {
      className: classes.email
    }, email)));
  }

}

_defineProperty(UserInformation, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string,
    userInformationContainer: PropTypes.string,
    userInformationSecondary: PropTypes.string,
    iconContainer: PropTypes.string
  }),
  user: PropTypes.shape({
    email: PropTypes.string,
    firstname: PropTypes.string,
    lastname: PropTypes.string,
    fullname: PropTypes.string
  })
});

export default classify(defaultClasses)(UserInformation);
//# sourceMappingURL=userInformation.js.map