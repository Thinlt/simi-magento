function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { arrayOf, object, func, shape, string } from 'prop-types';
import classify from "../../classify";
import { Notification, NotificationStack } from "../Notifications";
import defaultClasses from "./errorNotifications.css";
const dismissers = new WeakMap();

class ErrorNotifications extends Component {
  componentDidMount() {
    window.scrollTo(0, 0);
  }

  dismissNotificationOnClick(e, dismiss) {
    e.preventDefault();
    e.stopPropagation();
    dismiss();
  } // Memoize dismisser funcs to reduce re-renders from func identity change.


  getErrorDismisser(error) {
    const {
      onDismissError
    } = this.props;
    return dismissers.has(error) ? dismissers.get(error) : dismissers.set(error, () => onDismissError(error)).get(error);
  }

  get allNotifications() {
    const {
      classes,
      errors
    } = this.props;
    return errors.map(({
      error,
      id,
      loc
    }) => React.createElement(Notification, {
      key: id,
      type: "error",
      onClick: this.dismissNotificationOnClick,
      afterDismiss: this.getErrorDismisser(error)
    }, React.createElement("div", null, "Sorry! An unexpected error occurred."), React.createElement("small", {
      className: classes.debuginfo
    }, "Debug: ", id, " ", loc)));
  }

  render() {
    const {
      classes,
      errors
    } = this.props;

    if (errors.length > 0) {
      return React.createElement("div", {
        className: classes.root
      }, React.createElement(NotificationStack, null, this.allNotifications));
    }

    return null;
  }

}

_defineProperty(ErrorNotifications, "propTypes", {
  classes: shape({
    debuginfo: string
  }).isRequired,
  errors: arrayOf(shape({
    error: object.isRequired,
    id: string.isRequired,
    loc: string
  })),
  onDismissError: func.isRequired
});

export default classify(defaultClasses)(ErrorNotifications);
//# sourceMappingURL=errorNotifications.js.map