function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, Fragment } from 'react';
import { func, number, objectOf, shape, string } from 'prop-types';
import { Query } from "@magento/venia-drivers";
import classify from "../../classify";
import { loadingIndicator } from "../LoadingIndicator";
import Branch from "./categoryBranch";
import Leaf from "./categoryLeaf";
import CategoryTree from "./categoryTree";
import defaultClasses from "./categoryTree.css";
import navigationMenu from "../../queries/getNavigationMenu.graphql";

class Tree extends Component {
  get leaves() {
    const {
      classes,
      onNavigate,
      rootNodeId,
      updateRootNodeId,
      currentId
    } = this.props;
    return rootNodeId ? React.createElement(Query, {
      query: navigationMenu,
      variables: {
        id: rootNodeId
      }
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) return React.createElement("div", null, "Data Fetch Error");
      if (loading) return loadingIndicator;
      const branches = [];
      const children = data.category.children.sort((a, b) => {
        if (a.position > b.position) return 1;else if (a.position == b.position && a.id > b.id) return 1;else return -1;
      });
      const leaves = children.map(node => {
        // allow leaf node to render if value is 1 or undefined (field not in Magento 2.3.0 schema)
        if (node.include_in_menu === 0) {
          return null;
        }

        const {
          children_count
        } = node;
        const isLeaf = children_count == 0;
        const elementProps = {
          nodeId: node.id,
          name: node.name,
          urlPath: node.url_path,
          path: node.path
        };

        if (!isLeaf) {
          branches.push(React.createElement(CategoryTree, {
            key: node.id,
            rootNodeId: node.id,
            updateRootNodeId: updateRootNodeId,
            onNavigate: onNavigate,
            currentId: currentId
          }));
        }

        const element = isLeaf ? React.createElement(Leaf, _extends({}, elementProps, {
          onNavigate: onNavigate
        })) : React.createElement(Branch, _extends({}, elementProps, {
          onDive: updateRootNodeId
        }));
        return React.createElement("li", {
          key: node.id
        }, element);
      });
      const branchClass = currentId == rootNodeId ? classes.branch : classes.inactive;
      return React.createElement(Fragment, null, React.createElement("div", {
        className: branchClass
      }, leaves), branches);
    }) : null;
  }

  render() {
    const {
      leaves,
      props
    } = this;
    const {
      classes
    } = props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("ul", {
      className: classes.tree
    }, leaves));
  }

}

_defineProperty(Tree, "propTypes", {
  classes: shape({
    leaf: string,
    root: string,
    tree: string
  }),
  nodes: objectOf(shape({
    id: number.isRequired,
    position: number.isRequired
  })),
  onNavigate: func,
  rootNodeId: number.isRequired,
  updateRootNodeId: func.isRequired,
  currentId: number.isRequired
});

export default classify(defaultClasses)(Tree);
//# sourceMappingURL=categoryTree.js.map