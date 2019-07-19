import React, { Component } from 'react';
import { bool, shape, string } from 'prop-types';

import classify from 'src/classify';
import Footer from '../Footer';
import Header from 'src/simi/BaseComponents/Header'
import Identify from 'src/simi/Helper/Identify'
import Connection from 'src/simi/Network/SimiConnection'
import LoadingComponent  from 'src/simi/BaseComponents/Loading'
import * as Constants from 'src/simi/Config/Constants';
import storeConfigDataQuery from 'src/simi/queries/getStoreConfigData.graphql'
import simiStoreConfigDataQuery from 'src/simi/queries/simiconnector/getStoreConfigData.graphql'
import { Simiquery } from 'src/simi/Network/Query'
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

    mainContent(storeConfig = null) {
        const { classes } = this
        const { children } = this.props
        return (
            <React.Fragment>
                <Header storeConfig={storeConfig}/>
                <div id="data-breadcrumb"/>
                {storeConfig && <div className={classes.page} id="siminia-main-page">{children}</div>}
                <Footer />
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
                    this.mainContent(Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, Constants.STORE_CONFIG)) :
                    <Simiquery query={Identify.hasConnector()?simiStoreConfigDataQuery:storeConfigDataQuery}>
                        {({ data }) => {
                            if (data && data.storeConfig) {
                                Identify.saveStoreConfig(data)
                                return this.mainContent(data)
                            }
                            return this.mainContent()
                        }}
                    </Simiquery>
                }
            </main>
        );
    }
}

export default classify(defaultClasses)(Main);
