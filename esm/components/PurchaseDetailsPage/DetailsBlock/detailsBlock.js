function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { arrayOf, node, shape, string } from 'prop-types';
import classify from "../../../classify";
import defaultClasses from "./detailsBlock.css";

class DetailsBlock extends Component {
  render() {
    const {
      classes,
      rows
    } = this.props;
    const items = Array.from(rows, ({
      property,
      value
    }) => React.createElement(Fragment, {
      key: property
    }, React.createElement("dt", {
      className: classes.property
    }, property), React.createElement("dd", {
      className: classes.value
    }, value)));
    return React.createElement("dl", {
      className: classes.root
    }, items);
  }

}

_defineProperty(DetailsBlock, "propTypes", {
  classes: shape({
    property: string,
    root: string,
    value: string
  }).isRequired,
  rows: arrayOf(shape({
    property: node,
    value: node
  }))
});

_defineProperty(DetailsBlock, "defaultProps", {
  rows: []
});

export default classify(defaultClasses)(DetailsBlock);
//# sourceMappingURL=detailsBlock.js.map