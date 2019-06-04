function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { array, bool, func, shape, string } from 'prop-types';
import Main from "../Main";
import Mask from "../Mask";
import MiniCart from "../MiniCart";
import Navigation from "../Navigation";
import OnlineIndicator from "../OnlineIndicator";
import ErrorNotifications from "./errorNotifications";
import renderRoutes from "./renderRoutes";
import errorRecord from "../../util/createErrorRecord";

class App extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "recoverFromRenderError", () => window.location.reload());

    _defineProperty(this, "state", App.initialState);
  }

  static get initialState() {
    return {
      renderError: null
    };
  }

  get errorFallback() {
    const {
      renderError
    } = this.state;

    if (renderError) {
      const errors = [errorRecord(renderError, window, this, renderError.stack)];
      return React.createElement(Fragment, null, React.createElement(Main, {
        isMasked: true
      }), React.createElement(Mask, {
        isActive: true
      }), React.createElement(ErrorNotifications, {
        errors: errors,
        onDismissError: this.recoverFromRenderError
      }));
    }
  }

  get onlineIndicator() {
    const {
      app
    } = this.props;
    const {
      hasBeenOffline,
      isOnline
    } = app; // Only show online indicator when
    // online after being offline

    return hasBeenOffline ? React.createElement(OnlineIndicator, {
      isOnline: isOnline
    }) : null;
  } // Defining this static method turns this component into an ErrorBoundary,
  // which can re-render a fallback UI if any of its descendant components
  // throw an exception while rendering.
  // This is a common implementation: React uses the returned object to run
  // setState() on the component. <App /> then re-renders with a `renderError`
  // property in state, and the render() method below will render a fallback
  // UI describing the error if the `renderError` property is set.


  static getDerivedStateFromError(renderError) {
    return {
      renderError
    };
  }

  render() {
    const {
      errorFallback
    } = this;

    if (errorFallback) {
      return errorFallback;
    }

    const {
      app,
      closeDrawer,
      markErrorHandled,
      unhandledErrors
    } = this.props;
    const {
      onlineIndicator
    } = this;
    const {
      drawer,
      overlay
    } = app;
    const navIsOpen = drawer === 'nav';
    const cartIsOpen = drawer === 'cart';
    return React.createElement(Fragment, null, React.createElement(Main, {
      isMasked: overlay
    }, onlineIndicator, renderRoutes()), React.createElement(Mask, {
      isActive: overlay,
      dismiss: closeDrawer
    }), React.createElement(Navigation, {
      isOpen: navIsOpen
    }), React.createElement(MiniCart, {
      isOpen: cartIsOpen
    }), React.createElement(ErrorNotifications, {
      errors: unhandledErrors,
      onDismissError: markErrorHandled
    }));
  }

}

_defineProperty(App, "propTypes", {
  app: shape({
    drawer: string,
    hasBeenOffline: bool,
    isOnline: bool,
    overlay: bool.isRequired
  }).isRequired,
  closeDrawer: func.isRequired,
  markErrorHandled: func.isRequired,
  unhandledErrors: array
});

export default App;
//# sourceMappingURL=app.js.map