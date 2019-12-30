import React from 'react';
import { compose } from 'redux';
import {withRouter} from "react-router-dom";
import VendorDetail from "./VendorDetail";

const VendorDetailIndex = (props) => {
    const {match} = props;
    const vendorId = match && match.params && match.params.id || null;
    return <VendorDetail vendorId={vendorId} />
}

export default compose(withRouter)(VendorDetailIndex);