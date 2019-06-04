function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Fragment, Component } from 'react';
import { compose } from 'redux';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import Icon from "../../Icon";
import defaultClasses from "./myAccountMenuTrigger.css";
import UserInformation from "../UserInformation";
import MyAccountMenuPage from "../MyAccountMenuPage";
import ChevronUpIcon from 'react-feather/dist/icons/chevron-up';

class MyAccountMenuTrigger extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "state", {
      on: false
    });

    _defineProperty(this, "toggle", () => {
      this.setState({
        on: !this.state.on
      });
    });
  }

  get menu() {
    const {
      classes
    } = this.props;
    const menuContainerClassName = this.state.on ? classes.menuOpen : classes.menuClosed;
    return React.createElement("div", {
      className: menuContainerClassName
    }, React.createElement(MyAccountMenuPage, {
      onClose: this.toggle
    }));
  }

  render() {
    const {
      menu
    } = this;
    const {
      user,
      classes
    } = this.props;
    return React.createElement(Fragment, null, React.createElement("div", {
      className: classes.userChip
    }, React.createElement(UserInformation, {
      user: user
    }), React.createElement("button", {
      className: classes.userMore,
      onClick: this.toggle
    }, React.createElement(Icon, {
      src: ChevronUpIcon
    }))), menu);
  }

}

_defineProperty(MyAccountMenuTrigger, "propTypes", {
  classes: PropTypes.shape({
    userChip: PropTypes.string,
    userMore: PropTypes.string,
    menuOpen: PropTypes.string,
    menuClosed: PropTypes.string
  }),
  user: PropTypes.shape({
    email: PropTypes.string,
    firstname: PropTypes.string,
    lastname: PropTypes.string,
    fullname: PropTypes.string
  })
});

export default compose(classify(defaultClasses))(MyAccountMenuTrigger);
//# sourceMappingURL=myAccountMenuTrigger.js.map