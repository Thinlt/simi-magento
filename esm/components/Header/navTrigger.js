function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { connect } from "@magento/venia-drivers";
import { compose } from 'redux';
import PropTypes from 'prop-types';
import classify from "../../classify";
import { toggleDrawer } from "../../actions/app";
import defaultClasses from "./navTrigger.css";

class Trigger extends Component {
  render() {
    const {
      children,
      classes,
      openNav
    } = this.props;
    return React.createElement("button", {
      className: classes.root,
      "aria-label": "Toggle navigation panel",
      onClick: openNav
    }, children);
  }

}

_defineProperty(Trigger, "propTypes", {
  children: PropTypes.node,
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  openNav: PropTypes.func.isRequired
});

const mapDispatchToProps = dispatch => ({
  openNav: () => dispatch(toggleDrawer('nav'))
});

export default compose(classify(defaultClasses), connect(null, mapDispatchToProps))(Trigger);
//# sourceMappingURL=navTrigger.js.map