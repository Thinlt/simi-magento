import React, { Fragment, useCallback } from 'react';
import { arrayOf, bool, func, shape, string } from 'prop-types';
import { mergeClasses } from "../../classify";
import SuggestedCategories from "./suggestedCategories";
import SuggestedProducts from "./suggestedProducts";
import defaultClasses from "./suggestions.css";

const Suggestions = props => {
  const {
    products,
    searchValue,
    setVisible,
    visible
  } = props;
  const {
    filters,
    items
  } = products;
  const classes = mergeClasses(defaultClasses, props.classes);
  const onNavigate = useCallback(() => {
    setVisible(false);
  }, [setVisible]);

  if (!visible || !filters || !items) {
    return null;
  }

  const categoryFilter = filters.find(({
    name
  }) => name === 'Category') || {};
  const categories = categoryFilter.filter_items || [];
  return React.createElement(Fragment, null, React.createElement(SuggestedCategories, {
    categories: categories,
    onNavigate: onNavigate,
    value: searchValue
  }), React.createElement("h2", {
    className: classes.heading
  }, React.createElement("span", null, 'Product Suggestions')), React.createElement(SuggestedProducts, {
    onNavigate: onNavigate,
    products: items
  }));
};

export default Suggestions;
Suggestions.propTypes = {
  classes: shape({
    heading: string
  }),
  products: shape({
    filters: arrayOf(shape({
      filter_items: arrayOf(shape({})),
      name: string.isRequired
    }).isRequired),
    items: arrayOf(shape({}))
  }),
  searchValue: string,
  setVisible: func,
  visible: bool
};
//# sourceMappingURL=suggestions.js.map