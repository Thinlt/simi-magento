import React from 'react';
import {sendRequest} from 'src/simi/Network/RestMagento';
import Loading from 'src/simi/BaseComponents/Loading';
import Identify from "src/simi/Helper/Identify";
import { TopReview } from './Review';
import { getOS } from 'src/simi/App/Bianca/Helper';
import IconPhone from 'src/simi/App/Bianca/BaseComponents/Icon/Telephone';
import IconEnvelopeOpen from 'src/simi/App/Bianca/BaseComponents/Icon/EnvelopeOpen';
import IconSortAmount from 'src/simi/App/Bianca/BaseComponents/Icon/SortAmount';
import AllProduct from './AllProducts';

require('./style.scss');
// if (getOS() === 'MacOS') require('./home-ios.scss');

class VendorDetail extends React.Component {

    state = {}

    constructor(props){
        super(props);
        this.state = {isPhone: window.innerWidth < 1024}

        const {vendorId} = props;
        const storeConfig = Identify.getStoreConfig() || {};
        const {config} = storeConfig && storeConfig.simiStoreConfig || {};
        const {vendor_list} = config || {};
        if (vendor_list && vendor_list instanceof Array) {
            let vendor = vendor_list.find((item) => {
                if (item.vendor_id === vendorId) return true;
                return false;
            });
            if (vendor) {
                this.state.id = vendor.entity_id;
            } else {
                this.state.id = vendorId;
            }
        }
    }

    componentDidMount(){
        if (this.state.id) {
            sendRequest(`/rest/V1/simiconnector/vendors/${this.state.id}`, (data) => {
                if (data) {
                    this.setState({
                        data: data
                    });
                } else {
                    
                }
            }, 'GET', null, null);
        }
        window.onresize = () => {
            const isPhone = window.innerWidth < 1024;
            console.log(isPhone)
            this.setState({isPhone: isPhone});
        }
    }

    render(){
        const {data} = this.state;
        if (!data) return <Loading />

        let name = data.firstname || null;
        name = data.middlename ? `${name} ${data.middlename}` : name;
        name = data.lastname ? `${name} ${data.lastname}` : name;

        const {profile, reviews, telephone} = data || {}
        const storeName = profile && profile.store_name || name;
        const phone_number = profile && profile.phone_number || telephone;

        return (
            <div className={`vendor-detail ${this.state.isPhone?'mobile':''} ${(getOS() === 'MacOS')?'MacOS':''}`}>
                <div className="vendor-header">
                    <div className="container">
                        <div className="cont-left">
                            <div className="store-info">
                                <div className="logo"><img src={data.logo_path} alt="Vendor banner"/></div>
                                <div className="name">{Identify.__(storeName)}</div>
                                <div className="location">{Identify.__(profile.address)}</div>
                                <div className="description">{Identify.__(profile.description)}</div>
                                <div className="reviews">
                                    <TopReview reviews={reviews}/>
                                </div>
                                <div className="phone"><IconPhone /><span>{Identify.__(phone_number)}</span></div>
                                <div className="email"><IconEnvelopeOpen /><span>{Identify.__(data.email)}</span></div>
                            </div>
                        </div>
                        <div className="cont-right">
                            <div className="banner-info">
                                <img src={data.banner_path} alt="Vendor banner"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div className="vendor-body">
                    <div className="container">
                        <div className="cont-left">
                            <div className="menu-items">
                                <div className="item active">
                                    <span>{Identify.__('All Products')}</span>
                                </div>
                                <div className="item">
                                    <span>{Identify.__('Reviews')}</span>
                                </div>
                                <div className="item">
                                    <span>{Identify.__('Abount Store')}</span>
                                </div>
                                <div className="item">
                                    <span>{Identify.__('FAQs')}</span>
                                </div>
                            </div>
                        </div>
                        <div className="cont-right">
                            <AllProduct vendorId={this.state.id}/>
                        </div>
                    </div>
                </div>

                <div className="vendor-header"></div>
            </div>
        );
    }
}

export default VendorDetail