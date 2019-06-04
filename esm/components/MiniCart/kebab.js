function _extends() { _extends = Object.assign || function (target) { for (var i = 1; i < arguments.length; i++) { var source = arguments[i]; for (var key in source) { if (Object.prototype.hasOwnProperty.call(source, key)) { target[key] = source[key]; } } } return target; }; return _extends.apply(this, arguments); }

function _objectWithoutProperties(source, excluded) { if (source == null) return {}; var target = _objectWithoutPropertiesLoose(source, excluded); var key, i; if (Object.getOwnPropertySymbols) { var sourceSymbolKeys = Object.getOwnPropertySymbols(source); for (i = 0; i < sourceSymbolKeys.length; i++) { key = sourceSymbolKeys[i]; if (excluded.indexOf(key) >= 0) continue; if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue; target[key] = source[key]; } } return target; }

function _objectWithoutPropertiesLoose(source, excluded) { if (source == null) return {}; var target = {}; var sourceKeys = Object.keys(source); var key, i; for (i = 0; i < sourceKeys.length; i++) { key = sourceKeys[i]; if (excluded.indexOf(key) >= 0) continue; target[key] = source[key]; } return target; }

function _defineProperty(obj, key, value) { if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }

import React, { Component, createRef } from 'react';
import { shape, string } from 'prop-types';
import Icon from "../Icon";
import classify from "../../classify";
import defaultClasses from "./kebab.css";
import MoreVerticalIcon from 'react-feather/dist/icons/more-vertical';

class Kebab extends Component {
  constructor(props) {
    super(props);

    _defineProperty(this, "handleDocumentClick", event => {
      this.kebabButtonRef.current.contains(event.target) ? this.setState({
        isOpen: true
      }) : this.setState({
        isOpen: false
      });
    });

    this.kebabButtonRef = createRef();
    this.state = {
      isOpen: false
    };
  }

  componentDidMount() {
    document.addEventListener('click', this.handleDocumentClick);
    document.addEventListener('touchend', this.handleDocumentClick);
  }

  componentWillUnmount() {
    document.removeEventListener('click', this.handleDocumentClick);
    document.removeEventListener('touchend', this.handleDocumentClick);
  }

  render() {
    const _this$props = this.props,
          {
      classes,
      children
    } = _this$props,
          restProps = _objectWithoutProperties(_this$props, ["classes", "children"]);

    const toggleClass = this.state.isOpen ? classes.dropdown_active : classes.dropdown;
    return React.createElement("div", _extends({}, restProps, {
      className: classes.root
    }), React.createElement("button", {
      className: classes.kebab,
      ref: this.kebabButtonRef
    }, React.createElement(Icon, {
      src: MoreVerticalIcon
    })), React.createElement("ul", {
      className: toggleClass
    }, children));
  }

}

_defineProperty(Kebab, "propTypes", {
  classes: shape({
    dropdown: string,
    dropdown_active: string,
    kebab: string,
    root: string
  })
});

export default classify(defaultClasses)(Kebab);
//# sourceMappingURL=kebab.js.map