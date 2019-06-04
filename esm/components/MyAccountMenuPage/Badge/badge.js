function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../../classify";
import defaultClasses from "./badge.css";

class Badge extends Component {
  render() {
    const {
      classes,
      children
    } = this.props;
    return React.createElement("span", {
      className: classes.root
    }, React.createElement("span", {
      className: classes.text
    }, children));
  }

}

_defineProperty(Badge, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string,
    text: PropTypes.string
  }),
  children: PropTypes.node
});

export default classify(defaultClasses)(Badge);
//# sourceMappingURL=badge.js.map