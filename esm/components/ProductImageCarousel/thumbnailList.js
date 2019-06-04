function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { List } from '@magento/peregrine';
import classify from "../../classify";
import Thumbnail from "./thumbnail";
import defaultClasses from "./thumbnailList.css";

class ThumbnailList extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "updateActiveItemHandler", newActiveItemIndex => {
      this.props.updateActiveItemIndex(newActiveItemIndex);
    });
  }

  render() {
    const {
      activeItemIndex,
      items,
      classes
    } = this.props;
    return React.createElement(List, {
      items: items,
      renderItem: props => React.createElement(Thumbnail, _extends({}, props, {
        isActive: activeItemIndex === props.itemIndex,
        onClickHandler: this.updateActiveItemHandler
      })),
      getItemKey: i => i.file,
      classes: classes
    });
  }

}

_defineProperty(ThumbnailList, "propTypes", {
  activeItemIndex: PropTypes.number,
  classes: PropTypes.shape({
    root: PropTypes.string
  }),
  items: PropTypes.arrayOf(PropTypes.shape({
    label: PropTypes.string,
    position: PropTypes.number,
    disabled: PropTypes.bool,
    file: PropTypes.string.isRequired
  })).isRequired,
  updateActiveItemIndex: PropTypes.func.isRequired
});

export default classify(defaultClasses)(ThumbnailList);
//# sourceMappingURL=thumbnailList.js.map