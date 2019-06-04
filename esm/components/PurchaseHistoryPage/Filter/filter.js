function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { shape, string } from 'prop-types';
import Icon from "../../Icon";
import FilterIcon from 'react-feather/dist/icons/filter';
import classify from "../../../classify";
import defaultClasses from "./filter.css";
const FILTER_ICON_ATTRS = {
  width: 16,
  color: 'rgb(0, 134, 138)'
};

class Filter extends Component {
  render() {
    const {
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("div", {
      className: classes.filterIconContainer
    }, React.createElement(Icon, {
      src: FilterIcon,
      attrs: FILTER_ICON_ATTRS
    })), React.createElement("span", null, "Filter by..."));
  }

}

_defineProperty(Filter, "propTypes", {
  classes: shape({
    root: string,
    filterIconContainer: string
  })
});

export default classify(defaultClasses)(Filter);
//# sourceMappingURL=filter.js.map