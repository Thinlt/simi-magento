function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { string, shape } from 'prop-types';
import classify from "../../classify";
import Trigger from "./trigger";
import defaultClasses from "./emptyMiniCart.css";

class EmptyMiniCart extends Component {
  render() {
    const {
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("h3", {
      className: classes.emptyTitle
    }, "There are no items in your shopping cart"), React.createElement(Trigger, null, React.createElement("span", {
      className: classes.continue
    }, "Continue Shopping")));
  }

}

_defineProperty(EmptyMiniCart, "propTypes", {
  classes: shape({
    root: string,
    emptyTitle: string,
    continue: string
  })
});

export default classify(defaultClasses)(EmptyMiniCart);
//# sourceMappingURL=emptyMiniCart.js.map