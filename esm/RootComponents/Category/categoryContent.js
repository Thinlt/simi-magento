import React from 'react';
import { mergeClasses } from "../../classify";
import Gallery from "../../components/Gallery";
import Pagination from "../../components/Pagination";
import defaultClasses from "./category.css";

const CategoryContent = props => {
  const {
    pageControl,
    data,
    pageSize
  } = props;
  const classes = mergeClasses(defaultClasses, props.classes);
  const items = data ? data.category.products.items : null;
  const title = data ? data.category.description : null;
  const categoryTitle = data ? data.category.name : null;
  return React.createElement("article", {
    className: classes.root
  }, React.createElement("h1", {
    className: classes.title
  }, React.createElement("div", {
    dangerouslySetInnerHTML: {
      __html: title
    }
  }), React.createElement("div", {
    className: classes.categoryTitle
  }, categoryTitle)), React.createElement("section", {
    className: classes.gallery
  }, React.createElement(Gallery, {
    data: items,
    title: title,
    pageSize: pageSize
  })), React.createElement("div", {
    className: classes.pagination
  }, React.createElement(Pagination, {
    pageControl: pageControl
  })));
};

export default CategoryContent;
//# sourceMappingURL=categoryContent.js.map