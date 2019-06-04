import React, { Component } from 'react';
import classify from "../../classify";
import defaultClasses from "./indicator.css";
import logo from "../Logo/logo.svg";

class LoadingIndicator extends Component {
  render() {
    const {
      props
    } = this;
    const {
      children,
      classes
    } = props;
    return React.createElement("div", {
      className: classes.root
    }, React.createElement("img", {
      className: classes.indicator,
      src: logo,
      width: "64",
      height: "64",
      alt: "Loading indicator"
    }), React.createElement("span", {
      className: classes.message
    }, children));
  }

}

export default classify(defaultClasses)(LoadingIndicator);
//# sourceMappingURL=indicator.js.map