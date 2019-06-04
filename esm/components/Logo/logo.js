function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import classify from "../../classify";
import logo from "./logo.svg";

class Logo extends Component {
  render() {
    const {
      height,
      classes
    } = this.props;
    return React.createElement("img", {
      className: classes.logo,
      src: logo,
      height: height,
      alt: "Venia",
      title: "Venia"
    });
  }

}

_defineProperty(Logo, "propTypes", {
  classes: PropTypes.shape({
    logo: PropTypes.string
  }),
  height: PropTypes.number
});

_defineProperty(Logo, "defaultProps", {
  height: 24
});

export default classify({})(Logo);
//# sourceMappingURL=logo.js.map