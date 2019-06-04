function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { func, shape, string } from 'prop-types';
import classify from "../../classify";
import Icon from "../Icon";
import ArrowLeftIcon from 'react-feather/dist/icons/arrow-left';
import CloseIcon from 'react-feather/dist/icons/x';
import Trigger from "../Trigger";
import defaultClasses from "./navHeader.css";

class NavHeader extends Component {
  render() {
    const {
      classes,
      onBack,
      onClose,
      title
    } = this.props;
    return React.createElement(Fragment, null, React.createElement(Trigger, {
      key: "backButton",
      action: onBack
    }, React.createElement(Icon, {
      src: ArrowLeftIcon
    })), React.createElement("h2", {
      key: "title",
      className: classes.title
    }, React.createElement("span", null, title)), React.createElement(Trigger, {
      key: "closeButton",
      action: onClose
    }, React.createElement(Icon, {
      src: CloseIcon
    })));
  }

}

_defineProperty(NavHeader, "propTypes", {
  classes: shape({
    title: string
  }),
  onBack: func.isRequired,
  onClose: func.isRequired
});

export default classify(defaultClasses)(NavHeader);
//# sourceMappingURL=navHeader.js.map