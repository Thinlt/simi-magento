function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { array, func, oneOfType, shape, string } from 'prop-types';
import { Query } from "@magento/venia-drivers";
import classify from "../../classify";
import Block from "./block";
import defaultClasses from "./cmsBlock.css";
import getCmsBlocks from "../../queries/getCmsBlocks.graphql";

class CmsBlockGroup extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "renderBlocks", ({
      data,
      error,
      loading
    }) => {
      const {
        children,
        classes
      } = this.props;

      if (error) {
        return React.createElement("div", null, "Data Fetch Error");
      }

      if (loading) {
        return React.createElement("div", null, "Fetching Data");
      }

      const {
        items
      } = data.cmsBlocks;

      if (!Array.isArray(items) || !items.length) {
        return React.createElement("div", null, "There are no blocks to display");
      }

      const BlockChild = typeof children === 'function' ? children : Block;
      const blocks = items.map((item, index) => React.createElement(BlockChild, _extends({
        key: item.identifier,
        className: classes.block,
        index: index
      }, item)));
      return React.createElement("div", {
        className: classes.content
      }, blocks);
    });
  }

  render() {
    const {
      props,
      renderBlocks
    } = this;
    const {
      classes,
      identifiers
    } = props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement(Query, {
      query: getCmsBlocks,
      variables: {
        identifiers
      }
    }, renderBlocks));
  }

}

_defineProperty(CmsBlockGroup, "propTypes", {
  children: func,
  classes: shape({
    block: string,
    content: string,
    root: string
  }),
  identifiers: oneOfType([string, array])
});

export default classify(defaultClasses)(CmsBlockGroup);
//# sourceMappingURL=cmsBlock.js.map