function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import Icon from "../Icon";
import CloudOffIcon from 'react-feather/dist/icons/cloud-off';
import CheckIcon from 'react-feather/dist/icons/check';
import PropTypes from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./onlineIndicator.css";

class OnlineIndicator extends Component {
  render() {
    const {
      isOnline,
      classes
    } = this.props;
    return !isOnline ? React.createElement("div", {
      className: classes.offline
    }, React.createElement(Icon, {
      src: CloudOffIcon
    }), React.createElement("p", null, " You are offline. Some features may be unavailable. ")) : React.createElement("div", {
      className: classes.online
    }, React.createElement(Icon, {
      src: CheckIcon
    }), React.createElement("p", null, " You are online. "));
  }

}

_defineProperty(OnlineIndicator, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  isOnline: PropTypes.bool
});

export default classify(defaultClasses)(OnlineIndicator);
//# sourceMappingURL=onlineIndicator.js.map