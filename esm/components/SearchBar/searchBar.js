import React, { useCallback } from 'react';
import { bool, func, shape, string } from 'prop-types';
import { Form } from 'informed';
import { useDropdown } from '@magento/peregrine';
import { mergeClasses } from "../../classify";
import Autocomplete from "./autocomplete";
import SearchField from "./searchField";
import defaultClasses from "./searchBar.css";
const initialValues = {
  search_query: ''
};

const SearchBar = props => {
  const {
    history,
    isOpen,
    location
  } = props;
  const {
    elementRef,
    expanded,
    setExpanded
  } = useDropdown();
  const classes = mergeClasses(defaultClasses, props.classes);
  const rootClassName = isOpen ? classes.root_open : classes.root; // expand or collapse on input change

  const handleChange = useCallback(value => {
    setExpanded(!!value);
  }, []); // expand on focus

  const handleFocus = useCallback(() => {
    setExpanded(true);
  }, []); // navigate on submit

  const handleSubmit = useCallback(({
    search_query
  }) => {
    history.push(`/search.html?query=${search_query}`);
  }, [history]);
  return React.createElement("div", {
    className: rootClassName
  }, React.createElement("div", {
    ref: elementRef,
    className: classes.container
  }, React.createElement(Form, {
    autoComplete: "off",
    className: classes.form,
    initialValues: initialValues,
    onSubmit: handleSubmit
  }, React.createElement("div", {
    className: classes.search
  }, React.createElement(SearchField, {
    location: location,
    onChange: handleChange,
    onFocus: handleFocus
  })), React.createElement("div", {
    className: classes.autocomplete
  }, React.createElement(Autocomplete, {
    setVisible: setExpanded,
    visible: expanded
  })))));
};

export default SearchBar;
SearchBar.propTypes = {
  classes: shape({
    autocomplete: string,
    container: string,
    form: string,
    root: string,
    root_open: string,
    search: string
  }),
  history: shape({
    push: func.isRequired
  }).isRequired,
  isOpen: bool,
  location: shape({}).isRequired
};
//# sourceMappingURL=searchBar.js.map