function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import Logo from "../../Logo";
import MyAccountMenu from "../MyAccountMenu";
import Header from "../Header";
import defaultClasses from "./myAccountMenuPage.css";

class MyAccountMenuPage extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleSignOut", () => {
      const {
        signOut,
        history
      } = this.props;
      signOut({
        history
      });
    });
  }

  render() {
    const {
      classes,
      user,
      onClose
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement(Header, {
      user: user,
      onClose: onClose
    }), React.createElement(MyAccountMenu, {
      signOut: this.handleSignOut
    }), React.createElement("div", {
      className: classes.logoContainer
    }, React.createElement(Logo, {
      height: 32
    })));
  }

}

_defineProperty(MyAccountMenuPage, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string,
    logoContainer: PropTypes.string
  }),
  signOut: PropTypes.func,
  onClose: PropTypes.func,
  history: PropTypes.shape({}),
  user: PropTypes.shape({
    email: PropTypes.string,
    firstname: PropTypes.string,
    lastname: PropTypes.string,
    fullname: PropTypes.string
  })
});

export default classify(defaultClasses)(MyAccountMenuPage);
//# sourceMappingURL=myAccountMenuPage.js.map