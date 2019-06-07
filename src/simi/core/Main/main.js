import React, { Component } from 'react';
import { bool, shape, string } from 'prop-types';

import classify from 'src/classify';
import Header from 'src/simi/BaseComponents/Header'
import Identify from 'src/simi/Helper/Identify'
import Connection from 'src/simi/Network/Connection'
import LoadingComponent  from 'src/simi/BaseComponents/Loading'
import * as Constants from 'src/simi/Config/Constants';
import storeConfigDataQuery from 'src/simi/queries/getStoreConfigData.graphql'
import { Query } from 'src/drivers'
import defaultClasses from './main.css';

class Main extends Component {

    componentDidMount() {
        const dbConfig = Identify.getAppDashboardConfigs()
        if (!dbConfig) {
            Connection.connectSimiCartServer('GET', true, this);
        }
    }

    static propTypes = {
        classes: shape({
            page: string,
            page_masked: string,
            root: string,
            root_masked: string
        }),
        isMasked: bool
    };

    get classes() {
        const { classes, isMasked } = this.props;
        const suffix = isMasked ? '_masked' : '';

        return ['page', 'root'].reduce(
            (acc, val) => ({ ...acc, [val]: classes[`${val}${suffix}`] }),
            {}
        );
    }

    get mainContent() {
        const { classes } = this
        const { children } = this.props
        return (
            <React.Fragment>
                <Header />
                <div id="data-breadcrumb"/>
                <div className={classes.page}>{children}</div>
            </React.Fragment>
        )
    }
    render() {
        const { classes } = this
        return (
            <main className={classes.root}>
                <div className="app-loading" style={{display:'none'}} id="app-loading">
                    <LoadingComponent/>
                </div>
                { Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, Constants.STORE_CONFIG) ?
                    this.mainContent :
                    <Query query={storeConfigDataQuery}>
                        {({ loading, error, data }) => {
                            if (data)
                                Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, Constants.STORE_CONFIG, data);
                            return this.mainContent
                        }}
                    </Query>
                }
            </main>
        );
    }
}

export default classify(defaultClasses)(Main);
