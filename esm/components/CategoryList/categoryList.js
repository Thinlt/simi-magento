function _objectSpread(target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i] != null ? arguments[i] : {}; var ownKeys = Object.keys(source); if (typeof Object.getOwnPropertySymbols === 'function') { ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) { return Object.getOwnPropertyDescriptor(source, sym).enumerable; })); } ownKeys.forEach(function (key) { _defineProperty(target, key, source[key]); }); } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { string, number, shape } from 'prop-types';
import { Query } from "@magento/venia-drivers";
import classify from "../../classify";
import { loadingIndicator } from "../LoadingIndicator";
import defaultClasses from "./categoryList.css";
import CategoryTile from "./categoryTile";
import categoryListQuery from "../../queries/getCategoryList.graphql";

class CategoryList extends Component {
  get header() {
    const {
      title,
      classes
    } = this.props;
    return title ? React.createElement("div", {
      className: classes.header
    }, React.createElement("h2", {
      className: classes.title
    }, React.createElement("span", null, title))) : null;
  } // map Magento 2.3.1 schema changes to Venia 2.0.0 proptype shape to maintain backwards compatibility


  mapCategory(categoryItem) {
    const {
      items
    } = categoryItem.productImagePreview;
    return _objectSpread({}, categoryItem, {
      productImagePreview: {
        items: items.map(item => {
          const {
            small_image
          } = item;
          return _objectSpread({}, item, {
            small_image: typeof small_image === 'object' ? small_image.url : small_image
          });
        })
      }
    });
  }

  render() {
    const {
      id,
      classes
    } = this.props;
    return React.createElement("div", {
      className: classes.root
    }, this.header, React.createElement(Query, {
      query: categoryListQuery,
      variables: {
        id
      }
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) {
        return React.createElement("div", {
          className: classes.fetchError
        }, "Data Fetch Error: ", React.createElement("pre", null, error.message));
      }

      if (loading) {
        return loadingIndicator;
      }

      if (data.category.children.length === 0) {
        return React.createElement("div", {
          className: classes.noResults
        }, "No child categories found.");
      }

      return React.createElement("div", {
        className: classes.content
      }, data.category.children.map(item => React.createElement(CategoryTile, {
        item: this.mapCategory(item),
        key: item.url_key
      })));
    }));
  }

}

_defineProperty(CategoryList, "propTypes", {
  id: number,
  title: string,
  classes: shape({
    root: string,
    header: string,
    content: string
  })
});

export default classify(defaultClasses)(CategoryList);
//# sourceMappingURL=categoryList.js.map