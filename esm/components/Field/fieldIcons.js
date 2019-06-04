function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { shape, string } from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./fieldIcons.css";

class FieldIcons extends Component {
  render() {
    const {
      after,
      before,
      children,
      classes
    } = this.props;
    const style = {
      '--iconsBefore': before ? 1 : 0,
      '--iconsAfter': after ? 1 : 0
    };
    return React.createElement("span", {
      className: classes.root,
      style: style
    }, React.createElement("span", {
      className: classes.input
    }, children), React.createElement("span", {
      className: classes.before
    }, before), React.createElement("span", {
      className: classes.after
    }, after));
  }

}

_defineProperty(FieldIcons, "propTypes", {
  classes: shape({
    after: string,
    before: string,
    root: string
  })
});

export default classify(defaultClasses)(FieldIcons);
//# sourceMappingURL=fieldIcons.js.map