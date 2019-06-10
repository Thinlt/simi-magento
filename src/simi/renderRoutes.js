import React from 'react';
import { Switch, Route } from 'src/drivers';
import { Page } from '@magento/peregrine';
import ErrorView from 'src/components/ErrorView/index';
import CreateAccountPage from 'src/components/CreateAccountPage/index';
import Cart from 'src/simi/core/Cart';
import Product from 'src/simi/core/Product';
import Search from 'src/RootComponents/Search';

const renderRoutingError = props => <ErrorView {...props} />;

const renderRoutes = () => (
    <Switch>
        <Route exact path="/search.html" component={Search} />
        <Route exact path="/create-account" component={CreateAccountPage} />
        <Route exact path="/cart.html" component={Cart} />
        <Route exact path="/product.html" component={Product} />
        <Route render={() => <Page>{renderRoutingError}</Page>} />
    </Switch>
);

export default renderRoutes;
