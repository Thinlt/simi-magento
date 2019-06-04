import React, { Component } from 'react';
import { Link } from "@magento/venia-drivers";
import "./notFound.css";

class NotFound extends Component {
  render() {
    return React.createElement("article", {
      className: "NotFound"
    }, React.createElement("h1", {
      className: "NotFound-title"
    }, React.createElement("span", null, "404 Error!")), React.createElement("section", {
      className: "NotFound-hero"
    }, React.createElement("h2", {
      className: "NotFound-hero-title"
    }, React.createElement("span", null, "We\u2019re Sorry!"))), React.createElement("section", {
      className: "NotFound-content"
    }, React.createElement("p", null, React.createElement("span", null, "We could not find the page you were trying to get to. Here are some suggestions to help you get back on track.")), React.createElement("div", {
      className: "NotFound-content-actions"
    }, React.createElement(Link, {
      className: "NotFound-content-actions-action",
      to: "/cart"
    }, React.createElement("span", null, "Your Cart")), React.createElement(Link, {
      className: "NotFound-content-actions-action",
      to: "/history"
    }, React.createElement("span", null, "Recently Viewed")))));
  }

}

export default NotFound;
//# sourceMappingURL=notFound.js.map