function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component } from 'react';
import { Query, Redirect } from "@magento/venia-drivers";
import { bool, func, object, shape, string } from 'prop-types';
import Gallery from "../../components/Gallery";
import classify from "../../classify";
import Icon from "../../components/Icon";
import getQueryParameterValue from "../../util/getQueryParameterValue";
import CloseIcon from 'react-feather/dist/icons/x';
import { loadingIndicator } from "../../components/LoadingIndicator";
import defaultClasses from "./search.css";
import PRODUCT_SEARCH from "../../queries/productSearch.graphql";
const getCategoryName = {
  "kind": "Document",
  "definitions": [{
    "kind": "OperationDefinition",
    "operation": "query",
    "name": {
      "kind": "Name",
      "value": "getCategoryName"
    },
    "variableDefinitions": [{
      "kind": "VariableDefinition",
      "variable": {
        "kind": "Variable",
        "name": {
          "kind": "Name",
          "value": "id"
        }
      },
      "type": {
        "kind": "NonNullType",
        "type": {
          "kind": "NamedType",
          "name": {
            "kind": "Name",
            "value": "Int"
          }
        }
      },
      "directives": []
    }],
    "directives": [],
    "selectionSet": {
      "kind": "SelectionSet",
      "selections": [{
        "kind": "Field",
        "name": {
          "kind": "Name",
          "value": "category"
        },
        "arguments": [{
          "kind": "Argument",
          "name": {
            "kind": "Name",
            "value": "id"
          },
          "value": {
            "kind": "Variable",
            "name": {
              "kind": "Name",
              "value": "id"
            }
          }
        }],
        "directives": [],
        "selectionSet": {
          "kind": "SelectionSet",
          "selections": [{
            "kind": "Field",
            "name": {
              "kind": "Name",
              "value": "name"
            },
            "arguments": [],
            "directives": []
          }]
        }
      }]
    }
  }],
  "loc": {
    "start": 0,
    "end": 101,
    "source": {
      "body": "\n    query getCategoryName($id: Int!) {\n        category(id: $id) {\n            name\n        }\n    }\n",
      "name": "GraphQL request",
      "locationOffset": {
        "line": 1,
        "column": 1
      }
    }
  }
};
export class Search extends Component {
  constructor(...args) {
    super(...args);

    _defineProperty(this, "getCategoryName", (categoryId, classes) => React.createElement("div", {
      className: classes.categoryFilters
    }, React.createElement("button", {
      className: classes.categoryFilter,
      onClick: this.handleClearCategoryFilter
    }, React.createElement("small", {
      className: classes.categoryFilterText
    }, React.createElement(Query, {
      query: getCategoryName,
      variables: {
        id: categoryId
      }
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) return null;
      if (loading) return 'Loading...';
      return data.category.name;
    })), React.createElement(Icon, {
      src: CloseIcon,
      attrs: {
        width: '13px',
        height: '13px'
      }
    }))));

    _defineProperty(this, "handleClearCategoryFilter", () => {
      const inputText = getQueryParameterValue({
        location: this.props.location,
        queryParameter: 'query'
      });

      if (inputText) {
        this.props.executeSearch(inputText, this.props.history);
      }
    });
  }

  componentDidMount() {
    // Ensure that search is open when the user lands on the search page.
    const {
      location,
      searchOpen,
      toggleSearch
    } = this.props;
    const inputText = getQueryParameterValue({
      location,
      queryParameter: 'query'
    });

    if (toggleSearch && !searchOpen && inputText) {
      toggleSearch();
    }
  }

  render() {
    const {
      classes,
      location
    } = this.props;
    const {
      getCategoryName
    } = this;
    const inputText = getQueryParameterValue({
      location,
      queryParameter: 'query'
    });
    const categoryId = getQueryParameterValue({
      location,
      queryParameter: 'category'
    });

    if (!inputText) {
      return React.createElement(Redirect, {
        to: "/"
      });
    }

    const queryVariable = categoryId ? {
      inputText,
      categoryId
    } : {
      inputText
    };
    return React.createElement(Query, {
      query: PRODUCT_SEARCH,
      variables: queryVariable
    }, ({
      loading,
      error,
      data
    }) => {
      if (error) return React.createElement("div", null, "Data Fetch Error");
      if (loading) return loadingIndicator;
      if (data.products.items.length === 0) return React.createElement("div", {
        className: classes.noResult
      }, "No results found!");
      return React.createElement("article", {
        className: classes.root
      }, React.createElement("div", {
        className: classes.categoryTop
      }, React.createElement("div", {
        className: classes.totalPages
      }, data.products.total_count, " items", ' '), categoryId && getCategoryName(categoryId, classes)), React.createElement("section", {
        className: classes.gallery
      }, React.createElement(Gallery, {
        data: data.products.items
      })));
    });
  }

}

_defineProperty(Search, "propTypes", {
  classes: shape({
    noResult: string,
    root: string,
    totalPages: string
  }),
  executeSearch: func.isRequired,
  history: object,
  location: object.isRequired,
  match: object,
  searchOpen: bool,
  toggleSearch: func
});

export default classify(defaultClasses)(Search);
//# sourceMappingURL=search.js.map