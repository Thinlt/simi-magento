import React, { Suspense } from 'react';
import { bool, func, shape, string } from 'prop-types';
import MenuIcon from 'react-feather/dist/icons/menu';
import SearchIcon from 'react-feather/dist/icons/search';
import Icon from "../Icon";
import Logo from "../Logo";
import { Link, resourceUrl, Route } from "@magento/venia-drivers";
import CartTrigger from "./cartTrigger";
import NavTrigger from "./navTrigger";
import SearchTrigger from "./searchTrigger";
import { mergeClasses } from "../../classify";
import defaultClasses from "./header.css";
const SearchBar = React.lazy(() => import("../SearchBar"));

const Header = props => {
  // Props.
  const {
    searchOpen,
    toggleSearch
  } = props; // Members.

  const classes = mergeClasses(defaultClasses, props.classes);
  const rootClass = searchOpen ? classes.open : classes.closed;
  const searchIcon = React.createElement(Icon, {
    src: SearchIcon
  });
  return React.createElement("header", {
    className: rootClass
  }, React.createElement("div", {
    className: classes.toolbar
  }, React.createElement(Link, {
    to: resourceUrl('/')
  }, React.createElement(Logo, {
    classes: {
      logo: classes.logo
    }
  })), React.createElement("div", {
    className: classes.primaryActions
  }, React.createElement(NavTrigger, null, React.createElement(Icon, {
    src: MenuIcon
  }))), React.createElement("div", {
    className: classes.secondaryActions
  }, React.createElement(SearchTrigger, {
    searchOpen: searchOpen,
    toggleSearch: toggleSearch
  }, searchIcon), React.createElement(CartTrigger, null))), React.createElement(Suspense, {
    fallback: searchOpen ? searchIcon : null
  }, React.createElement(Route, {
    render: ({
      history,
      location
    }) => React.createElement(SearchBar, {
      isOpen: searchOpen,
      history: history,
      location: location
    })
  })));
};

Header.propTypes = {
  classes: shape({
    closed: string,
    logo: string,
    open: string,
    primaryActions: string,
    secondaryActions: string,
    toolbar: string
  }),
  searchOpen: bool,
  toggleSearch: func.isRequired
};
export default Header;
//# sourceMappingURL=header.js.map