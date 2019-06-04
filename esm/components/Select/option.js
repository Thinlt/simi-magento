function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';

class Option extends Component {
  render() {
    const {
      disabled,
      item
    } = this.props;
    const {
      label,
      value
    } = item;
    const text = label != null ? label : value;
    return React.createElement("option", {
      value: value,
      disabled: disabled
    }, text);
  }

}

_defineProperty(Option, "propTypes", {
  disabled: PropTypes.bool,
  item: PropTypes.shape({
    label: PropTypes.string,
    value: PropTypes.string.isRequired
  }).isRequired
});

_defineProperty(Option, "defaultProps", {
  disabled: false
});

export default Option;
//# sourceMappingURL=option.js.map