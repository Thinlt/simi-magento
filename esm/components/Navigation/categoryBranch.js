function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { func, shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./categoryLeaf.css";

class Branch extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "handleClick", () => {
      const {
        path,
        onDive
      } = this.props;
      onDive(path);
    });
  }

  render() {
    const {
      classes,
      name
    } = this.props;
    return React.createElement("button", {
      className: classes.root,
      onClick: this.handleClick
    }, React.createElement("span", {
      className: classes.text
    }, name));
  }

}

_defineProperty(Branch, "propTypes", {
  classes: shape({
    root: string,
    text: string
  }),
  name: string.isRequired,
  path: string.isRequired,
  onDive: func.isRequired
});

export default classify(defaultClasses)(Branch);
//# sourceMappingURL=categoryBranch.js.map