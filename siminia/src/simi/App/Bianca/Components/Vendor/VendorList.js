import React, {useState} from 'react';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
import Identify from "src/simi/Helper/Identify";
import { getOS } from 'src/simi/App/Bianca/Helper';
// import Loading from 'src/simi/BaseComponents/Loading';
import {smoothScrollToView} from 'src/simi/Helper/Behavior';
import { withRouter } from 'react-router-dom';


require('./VendorList.scss');

const $ = window.$;

const VendorList = (props) => {

    const windowSize = useWindowSize();
    const isPhone = windowSize.width < 1024;
    const storeConfig = Identify.getStoreConfig() || {};
    const {vendor_list} = storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
    const [curChar, setCurChar] = useState();

    const chars = Array.from("ABCDEFGHIJKLMNOPQRSTUVWXYZ");

    const scrollToChar = (char) => {
        smoothScrollToView($(`.listing .group.${char}`));
        setCurChar(char);
        return char;
    }

    const renderFirstChars = () => {
        return chars.map(c => 
            <span onClick={() => scrollToChar(c)} className={`${curChar===c?'active':''}`} key={c}>
                {Identify.__(c)}
            </span>
        );
    }

    const getStoreName = (vendor) => {
        let storeName = vendor.middlename ? `${vendor.firstname} ${vendor.middlename}` : vendor.firstname;
        storeName = vendor.lastname ? `${storeName} ${vendor.lastname}` : storeName;
        return vendor && vendor.profile && vendor.profile.store_name || storeName;
    }

    const getVendorGroups = () => {
        let groups = {};
        if (vendor_list && vendor_list.length) {
            vendor_list.sort((vendorA, vendorB)=> getStoreName(vendorA).localeCompare(getStoreName(vendorB)));//sort by alphabet
            vendor_list.map((vendor) => {
                const name = getStoreName(vendor).toUpperCase();
                if(!(groups[name[0]] instanceof Array)) groups[name[0]] = [];
                groups[name[0]].push(vendor);
                return vendor;
            });
        }
        return groups;
    }

    const viewVendorDetail = (id) => {
        // props.history && id && props.history.push(`/designers/${id}.html`);
        return props.history && id && `/designers/${id}.html` || '#';
    }

    const renderList = () => {
        const groups = getVendorGroups();
        return chars.map((char) => {
            if (groups[char] && groups[char].length) {
                return (
                    <div className={`group ${char}`} key={char}>
                        <div className={`group-name`}>{Identify.__(char)}</div>
                        <div className="items">
                            {
                                groups[char].map((vendor, index) => {
                                    return <div className={`vendor-name ${vendor.vendor_id}`} key={index}>
                                        <a href={viewVendorDetail(vendor.vendor_id)} title={getStoreName(vendor)}>
                                            {Identify.__(getStoreName(vendor))}
                                        </a>
                                    </div>
                                })
                            }
                        </div>
                    </div>
                )
            }
            return null;
        });
    }

    return (
        <div className={`vendor-list ${isPhone?'mobile':''} ${getOS()}`}>
            <div className="container">
                <div className="alphabet">
                    <div className="title">{Identify.__('DESIGNER')}</div>
                    <div className="abcxyz">
                        {renderFirstChars()}
                    </div>
                </div>
                <div className="listing">
                    {renderList()}
                </div>
            </div>
        </div>
    );
}

export default withRouter(VendorList)