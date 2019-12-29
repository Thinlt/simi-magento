import React from 'react';
import {sendRequest} from 'src/simi/Network/RestMagento';
import Loading from 'src/simi/BaseComponents/Loading';
import Identify from "src/simi/Helper/Identify";
import { TopReview } from './Review';
import { getOS } from 'src/simi/App/Bianca/Helper';
import IconPhone from 'src/simi/App/Bianca/BaseComponents/Icon/Telephone';
import EnvelopeOpen from 'src/simi/App/Bianca/BaseComponents/Icon/EnvelopeOpen';

require('./style.scss');
// if (getOS() === 'MacOS') require('./home-ios.scss');

class VendorDetail extends React.Component {

    state = {}

    constructor(props){
        super(props);
        console.log(props)
        this.state = {isPhone: window.innerWidth < 1024}
    }

    componentDidMount(){
        const { vendorId } = this.props;
        if (vendorId) {
            sendRequest(`/rest/V1/simiconnector/vendors/${vendorId}`, (data) => {
                if (data) {
                    this.setState({
                        data: data
                    });
                } else {
                    
                }
            }, 'GET', null, null);
        }
        window.onresize = function () {
            const isPhone = window.innerWidth < 1024;
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
                                <div className="email"><EnvelopeOpen /><span>{Identify.__(data.email)}</span></div>
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
                        <div className="cont-left"></div>
                        <div className="cont-right"></div>
                    </div>
                </div>

                <div className="vendor-header"></div>
            </div>
        );
    }
}

export default VendorDetail