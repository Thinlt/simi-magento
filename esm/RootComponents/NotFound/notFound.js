function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import classify from "../../classify";
import defaultClasses from "./notFound.css";

class NotFound extends Component {
  // TODO: Should not be a default here, we just don't have
  // the wiring in place to map route info down the tree (yet)
  goBack() {
    history.back();
  }

  render() {
    const {
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("h1", null, " Offline! "), React.createElement("button", {
      onClick: this.goBack
    }, " Go Back "));
  }

}

_defineProperty(NotFound, "defaultProps", {
  id: 3
});

export default classify(defaultClasses)(NotFound);
//# sourceMappingURL=notFound.js.map