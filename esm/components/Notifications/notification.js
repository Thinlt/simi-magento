function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import Icon from "../Icon";
import { any, func, oneOf, shape, string } from 'prop-types';
import AlertCircle from 'react-feather/dist/icons/alert-circle';
import InfoIcon from 'react-feather/dist/icons/info';
import CheckCircle from 'react-feather/dist/icons/check-circle';
import classify from "../../classify";
import defaultClasses from "./notification.css";
const defaultIcons = {
  error: AlertCircle,
  warning: InfoIcon,
  success: CheckCircle
};

class Notification extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "dismiss", () => this.setState({
      showing: false
    }));

    _defineProperty(this, "handleTransitionEnd", e => {
      // The transitionend event fires after slide down and slide up,
      // so we test the showing state to determine which callback to run.
      if (this.state.showing) {
        // Pass a function to dismiss this notification into the caller.
        this.runHandlerIfPresent('afterShow', e, this.dismiss);
      } else {
        this.runHandlerIfPresent('afterDismiss', e);
      }
    });

    _defineProperty(this, "handleClick", e => {
      // Pass a function to dismiss this notification into the caller.
      this.runHandlerIfPresent('onClick', e, this.dismiss);
    });

    _defineProperty(this, "state", {
      showing: false
    });
  }

  runHandlerIfPresent(name, ...args) {
    const handler = this.props[name];

    if (typeof handler === 'function') {
      handler(...args);
    }
  }

  componentDidMount() {
    // React sometimes optimizes by merging two successive DOM updates,
    // which means we don't get the transition effect. Therefore we wait a
    // moment after mount to add the class which causes the slide-down.
    this._showingTimeout = setTimeout(() => this.setState({
      showing: true
    }), Notification.SHOW_DELAY);
  }

  componentWillUnmount() {
    // Don't leak timeouts! React will complain if the above timeout tries
    // to run setState on a component that isn't mounted anymore.
    clearTimeout(this._showingTimeout);
    this._showingTimeout = null;
  }

  render() {
    const {
      classes,
      children,
      icon: CustomIcon,
      onClick,
      type
    } = this.props;
    const {
      showing
    } = this.state;
    let className = classes[type];

    if (showing) {
      className += ` ${classes.showing}`;
    }

    const IconGlyph = CustomIcon || defaultIcons[type];
    return React.createElement("button", {
      className: className,
      disabled: !onClick,
      onTransitionEnd: this.handleTransitionEnd,
      onClick: this.handleClick
    }, React.createElement(Icon, {
      classes: {
        root: classes.icon
      },
      src: IconGlyph
    }), React.createElement("div", {
      className: classes.message
    }, children));
  }

}

_defineProperty(Notification, "propTypes", {
  children: any.isRequired,
  classes: shape({
    root: string
  }),
  icon: any,
  afterDismiss: func,
  afterShow: func,
  onClick: func,
  type: oneOf(['error', 'success', 'warning']).isRequired
});

_defineProperty(Notification, "SHOW_DELAY", 50);

export default classify(defaultClasses)(Notification);
//# sourceMappingURL=notification.js.map