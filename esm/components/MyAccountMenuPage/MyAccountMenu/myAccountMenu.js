function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import MenuItem from "../MenuItem/index";
import defaultClasses from "./myAccountMenu.css";

class MyAccountMenu extends Component {
  // TODO: add all menu items, use Badge component. Add purchase history page url.
  render() {
    const {
      classes,
      signOut
    } = this.props;
    return React.createElement("nav", {
      className: classes.list
    }, React.createElement(MenuItem.Link, {
      title: "Purchase History",
      to: "/"
    }), React.createElement(MenuItem.Button, {
      title: React.createElement("span", {
        className: classes.signOutTitle
      }, "Sign Out"),
      onClick: signOut
    }));
  }

}

_defineProperty(MyAccountMenu, "propTypes", {
  classes: PropTypes.shape({
    list: PropTypes.string,
    signOutTitle: PropTypes.string,
    rewardsPoints: PropTypes.string
  }),
  signOut: PropTypes.func
});

export default classify(defaultClasses)(MyAccountMenu);
//# sourceMappingURL=myAccountMenu.js.map