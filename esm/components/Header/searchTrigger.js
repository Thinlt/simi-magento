function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import PropTypes from 'prop-types';
import classify from "../../classify";
import defaultClasses from "./searchTrigger.css";

class SearchTrigger extends Component {
  render() {
    const {
      children,
      classes,
      toggleSearch,
      searchOpen
    } = this.props;
    const searchClass = searchOpen ? classes.open : classes.root;
    return React.createElement(Fragment, null, React.createElement("button", {
      className: searchClass,
      onClick: toggleSearch
    }, children));
  }

}

_defineProperty(SearchTrigger, "propTypes", {
  children: PropTypes.node,
  classes: PropTypes.shape({
    root: PropTypes.string,
    open: PropTypes.string
  }),
  searchOpen: PropTypes.bool,
  toggleSearch: PropTypes.func.isRequired
});

export default classify(defaultClasses)(SearchTrigger);
//# sourceMappingURL=searchTrigger.js.map