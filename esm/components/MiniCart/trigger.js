function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { connect } from "@magento/venia-drivers";
import { compose } from 'redux';
import PropTypes from 'prop-types';
import classify from "../../classify";
import { closeDrawer } from "../../actions/app";
import defaultClasses from "./trigger.css";

class Trigger extends Component {
  render() {
    const {
      children,
      classes,
      closeDrawer
    } = this.props;
    return React.createElement("button", {
      className: classes.root,
      onClick: closeDrawer
    }, children);
  }

}

_defineProperty(Trigger, "propTypes", {
  children: PropTypes.node,
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  closeDrawer: PropTypes.func
});

const mapDispatchToProps = {
  closeDrawer
};
export default compose(classify(defaultClasses), connect(null, mapDispatchToProps))(Trigger);
//# sourceMappingURL=trigger.js.map