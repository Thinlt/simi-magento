function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./block.css";

class Block extends Component {
  render() {
    const {
      classes,
      content: __html
    } = this.props;
    return React.createElement("div", {
      className: classes.root,
      dangerouslySetInnerHTML: {
        __html
      }
    });
  }

}

_defineProperty(Block, "propTypes", {
  classes: shape({
    root: string
  }),
  content: string.isRequired
});

export default classify(defaultClasses)(Block);
//# sourceMappingURL=block.js.map