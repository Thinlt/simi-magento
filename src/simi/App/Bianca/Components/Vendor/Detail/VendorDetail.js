import React from 'react';
import {sendRequest} from 'src/simi/Network/RestMagento';
import ReactHTMLParse from 'react-html-parser';
import { withRouter } from 'react-router-dom';
import Loading from 'src/simi/BaseComponents/Loading';
// import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from "src/simi/Helper/Identify";
import {StaticRate} from 'src/simi/App/Bianca/BaseComponents/Rate';
import { TopReview } from './Review';
import { getOS } from 'src/simi/App/Bianca/Helper';
import IconPhone from 'src/simi/App/Bianca/BaseComponents/Icon/Telephone';
import IconEnvelopeOpen from 'src/simi/App/Bianca/BaseComponents/Icon/EnvelopeOpen';
// import IconSortAmount from 'src/simi/App/Bianca/BaseComponents/Icon/SortAmount';
import AllProduct from './AllProducts';
import {smoothScrollToView} from 'src/simi/Helper/Behavior';
import ReviewList from 'src/simi/App/Bianca/Components/Vendor/Detail/Review/ReviewList';


require('./style.scss');
// if (getOS() === 'MacOS') require('./home-ios.scss');

const $ = window.$;

class VendorDetail extends React.Component {

    state = {
        activeContent: 'products'
    }

    constructor(props){
        super(props);
        this.state.isPhone = window.innerWidth < 1024;

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
        this.state.contentRight = this.renderAllProducts();
    }

    componentDidMount(){
        if (this.state.id) {
            sendRequest(`/rest/V1/simiconnector/vendors/${this.state.id}`, (data) => {
                if (data && !data.errors) {
                    this.setState({
                        data: data
                    });
                    return;
                }
                this.props.history && this.props.history.push('/');
            }, 'GET', null, null);
        }
        window.onresize = () => {
            const isPhone = window.innerWidth < 1024;
            this.setState({isPhone: isPhone});
        }
    }
    
    componentDidUpdate(){
        if (this.clickActiveContent) {
            smoothScrollToView($('.vendor-body .cont-right'));
        }
        this.clickActiveContent = false;
    }

    renderAllProducts = () => {
        return <AllProduct vendorId={this.state.id} isPhone={this.state.isPhone}/>
    }

    renderReviews = () => {
        const {data} = this.state;
        const {reviews} = data || {}
        return (
            <div className="message-reviews">
                <div className="title">{Identify.__('Reviews')}</div>
                <div className="average-review">
                    <span>{Identify.__('Average review')}</span>
                    <StaticRate className="rate-star" rate={reviews.rate} size={24} width={137}/>
                    <span className="review-count">({reviews.number})</span>
                </div>
                <ReviewList vendorId={this.state.id} isPhone={this.state.isPhone}/>
            </div>
        );
    }

    renderAbout = () => {
        const {data} = this.state;
        const about = data && data.about || null
        return (
            <div className="about-store">{about ? ReactHTMLParse(about) : <div className="no-data">{Identify.__('No Data')}</div>}</div>
        );
    }

    renderFaqs = () => {
        const {data} = this.state;
        const faqs = data && data.faqs || null
        return (
            <div className="faqs-store">{faqs ? ReactHTMLParse(faqs) : <div className="no-data">{Identify.__('No Data')}</div>}</div>
        );
    }

    activeContent = (name) => {
        this.clickActiveContent = true;
        switch(name){
            case "products":
                this.setState({contentRight: this.renderAllProducts(), activeContent: name});
                break;
            case "reviews":
                this.setState({contentRight: this.renderReviews(), activeContent: name});
                break;
            case "about":
                this.setState({contentRight: this.renderAbout(), activeContent: name});
                break;
            case "faqs":
                this.setState({contentRight: this.renderFaqs(), activeContent: name});
                break;
            default:
                this.setState({contentRight: this.renderAllProducts(), activeContent: name});
        }
    }

    isActive = (name) => {
        const {activeContent} = this.state;
        return activeContent === name ? 'active' : '';
    }

    render(){
        const {isActive, activeContent} = this;
        const {data, contentRight} = this.state;
        const mediaPrefix = '/'+window.SMCONFIGS.media_url_prefix;
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
                                <div className="logo"><img src={mediaPrefix+data.logo_path} alt="Vendor banner"/></div>
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
                                {data.banner_path &&
                                    <img src={mediaPrefix+data.banner_path} alt="Vendor banner"/>
                                }
                            </div>
                        </div>
                    </div>
                </div>
                <div className="vendor-body">
                    <div className="container">
                        <div className="cont-left">
                            <div className="menu-items">
                                <div className={`item ${isActive('products')}`} onClick={() => activeContent('products')}>
                                    <span>{Identify.__('All Products')}</span>
                                </div>
                                <div className={`item ${isActive('reviews')}`} onClick={() => activeContent('reviews')}>
                                    <span>{Identify.__('Reviews')}</span>
                                </div>
                                <div className={`item ${isActive('about')}`} onClick={() => activeContent('about')}>
                                    <span>{Identify.__('About Store')}</span>
                                </div>
                                <div className={`item ${isActive('faqs')}`} onClick={() => activeContent('faqs')}>
                                    <span>{Identify.__('FAQs')}</span>
                                </div>
                            </div>
                        </div>
                        <div className="cont-right">
                            {contentRight}
                        </div>
                    </div>
                </div>

                <div className="vendor-header"></div>
            </div>
        );
    }
}

export default withRouter(VendorDetail)