function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import Trigger from "../../Trigger";
import Icon from "../../Icon";
import CloseIcon from 'react-feather/dist/icons/x';
import UserInformation from "../UserInformation";
import defaultClasses from "./header.css";

class Header extends Component {
  render() {
    const {
      user,
      onClose,
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement(UserInformation, {
      user: user
    }), React.createElement(Trigger, {
      classes: {
        root: classes.closeButton
      },
      action: onClose
    }, React.createElement(Icon, {
      src: CloseIcon
    })));
  }

}

_defineProperty(Header, "propTypes", {
  classes: PropTypes.shape({
    closeButton: PropTypes.string,
    header: PropTypes.string
  }),
  onClose: PropTypes.func,
  user: PropTypes.shape({
    email: PropTypes.string,
    firstname: PropTypes.string,
    lastname: PropTypes.string,
    fullname: PropTypes.string
  })
});

export default classify(defaultClasses)(Header);
//# sourceMappingURL=header.js.map