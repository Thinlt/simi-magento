function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import classify from "../../classify";
import PropTypes from 'prop-types';
import defaultClasses from "./cartCounter.css";

class CartCounter extends Component {
  render() {
    const {
      counter,
      classes
    } = this.props;
    return counter > 0 ? React.createElement("span", {
      className: classes.root
    }, counter) : null;
  }

}

_defineProperty(CartCounter, "propTypes", {
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  counter: PropTypes.number.isRequired
});

export default classify(defaultClasses)(CartCounter);
//# sourceMappingURL=cartCounter.js.map