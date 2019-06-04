import React from 'react';
import { Switch, Route } from "@magento/venia-drivers";
import { Page } from '@magento/peregrine';
import ErrorView from "../ErrorView";
import CreateAccountPage from "../CreateAccountPage";
import Search from "../../RootComponents/Search";

const renderRoutingError = props => React.createElement(ErrorView, props);

const renderRoutes = () => React.createElement(Switch, null, React.createElement(Route, {
  exact: true,
  path: "/search.html",
  component: Search
}), React.createElement(Route, {
  exact: true,
  path: "/create-account",
  component: CreateAccountPage
}), React.createElement(Route, {
  render: () => React.createElement(Page, null, renderRoutingError)
}));

export default renderRoutes;
//# sourceMappingURL=renderRoutes.js.map