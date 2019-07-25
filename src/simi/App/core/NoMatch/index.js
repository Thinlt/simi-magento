import React from 'react'
import ErrorView from 'src/simi/BaseComponents/ErrorView/index';
import { Page } from '@magento/peregrine';
const renderRoutingError = props => <ErrorView {...props} />;

const NoMatch = props => {
    console.log(props)
    return (
        <Page>
            {renderRoutingError}
        </Page>
    )
}
export default NoMatch