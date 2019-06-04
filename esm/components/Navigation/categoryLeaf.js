function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { Link } from "@magento/venia-drivers";
import { func, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./categoryLeaf.css";
const urlSuffix = '.html';

class Leaf extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleClick", () => {
      const {
        onNavigate
      } = this.props;

      if (typeof onNavigate === 'function') {
        onNavigate();
      }
    });
  }

  render() {
    const {
      classes,
      name,
      urlPath
    } = this.props;
    return React.createElement(Link, {
      className: classes.root,
      to: `/${urlPath}${urlSuffix}`,
      onClick: this.handleClick
    }, React.createElement("span", {
      className: classes.text
    }, name));
  }

}

_defineProperty(Leaf, "propTypes", {
  classes: shape({
    root: string,
    text: string
  }),
  name: string.isRequired,
  urlPath: string.isRequired,
  onNavigate: func
});

export default classify(defaultClasses)(Leaf);
//# sourceMappingURL=categoryLeaf.js.map