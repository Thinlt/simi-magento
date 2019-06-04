function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

import React from 'react';
import { arrayOf, func, number, oneOfType, shape, string } from 'prop-types';
import { mergeClasses } from "../../classify";
import mapProduct from "./mapProduct";
import SuggestedProduct from "./suggestedProduct";
import defaultClasses from "./suggestedProducts.css";

const SuggestedProducts = props => {
  const {
    limit,
    onNavigate,
    products
  } = props;
  const classes = mergeClasses(defaultClasses, props.classes);
  const items = products.slice(0, limit).map(product => React.createElement("li", {
    key: product.id,
    className: classes.item
  }, React.createElement(SuggestedProduct, _extends({}, mapProduct(product), {
    onNavigate: onNavigate
  }))));
  return React.createElement("ul", {
    className: classes.root
  }, items);
};

export default SuggestedProducts;
SuggestedProducts.defaultProps = {
  limit: 3
};
SuggestedProducts.propTypes = {
  classes: shape({
    item: string,
    root: string
  }),
  limit: number.isRequired,
  onNavigate: func,
  products: arrayOf(shape({
    id: oneOfType([number, string]).isRequired
  })).isRequired
};
//# sourceMappingURL=suggestedProducts.js.map