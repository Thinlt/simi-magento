import React, { useCallback } from 'react';
import { arrayOf, func, number, shape, string } from 'prop-types';
import { Link } from "@magento/venia-drivers";
import { mergeClasses } from "../../classify";
import getLocation from "./getLocation";
import defaultClasses from "./suggestedCategories.css";

const SuggestedCategories = props => {
  const {
    categories,
    limit,
    onNavigate,
    value
  } = props;
  const classes = mergeClasses(defaultClasses, props.classes);
  const handleClick = useCallback(() => {
    if (typeof onNavigate === 'function') {
      onNavigate();
    }
  }, [onNavigate]);
  const items = categories.slice(0, limit).map(({
    label,
    value_string: categoryId
  }) => React.createElement("li", {
    key: categoryId,
    className: classes.item
  }, React.createElement(Link, {
    className: classes.link,
    to: getLocation(value, categoryId),
    onClick: handleClick
  }, React.createElement("strong", {
    className: classes.value
  }, value), React.createElement("span", null, ` in ${label}`))));
  return React.createElement("ul", {
    className: classes.root
  }, items);
};

export default SuggestedCategories;
SuggestedCategories.defaultProps = {
  limit: 4
};
SuggestedCategories.propTypes = {
  categories: arrayOf(shape({
    label: string.isRequired,
    value_string: string.isRequired
  })).isRequired,
  classes: shape({
    item: string,
    link: string,
    root: string,
    value: string
  }),
  limit: number.isRequired,
  onNavigate: func,
  value: string
};
//# sourceMappingURL=suggestedCategories.js.map