import React, { Fragment, useCallback, useEffect } from 'react';
import { bool, func, shape, string } from 'prop-types';
import { mergeClasses } from "../../../classify";
import Button from "../../Button";
import defaultClasses from "./receipt.css";

const Receipt = props => {
  const {
    createAccount,
    history,
    order,
    reset,
    user
  } = props;
  const classes = mergeClasses(defaultClasses, props.classes);
  useEffect(() => reset, [reset]);
  const handleCreateAccount = useCallback(() => {
    createAccount(history);
  }, [createAccount, history]);
  const handleViewOrderDetails = useCallback(() => {// TODO: Implement/connect/redirect to order details page.
  }, [order]);
  return React.createElement("div", {
    className: classes.root
  }, React.createElement("div", {
    className: classes.body
  }, React.createElement("h2", {
    className: classes.header
  }, "Thank you for your purchase!"), React.createElement("div", {
    className: classes.textBlock
  }, "You will receive an order confirmation email with order status and other details."), user.isSignedIn ? React.createElement(Fragment, null, React.createElement("div", {
    className: classes.textBlock
  }, "You can also visit your account page for more information."), React.createElement(Button, {
    onClick: handleViewOrderDetails
  }, "View Order Details")) : React.createElement(Fragment, null, React.createElement("hr", null), React.createElement("div", {
    className: classes.textBlock
  }, "Track order status and earn rewards for your purchase by creating an account."), React.createElement(Button, {
    priority: "high",
    onClick: handleCreateAccount
  }, "Create an Account"))));
};

Receipt.propTypes = {
  classes: shape({
    body: string,
    footer: string,
    root: string
  }),
  order: shape({
    id: string
  }).isRequired,
  createAccount: func.isRequired,
  reset: func.isRequired,
  user: shape({
    isSignedIn: bool
  })
};
Receipt.defaultProps = {
  order: {},
  reset: () => {},
  createAccount: () => {}
};
export default Receipt;
//# sourceMappingURL=receipt.js.map