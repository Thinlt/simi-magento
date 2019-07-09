import React, { useState, useEffect } from 'react';
// import { object } from 'prop-types';
import classify from 'src/classify';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import Loading from "src/simi/BaseComponents/Loading";
// import gql from 'graphql-tag';
// import { useQuery } from '@magento/peregrine';
// import { Query } from 'src/drivers';
import { simiUseQuery } from 'src/simi/Network/Query';
// import { Mutation } from 'react-apollo';
import CUSTOMER_ADDRESS from 'src/simi/queries/customerAddress.graphql';
// import GET_COUNTRIES from 'src/simi/queries/getCountries.graphql';
import List from './list';
import Edit from './edit';
import defaultClasses from './style.css';

const AddressBook = props => {
    
    const {user, classes} = props;
    const [queryResult, queryApi] = simiUseQuery(CUSTOMER_ADDRESS, false);
    const { data } = queryResult;
    const { runQuery } = queryApi;
    // const [queryResultCountries, queryApiCountries] = simiUseQuery(GET_COUNTRIES, true);
    // const dataCountries = queryResultCountries.data;
    // const runQueryCountries = queryApiCountries.runQuery;

    const getAddresses = () => {
        runQuery({});
    }

    // const getCountries = () => {
    //     runQueryCountries({});
    // }

    useEffect(() => {
        if(!data) {
            getAddresses()
        };
        // if(!dataCountries) {
        //     getCountries()
        // };
    }, [data]);

    const [ isEditAddress, setIsEditAddress ] = useState(null);
    // const [ editAddressId, setEditAddressId ] = useState(null);
    const [ addressEditing, setAddressEditing ] = useState(null);
    const { customer, countries } = data || {};
    const { addresses } = customer || {};

    var defaultBilling = {};
    var defaultShipping = {};
    var addressList = []; //other address list
    // var addressEditing = null; //address item

    for (var addrNo in addresses) {
        if (addresses[addrNo].default_billing) {
            defaultBilling = addresses[addrNo];
        }
        if (addresses[addrNo].default_shipping) {
            defaultShipping = addresses[addrNo];
        }
        if (!addresses[addrNo].default_billing && !addresses[addrNo].default_shipping) {
            var item = addresses[addrNo];
            // get country
            var country = {}
            for (var idx in countries) {
                if (countries[idx].id === item.country_id) {
                    country = countries[idx];
                    break;
                }
            }
            item.region_code = item.region.region_code
            item.country = country.full_name_locale
            addressList.push(item)
        }
    }

    var defaultBillingCountry = {}
    var defaultShippingCountry = {}
    // const { countries } = dataCountries || [];
    for (var idx in countries) {
        if (countries[idx].id === defaultBilling.country_id) {
            defaultBillingCountry = countries[idx];
        }
        if (countries[idx].id === defaultShipping.country_id) {
            defaultShippingCountry = countries[idx];
        }
    }

    //id - address id
    const editDefaultAddressHandle = (e, id, addressType) => {
        e.preventDefault();
        if (addressType === 'billing') {
            var address = defaultBilling;
        }
        if (addressType === 'shipping') {
            var address = defaultShipping;
        }
        setAddressEditing(address);
        setIsEditAddress(id);
        // setEditAddressId(id);
        return e;
    }


    //id - is index of items array
    const editAddressOther = (id) => {
        let address = typeof addressList[id] !== 'undefined' ? addressList[id] : null;
        setAddressEditing(address);
        setIsEditAddress(id);
        // setEditAddressId(id);
    }

    const deleteAddressOther = (id) => {
        addressEditing = typeof addressList[id] !== 'undefined' ? addressList[id] : null;
        console.log('delete address', addressEditing)
    }

    const renderDefaultAddress = () => {
        return (
            <div className="address-content">
                <div className="billing-address">
                    <span className="box-title">{Identify.__("Default Billing Address")}</span>
                    <div className="box-content">
                        <address>
                            {defaultBilling.firstname} {defaultBilling.lastname}<br/>
                            {defaultBilling.street ? <>{defaultBilling.street}<br/></> : ''}
                            {defaultBilling.postcode ? <>{defaultBilling.postcode}, </> : ''}
                            {defaultBilling.city ? <>{defaultBilling.city}, </>: ''}
                            {defaultBilling.region ? <>{defaultBilling.region.region_code}<br/></>: ''}
                            {defaultBillingCountry.full_name_locale ? <>{defaultBillingCountry.full_name_locale}<br/></> : ''}
                            {defaultBilling.telephone && 
                                <>
                                    T: <a href={"tel:"+defaultBilling.telephone}>{defaultBilling.telephone}</a>
                                </>
                            }
                        </address>
                    </div>
                    <div className="box-action">
                        <a href="" onClick={e => editDefaultAddressHandle(e, defaultBilling.id, 'billing')}><span>{Identify.__("Change Billing Address")}</span></a>
                    </div>
                </div>
                <div className="shipping-address">
                    <span className="box-title">{Identify.__("Default Shipping Address")}</span>
                    <div className="box-content">
                        <address>
                            {defaultShipping.firstname} {defaultShipping.lastname}<br/>
                            {defaultShipping.street ? <>{defaultShipping.street}<br/></> : ''}
                            {defaultShipping.postcode ? <>{defaultShipping.postcode}, </> : ''}
                            {defaultShipping.city ? <>{defaultShipping.city}, </>: ''}
                            {defaultShipping.region ? <>{defaultShipping.region.region_code}<br/></>: ''}
                            {defaultShippingCountry.full_name_locale ? <>{defaultShippingCountry.full_name_locale}<br/></> : ''}
                            {defaultShipping.telephone && 
                                <>
                                    T: <a href={"tel:"+defaultShipping.telephone}>{defaultShipping.telephone}</a>
                                </>
                            }
                        </address>
                    </div>
                    <div className="box-action">
                        <a href="" onClick={e => editDefaultAddressHandle(e, defaultShipping.id, 'shipping')}><span>{Identify.__("Change Shipping Address")}</span></a>
                    </div>
                </div>
            </div>
        )
    }

    const renderAddressList = () => {
        //fake dataItems
        if (typeof addressList[0] !== 'undefined') {
            for(var i=2; i<=101; i++){
                let myitem = {...addressList[0]}
                myitem.firstname = myitem.firstname + ' ' + i
                addressList.push(myitem)
            }
        }
        return <List items={addressList} editAddress={(id) => editAddressOther(id)} deleteAddress={(id) => deleteAddressOther(id)}/>
    }

    return (
        <div className={classes["address-book"]}>
            {isEditAddress && addressEditing ? 
                <Edit setIsEditAddress={setIsEditAddress} addressData={addressEditing} countries={countries} user={user} />
            :
            <>
                {TitleHelper.renderMetaHeader({title:Identify.__('Address Book')})}
                <h1>{Identify.__("Address Book")}</h1>
                <div className="default-address">
                    <div className="address-label">{Identify.__("Default Addresses")}</div>
                    {renderDefaultAddress()}
                </div>
                <div className="additional-address">
                    <div className="address-label">{Identify.__("Additional Address Entries")}</div>
                    {renderAddressList()}
                </div>
            </>
            }
        </div>
    );
}

const mapStateToProps = ({ user }) => {
    const { currentUser } = user
    return {
        user: currentUser
    };
}

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps
    )
)(AddressBook);
