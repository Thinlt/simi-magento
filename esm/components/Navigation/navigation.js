function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { PureComponent } from 'react';
import { bool, func, object, shape, string } from 'prop-types';
import classify from "../../classify";
import Button from "../Button";
import CreateAccount from "../CreateAccount";
import SignIn from "../SignIn";
import ForgotPassword from "../ForgotPassword";
import CategoryTree from "./categoryTree";
import NavHeader from "./navHeader";
import defaultClasses from "./navigation.css";
import { MyAccountMenuTrigger } from "../MyAccountMenuPage";

class Navigation extends PureComponent {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "state", {
      isCreateAccountOpen: false,
      isSignInOpen: false,
      isForgotPasswordOpen: false,
      rootNodeId: null,
      currentPath: null
    });

    _defineProperty(this, "createAccount", () => {});

    _defineProperty(this, "setCreateAccountForm", () => {
      /*
      When the CreateAccount component mounts, its email input will be set to
      the value of the SignIn component's email input.
      Inform's initialValue is set on component mount.
      Once the create account button is dirtied, always render the CreateAccount
      Component to show animation.
      */
      this.createAccount = className => {
        return React.createElement("div", {
          className: className
        }, React.createElement(CreateAccount, {
          onSubmit: this.props.createAccount,
          initialValues: {
            email: this.state.defaultUsername
          }
        }));
      };

      this.showCreateAccountForm();
    });

    _defineProperty(this, "forgotPassword", () => {});

    _defineProperty(this, "setForgotPasswordForm", () => {
      this.forgotPassword = className => {
        const {
          completePasswordReset,
          forgotPassword,
          resetPassword
        } = this.props;
        const {
          email,
          isInProgress
        } = forgotPassword;
        return React.createElement("div", {
          className: className
        }, React.createElement(ForgotPassword, {
          completePasswordReset: completePasswordReset,
          email: email,
          initialValues: {
            email: this.state.defaultUsername
          },
          isInProgress: isInProgress,
          onClose: this.closeForgotPassword,
          resetPassword: resetPassword
        }));
      };

      this.showForgotPasswordForm();
    });

    _defineProperty(this, "closeForgotPassword", () => {
      this.props.closeDrawer();
      this.hideForgotPasswordForm();
      this.hideSignInForm();
    });

    _defineProperty(this, "showSignInForm", () => {
      this.setState(() => ({
        isSignInOpen: true
      }));
    });

    _defineProperty(this, "hideSignInForm", () => {
      this.setState(() => ({
        isSignInOpen: false
      }));
    });

    _defineProperty(this, "setDefaultUsername", nextDefaultUsername => {
      this.setState(() => ({
        defaultUsername: nextDefaultUsername
      }));
    });

    _defineProperty(this, "showCreateAccountForm", () => {
      this.setState(() => ({
        isCreateAccountOpen: true
      }));
    });

    _defineProperty(this, "showForgotPasswordForm", () => {
      this.setState(() => ({
        isForgotPasswordOpen: true
      }));
    });

    _defineProperty(this, "hideCreateAccountForm", () => {
      this.setState(() => ({
        isCreateAccountOpen: false
      }));
    });

    _defineProperty(this, "hideForgotPasswordForm", () => {
      this.setState(() => ({
        isForgotPasswordOpen: false
      }));
    });

    _defineProperty(this, "setCurrentPath", currentPath => {
      const path = currentPath.split('/').reverse();
      const rootNodeId = parseInt(path[0]);
      this.setState(() => ({
        rootNodeId: rootNodeId,
        currentPath: path
      }));
    });

    _defineProperty(this, "setRootNodeIdToParent", () => {
      const path = this.state.currentPath;
      const parentId = path.length > 1 ? parseInt(path[1]) : this.props.rootCategoryId;
      path.shift();
      this.setState(() => ({
        rootNodeId: parentId,
        currentPath: path
      }));
    });
  }

  static getDerivedStateFromProps(props, state) {
    if (!state.rootNodeId && props.rootCategoryId) {
      return _objectSpread({}, state, {
        rootNodeId: props.rootCategoryId
      });
    }

    return state;
  }

  componentDidMount() {
    this.props.getUserDetails();
    this.props.getAllCategories();
  }

  get categoryTree() {
    const {
      props,
      setCurrentPath,
      state
    } = this;
    const {
      rootNodeId
    } = state;
    const {
      closeDrawer
    } = props;
    return rootNodeId ? React.createElement(CategoryTree, {
      rootNodeId: props.rootCategoryId,
      currentId: rootNodeId,
      updateRootNodeId: setCurrentPath,
      onNavigate: closeDrawer
    }) : null;
  }

  get footer() {
    const {
      classes
    } = this.props;
    return !this.props.isSignedIn ? React.createElement("div", {
      className: classes.authBar
    }, React.createElement(Button, {
      priority: "high",
      onClick: this.showSignInForm
    }, "Sign In")) : React.createElement(MyAccountMenuTrigger, null);
  }

  get signInForm() {
    const {
      isSignInOpen
    } = this.state;
    const {
      classes,
      isSignedIn
    } = this.props;
    const isOpen = !isSignedIn && isSignInOpen;
    const className = isOpen ? classes.signIn_open : classes.signIn_closed;
    return React.createElement("div", {
      className: className
    }, React.createElement(SignIn, {
      showCreateAccountForm: this.setCreateAccountForm,
      setDefaultUsername: this.setDefaultUsername,
      onForgotPassword: this.setForgotPasswordForm
    }));
  }

  get createAccountForm() {
    const {
      isCreateAccountOpen
    } = this.state;
    const {
      classes,
      isSignedIn
    } = this.props;
    const isOpen = !isSignedIn && isCreateAccountOpen;
    const className = isOpen ? classes.form_open : classes.form_closed;
    return this.createAccount(className);
  }

  get forgotPasswordForm() {
    const {
      isForgotPasswordOpen
    } = this.state;
    const {
      classes,
      isSignedIn
    } = this.props;
    const isOpen = !isSignedIn && isForgotPasswordOpen;
    const className = isOpen ? classes.form_open : classes.form_closed;
    return this.forgotPassword(className);
  }

  render() {
    const {
      categoryTree,
      createAccountForm,
      footer,
      hideCreateAccountForm,
      hideSignInForm,
      setRootNodeIdToParent,
      signInForm,
      forgotPasswordForm,
      hideForgotPasswordForm,
      props,
      state
    } = this;
    const {
      isCreateAccountOpen,
      isSignInOpen,
      isForgotPasswordOpen,
      rootNodeId
    } = state;
    const {
      classes,
      closeDrawer,
      isOpen,
      isSignedIn,
      rootCategoryId
    } = props;
    const className = isOpen ? classes.root_open : classes.root;
    const isTopLevel = !rootNodeId || rootNodeId === rootCategoryId;
    const handleBack = isCreateAccountOpen && !isSignedIn ? hideCreateAccountForm : isForgotPasswordOpen ? hideForgotPasswordForm : isSignInOpen && !isSignedIn ? hideSignInForm : isTopLevel ? closeDrawer : setRootNodeIdToParent;
    const title = isCreateAccountOpen && !isSignedIn ? 'Create Account' : isForgotPasswordOpen ? 'Forgot password' : isSignInOpen && !isSignedIn ? 'Sign In' : 'Main Menu';
    return React.createElement("aside", {
      className: className
    }, React.createElement("div", {
      className: classes.header
    }, React.createElement(NavHeader, {
      title: title,
      onBack: handleBack,
      onClose: closeDrawer
    })), React.createElement("nav", {
      className: classes.body
    }, categoryTree), React.createElement("div", {
      className: classes.footer
    }, footer), signInForm, createAccountForm, forgotPasswordForm);
  }

}

_defineProperty(Navigation, "propTypes", {
  classes: shape({
    authBar: string,
    body: string,
    form_closed: string,
    form_open: string,
    footer: string,
    header: string,
    root: string,
    root_open: string,
    signIn_closed: string,
    signIn_open: string
  }),
  closeDrawer: func.isRequired,
  completePasswordReset: func.isRequired,
  createAccount: func.isRequired,
  email: string,
  firstname: string,
  forgotPassword: shape({
    email: string,
    isInProgress: bool
  }),
  getAllCategories: func.isRequired,
  getUserDetails: func.isRequired,
  isOpen: bool,
  isSignedIn: bool,
  lastname: string,
  resetPassword: func.isRequired,
  signInError: object
});

export default classify(defaultClasses)(Navigation);
//# sourceMappingURL=navigation.js.map