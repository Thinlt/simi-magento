import React, { Component, Suspense } from 'react';
import { withRouter } from 'react-router-dom';
import { arrayOf, bool, number, shape, string, object } from 'prop-types';
import {smoothScrollToView} from 'src/simi/Helper/Behavior'
import Loading from 'src/simi/BaseComponents/Loading'
import { Colorbtn, Whitebtn } from 'src/simi/BaseComponents/Button'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import ProductImage from './ProductImage';
// import Quantity from './ProductQuantity';
import isProductConfigurable from 'src/util/isProductConfigurable';
import Identify from 'src/simi/Helper/Identify';
import * as Constants from 'src/simi/Config/Constants';
import TitleHelper from 'src/simi/Helper/TitleHelper'
import {prepareProduct} from 'src/simi/Helper/Product'
import ProductPrice from '../Component/Productprice';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { addToWishlist as simiAddToWishlist } from 'src/simi/Model/Wishlist';
// import {configColor} from 'src/simi/Config'
import {showToastMessage} from 'src/simi/Helper/Message';
import ReactHTMLParse from 'react-html-parser';
import BreadCrumb from "src/simi/App/Bianca/BaseComponents/BreadCrumb";
import Tabs from "src/simi/App/Bianca/BaseComponents/Tabs";
import { TopReview, ReviewList, NewReview } from './Review'
import SocialShare from 'src/simi/App/Bianca/BaseComponents/SocialShare';
import Description from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Description';
// import Techspec from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Techspec';
import LinkedProduct from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/LinkedProduct';
import Favorite from 'src/simi/App/Bianca/BaseComponents/Icon/Favorite';
import CompareIcon from 'src/simi/App/Bianca/BaseComponents/Icon/SyncCompare';
import CloseIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Close';
import Modal from 'react-responsive-modal';
import {sendRequest} from 'src/simi/Network/RestMagento';
import { compose } from 'redux';
import { connect } from 'src/drivers';
import { getOS } from 'src/simi/App/Bianca/Helper';
import CompareProduct from 'src/simi/App/Bianca/BaseComponents/CompareProducts';
import { getProductDetail } from 'src/simi/Model/Product';

const ConfigurableOptions = React.lazy(() => import('./Options/ConfigurableOptions'));
const CustomOptions = React.lazy(() => import('./Options/CustomOptions'));
const BundleOptions = React.lazy(() => import('./Options/Bundle'));
const GroupedOptions = React.lazy(() => import('./Options/GroupedOptions'));
const DownloadableOptions = React.lazy(() => import('./Options/DownloadableOptions'));
const GiftcardOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/GiftcardOptions'));
const TrytobuyOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/TrytobuyOptions'));

import {addRecentViewedProducts} from '../../../Helper/Biancaproduct'
import { productUrlSuffix } from 'src/simi/Helper/Url';
import SizeGuide from 'src/simi/App/Bianca/Components/Product/SizeGuide';
import Select from 'src/simi/App/Bianca/BaseComponents/FormInput/Select';
import { Link } from 'src/drivers';

import { analyticAddCartGTM, analyticsViewDetailsGTM } from 'src/simi/Helper/Analytics'
import { resourceUrl } from 'src/simi/Helper/Url'

require('./productFullDetail.scss');
if (getOS() === 'MacOS') {
    require('src/simi/App/Bianca/Components/Product/ProductFullDetail/style-macos.scss');
}

/* Product data structure for SEO */
window.productDataStructure = {
    "@context": "https://schema.org/",
    "@type": "Product",
}

class ProductFullDetail extends Component {  
    constructor(props) {
        super(props)
        if (props.product && props.product.small_image)
            addRecentViewedProducts(props.product)
    }

    state = {
        optionCodes: new Map(),
        optionSelections: new Map(),
        openModal: false,
        reserveSuccess: false,
        reserveModalMessage: false,
        reserveError: '',
        isOpenSizeGuide: false,
        isErrorPreorder: false,
        isPreorder: false,
        openCompareModal: false,
        isPhone: window.innerWidth < 1024
    };

    quantity = 1;
    stores = []; // Storelocators
    storesOptions = []; // Storelocators option value
    reserveSelectTrigger = React.createRef();
    reserveStoreId = '';
    isPreorder = false;

    getIsPhone = () => {
        if (this.state.isPhone === null) {
            return this.isPhone;
        }
        return this.state.isPhone;
    }

    resizePhone = () => {
        window.onresize = () => {
            const isPhone = window.innerWidth < 1024;
            this.setState({isPhone: isPhone});
        }
    }

    componentDidMount(){
        smoothScrollToView($('#siminia-main-page'));
        this.getStoreLocations();
        //get user detail when missing (from refreshing) - fix error
        if (this.props.isSignedIn && !this.props.customerId && this.props.getUserDetails){
            this.props.getUserDetails();
        }
        this.resizePhone();
        this.isPhone = window.innerWidth < 1024;

        const { product } = this.props;
        //add google structure data for SEO
        if(product){
            var stData = document.createElement("script");
            stData.type = "application/ld+json";
            var image = product.media_gallery_entries && product.media_gallery_entries.length && product.media_gallery_entries[0] || null;
            const src = image && image.file ? window.location.origin+resourceUrl(image.file, { type: 'image-product', width: 640 }) : '';
            const storeConfig = Identify.getStoreConfig()
            const config = storeConfig && storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
            const extraAttributes = product.simiExtraField && product.simiExtraField.attribute_values || {};
            const reviewRating = product.simiExtraField && product.simiExtraField.app_reviews || {};
            const { brands } = config;
            const { brand, url_key, pre_order, is_salable } = extraAttributes;
            const pBrand = brands.find((br)=>br.option_id === brand);
            const price = product.price && (product.price.minimalPrice || product.price.regularPrice) || {};
            const sellerName = this.getVendorStoreName();
            window.productDataStructure = {...window.productDataStructure,
                "name": product.name,
                "image": [src],
                "description": product.short_description && product.short_description.html.replace(/(<([^>]+)>)/ig,"") 
                            || product.description && product.description.html.replace(/(<([^>]+)>)/ig,"") || '',
                "sku": product.sku,
                "mpn": product.mpn || '',
                "mpn": product.gtin || '',
                "mpn": product.isbn || '',
                "brand": {
                    "@type": "Thing",
                    "name": pBrand && pBrand.name || ''
                },
                "offers": {
                    "@type": "Offer",
                    "url": url_key && window.location.origin+'/'+url_key+'.'+productUrlSuffix() || '',
                    "priceCurrency": price.amount && price.amount.currency,
                    "price": price.amount && price.amount.value,
                    "itemCondition": product.itemCondition || '',
                    "availability": parseInt(pre_order) === 1 && !is_salable ? 'PreOrder': !is_salable?'OutOfStock':'InStock',
                    "seller": {
                        "@type": sellerName ? "Person":"Organization",//or Organization
                        "name": sellerName
                    }
                }
            }
            if (reviewRating && reviewRating.rate && reviewRating.number) {
                window.productDataStructure.aggregateRating = {
                    "@type": "AggregateRating",
                    "ratingValue": reviewRating.rate,
                    "ratingCount": reviewRating.number
                }
            }
            stData.innerHTML = JSON.stringify(window.productDataStructure);
            document.head.appendChild(stData);
        }
    }

    getStoreLocations = (callback) => {
        sendRequest('/rest/V1/simiconnector/storelocations', (data) => {
            if (data && data.storelocations) {
                this.stores = data.storelocations;
                this.storesOptions = this.stores && this.stores.map((item) => {
                    return { label: item.store_name, value: item.simistorelocator_id }
                }) || []
                if (callback) callback(this.stores);
            }
        });
        return this.stores;
    }
      
    static getDerivedStateFromProps(props, state) {
        const { configurable_options } = props.product;
        const optionCodes = new Map(state.optionCodes);
        // if this is a simple product, do nothing
        if (!isProductConfigurable(props.product) || !configurable_options) {
            return null;
        }
        // otherwise, cache attribute codes to avoid lookup cost later
        for (const option of configurable_options) {
            optionCodes.set(option.attribute_id, option.attribute_code);
        }
        return { optionCodes };
    }

    setQuantity = quantity => this.quantity = quantity;

    prepareParams = () => {
        const { props, state, quantity } = this;
        const { optionSelections } = state;
        const { product } = props;
        const params = {product: String(product.id), qty: quantity?String(quantity):'1'}
        if (this.customOption) {
            const customOptParams = this.customOption.getParams()
            if (customOptParams && customOptParams.options) {
                params['options'] = customOptParams.options
            } else
                this.missingOption = true
        }
        if (this.bundleOption) {
            const bundleOptParams = this.bundleOption.getParams()
            if (bundleOptParams && bundleOptParams.bundle_option_qty && bundleOptParams.bundle_option) {
                params['bundle_option'] = bundleOptParams.bundle_option
                params['bundle_option_qty'] = bundleOptParams.bundle_option_qty
            }
        }
        if (this.groupedOption) {
            const groupedOptionParams = this.groupedOption.getParams()
            if (groupedOptionParams && groupedOptionParams.super_group) {
                params['super_group'] = groupedOptionParams.super_group
            }
        }
        if (this.downloadableOption) {
            const downloadableOption = this.downloadableOption.getParams()
            if (downloadableOption && downloadableOption.links) {
                params['links'] = downloadableOption.links
            } else
                this.missingOption = true
        }
        if (this.giftcardOption) {
            const giftcardOptParams = this.giftcardOption.getParams()
            for (const attr in giftcardOptParams) {
                if (attr === 'product' || attr === 'qty') continue;
                params[attr] = giftcardOptParams[attr];
            }
            if (!giftcardOptParams) {
                this.missingOption = true
            }
        }
        if (this.isMissingConfigurableOptions) {
            this.missingOption = true
        }
        if (optionSelections && optionSelections.size) { //configurable option
            const super_attribute = {}
            optionSelections.forEach((value, key) => {
                super_attribute[String(key)] = String(value)
            })
            params['super_attribute'] = super_attribute
        }
        
        if (this.trytobuyOptionsRef) {
            params['try_to_buy'] = this.trytobuyOptionsRef.checked ? 1 : 0;
        }

        return params
    }

    checkLogin = (params) => {
        // check login for try-to-buy or pre-order
        if (parseInt(params.pre_order) === 1 || parseInt(params.try_to_buy) === 1) {
            if (!this.props.isSignedIn) {
                // showToastMessage(Identify.__('Please login first'));
                setTimeout(()=>{
                    this.props.history.push({pathname: '/login.html', pushTo: this.props.history.location.pathname});
                }, 10);
                return false;
            }
        }
        return true;
    }

    addToCart = () => {
        const { props } = this;
        const { product } = props;
        if (product && product.id) {
            this.missingOption = false
            const params = this.prepareParams()
            if (this.missingOption) {
                showToastMessage(Identify.__('Please select the options required (*)'));
                return
            }
            // check login
            if(!this.checkLogin(params)) return;
            showFogLoading()
            simiAddToCart(this.addToCartCallBack, params)
        }
    };

    addToCartWithParams = (data = {}) => {
        const { product } = this.props;
        if (product && product.id) {
            this.missingOption = false
            const params = {...this.prepareParams(), ...data};
            if (this.missingOption) {
                showToastMessage(Identify.__('Please select the options required (*)'));
                return
            }
            // check login
            if(!this.checkLogin(params)) return;
            showFogLoading()
            simiAddToCart(this.addToCartCallBack, params)
            this.isPreorder = false;
            this.isBuy1Click = false;
            if (params.pre_order && parseInt(params.pre_order) === 1) {
                this.isPreorder = true;
            }
            if (params.buy1click && parseInt(params.buy1click) === 1) {
                this.isBuy1Click = true;
            }
        }
    };

    preorderAction = () => {
        this.addToCartWithParams({pre_order: '1'})
    }

    buy1ClickAction = (cartParams) => {
        this.addToCartWithParams(cartParams);
        this.isBuy1Click = true;
    }

    addToCartCallBack = (data) => {
        if (data.errors) {
            this.showError(data)
            if (this.isPreorder) {
                this.setState({isErrorPreorder: true});
            }
        } else {
            this.props.updateItemInCart()
            if (this.isBuy1Click) {
                showToastMessage(Identify.__('Checkout processing..'));
                (new Promise(resolve => setTimeout(resolve, 2000))).then(() => {
                    hideFogLoading();
                    this.props.history.push('/checkout.html');
                });
                return;
            }
            this.showSuccess(data)
            analyticAddCartGTM(this.props.product.name, this.props.product.id, this.props.product.price)
        }
        hideFogLoading()
    }

    addToWishlist = () => {
        const {product, isSignedIn, history} = this.props
        if (!isSignedIn) {
            history.push({pathname: '/login.html', pushTo: this.props.history.location.pathname})
        } else if (product && product.id) {
            this.missingOption = false
            const params = this.prepareParams()
            showFogLoading()
            simiAddToWishlist(this.addToWishlistCallBack, params)
        }
    }

    addToWishlistCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
            this.showError(data)
        } else {
            this.props.toggleMessages([{
                type: 'success',
                message: Identify.__('Product was added to your wishlist'),
                auto_dismiss: true
            }])
        }
    }

    showModalCompare = () => {
        this.setState({
            openCompareModal : true
        })
    }

    closeCompareModal = () =>{
        this.setState({
            openCompareModal : false
        })
    }

    addToCompare = () => {
        const { product } = this.props;
        const storeageData = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE,'compare_product');
        let compareProducts;
        if(storeageData){
            compareProducts = storeageData;
            const result = compareProducts.find(item => item.entity_id == product.id)
            if(result){
                this.showModalCompare();
                showToastMessage(Identify.__('Product has already added'.toUpperCase()))
            } else {
                showFogLoading();
                getProductDetail(this.compareCallBack, product.id)
            }
        } else {
            showFogLoading();
            getProductDetail(this.compareCallBack,product.id)
        }
    }

    compareCallBack = (data) => {
        const storeageData = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE,'compare_product');
        let compareProducts;

        if(storeageData){
            compareProducts = storeageData;
            compareProducts.push(data.product);
            Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE,'compare_product', compareProducts);
            showToastMessage(Identify.__('Product has added to your compare list'.toUpperCase()),)
            hideFogLoading();
            this.showModalCompare();
        } else {
            compareProducts = [];
            compareProducts.push(data.product);
            Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE,'compare_product', compareProducts);
            showToastMessage(Identify.__('Product has added to your compare list'.toUpperCase()),)
            hideFogLoading();
            this.showModalCompare();
        }
    }

    onCloseErrorPopup = () => {
        this.setState({isErrorPreorder: false});
    }

    onCloseReserve = () => {
        this.reserveStoreId = null;
        this.setState({openModal: false, reserveSubmited: false, reserveSuccess: false, reserveError: '', reserveMessage: ''});
    }

    onCloseReserveModalMessage = () => {
        this.setState({reserveModalMessage: false});
    }

    reserveAction = () => {
        // check user signedin
        if (!this.props.isSignedIn) {
            this.props.history.push({pathname: '/login.html', pushTo: this.props.history.location.pathname});
            return;
        }
        // get product option selected
        this.missingOption = false
        this.prepareParams();
        if (this.missingOption) {
            this.setState({reserveModalMessage: true});
            // showToastMessage(Identify.__('Please select the options required (*)'));
            return
        }
        // try to fetch storelocations
        if (this.stores.length <= 0) {
            this.getStoreLocations((stores) => {
                this.setState({openModal: true, reserveSubmitting: false});
            });
        } else {
            this.setState({openModal: true, reserveSubmitting: false});
        }
    };

    onReserveChooseStore = (storeId) => {
        this.reserveStoreId = storeId;
        this.setState({reserveSuccess: false, reserveError: ''});
    }

    handleReserveSubmit = (data) => {
        let regData = {};
        regData = {...regData, ...data}
        // find store and get store name
        if (this.stores.length) {
            if (this.reserveStoreId) {
                regData.storelocator_id = this.reserveStoreId;
                let store = this.stores.find((store) => {
                    return parseInt(store.simistorelocator_id) === parseInt(this.reserveStoreId);
                });
                if (store) {
                    regData.store_name = store.store_name;
                }
            } else {
                this.setState({reserveError: Identify.__('Please choose a store'), reserveSuccess: false});
                return
            }
        }
        // get product option selected
        this.missingOption = false
        const params = this.prepareParams();
        if (this.missingOption) {
            showToastMessage(Identify.__('Please select the options required (*)'));
            return
        }
        regData.request_info = params;
        if (!this.props.isSignedIn) {
            this.props.history.push({pathname: '/login.html', pushTo: this.props.history.location.pathname});
        }
        regData.customer_id = this.props.customerId;
        regData.customer_name = this.props.customerLastname ? this.props.customerFirstname : `${this.props.customerFirstname} ${this.props.customerLastname}`;
        // regData.category_name = '';
        sendRequest('/rest/V1/simiconnector/reserve', (data) => {
            if (data && data === true) {
                this.setState({reserveSubmited: true, reserveSuccess: true, reserveError: '', reserveSubmitting: false});
            } else {
                this.setState({openModal: true, reserveSubmited: true, reserveSuccess: false, reserveMessage: data.message, reserveError: '', reserveSubmitting: false});
            }
        }, 'POST', null, regData);
        this.setState({reserveSubmitting: true});
    }

    onSizeGuideClick = (optionId, code) => {
        this.setState({isOpenSizeGuide: true});
    }

    onCloseSizeGuide = () => {
        this.setState({isOpenSizeGuide: false});
    }

    showError(data) {
        if (data.errors.length) {
            const errors = data.errors.map(error => {
                return {
                    type: 'error',
                    message: error.message,
                    auto_dismiss: true
                }
            });
            this.props.toggleMessages(errors)
        }
    }

    showSuccess(data) {
        if (data.message) {
            this.props.toggleMessages([{
                type: 'success',
                message: Array.isArray(data.message)?data.message[0]:data.message,
                auto_dismiss: true
            }])
        }
    }

    handleConfigurableSelectionChange = (optionId, selection) => {
        let value = Array.from(selection).pop();
        if (value instanceof Array) {
            value = value[0];
        }
        this.setState(({ optionSelections }) => ({
            optionSelections: new Map(optionSelections).set(optionId, value)
        }));
    };

    get isMissingConfigurableOptions() {
        const { product } = this.props;
        const { configurable_options } = product;
        if (configurable_options) {
            const numProductOptions = configurable_options.length;
            const numProductSelections = this.state.optionSelections.size;
            return numProductSelections < numProductOptions;
        }
        return false;
    }

    get fallback() {
        return <Loading />;
    }

    get productOptions() {
        const { fallback, handleConfigurableSelectionChange, props } = this;
        const { configurable_options, simiExtraField, type_id, is_dummy_data, variants } = props.product;
        const {attribute_values: {pre_order, try_to_buy, reservable, is_salable}} = simiExtraField;
        // map color options in simiExtraField to configurable_options
        if (simiExtraField && simiExtraField.app_options && simiExtraField.app_options.configurable_options && simiExtraField.app_options.configurable_options.attributes) {
            let optionColors = Object.values(simiExtraField.app_options.configurable_options.attributes);
            let optionColor = optionColors.find(item => item.code === 'color');
            if (optionColor && optionColor.options){
                let _optionColor = configurable_options.find(item => item.attribute_code === 'color');
                if (_optionColor && _optionColor.values) {
                    optionColor.options.map(item => {
                        let option = _optionColor.values.find(_optItem => _optItem.value_index === parseInt(item.id));
                        if (option) option.option_value = item.option_value;
                        return option;
                    })
                }
            }
        }

        // sorting options
        if (configurable_options) {
            let startSortOrder = 3;
            configurable_options.map((option) => {
                if (option.attribute_code === 'size') {
                    option.sort_order = 1;
                    return option;
                }
                if (option.attribute_code === 'color') {
                    option.sort_order = 2;
                    return option;
                }
                option.sort_order = startSortOrder;
                startSortOrder += 1;
                return option;
            });
            configurable_options.sort(function(a, b){
                if (a.sort_order && b.sort_order){
                    return a.sort_order - b.sort_order;
                }
                return 0;
            })
        }

        const isConfigurable = isProductConfigurable(props.product);
        if (is_dummy_data)
            return <Loading />
        return (
            <Suspense fallback={fallback}>
                {
                    isConfigurable &&
                    <ConfigurableOptions
                        variants={variants}
                        options={configurable_options}
                        optionSelections={this.state.optionSelections}
                        onSelectionChange={handleConfigurableSelectionChange}
                        onSizeGuideClick={this.onSizeGuideClick}
                    />
                }
                {
                    type_id === 'bundle' &&
                    <BundleOptions 
                        key={Identify.randomString(5)}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        ref={e => this.bundleOption = e}
                        parent={this}
                    />
                }
                {
                    type_id === 'grouped' &&
                    <GroupedOptions 
                        key={Identify.randomString(5)}
                        app_options={props.product.items?props.product.items:[]}
                        product_id={this.props.product.entity_id}
                        ref={e => this.groupedOption = e}
                        parent={this}
                    />
                }
                {
                    type_id === 'downloadable' &&
                    <DownloadableOptions 
                        key={Identify.randomString(5)}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        ref={e => this.downloadableOption = e}
                        parent={this}
                    />
                }
                {
                    type_id === 'aw_giftcard' &&
                    <GiftcardOptions 
                        key={Identify.randomString(5)}
                        extraField={simiExtraField}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        // ref={e => this.giftcardOption = e}
                        myRef={e => this.giftcardOption = e}
                        parent={this}
                    />
                }
                {
                    ( simiExtraField && simiExtraField.app_options && simiExtraField.app_options.custom_options) &&
                    <CustomOptions 
                        key={Identify.randomString(5)}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        ref={e => this.customOption = e}
                        parent={this}
                    />
                }
                {try_to_buy === '1' && pre_order !== '1' && parseInt(is_salable) === 1 &&
                    <TrytobuyOptions className={"try-to-buy"} cbRef={el => this.trytobuyOptionsRef = el} />
                }
            </Suspense>
        );
    }

    breadcrumb = (product) => {
        let breadcrumbs = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, Constants.BREADCRUMBS);
        if (!breadcrumbs || breadcrumbs.length == 0) {
            breadcrumbs = [{name: Identify.__("Home"), link: '/'}];
            if (product && product.categories) {
                let cate = product.categories.pop();
                if (cate) {
                    breadcrumbs.push({name: cate.name, link: cate.url_path+productUrlSuffix()});
                }
            }
        }
        return (
            <BreadCrumb breadcrumb={breadcrumbs} history={this.props.history}>
                {product.name}
            </BreadCrumb>
        );
    }

    tabItem = (props) => {
        if (!props.show) return null;
        return (
            <div className={`item item-${props.id} ${props.className}`}>
                <div className="tab-container">
                    {props.children}
                </div>
            </div>
        );
    }

    handleAddYourReview = () => {
        if(this.tabs){
            this.tabs.openTab(2);
        }
    }

    getVendor = () => {
        const { product: {simiExtraField: {attribute_values: attribute_values} }, history} = this.props;
        if (attribute_values && attribute_values.vendor_id) {
            const configs = Identify.getStoreConfig();
            if (configs && configs.simiStoreConfig && configs.simiStoreConfig.config && configs.simiStoreConfig.config.vendor_list) {
                const vendorList = configs.simiStoreConfig.config.vendor_list;
                const vendor = vendorList.find((vendor) => {
                    return parseInt(vendor.entity_id) === parseInt(attribute_values.vendor_id);
                });
                return vendor;
            }
        }
        return null
    }

    getVendorStoreName = () => {
        const vendor = this.getVendor();
        let vendorName = '';
        if(vendor){
            if (vendor && vendor.firstname) vendorName = `${vendor.firstname}`;
            if (vendor && vendor.lastname) vendorName = `${vendorName} ${vendor.lastname}`;
            vendorName = vendor.profile && vendor.profile.store_name || vendorName;
        }
        return vendorName;
    }

    vendorName = () => {
        const vendor = this.getVendor();
        let vendorName = this.getVendorStoreName();
        if(vendor && vendorName){
            if (vendor.vendor_id) {
                return <Link to={`/designers/${vendor.vendor_id}.html`}>{vendorName}</Link>
            }
            return <Link to={`/designers/${vendor.entity_id}.html`}>{vendorName}</Link>
        }
        return null
    }
    
    render() {
        hideFogLoading()
        const isPhone = this.getIsPhone();
        const { addToCart, addToCartWithParams, addToCompare, reserveAction, productOptions, props, state, addToWishlist } = this;
        const { optionCodes, optionSelections, } = state;
        const storeConfig = Identify.getStoreConfig()
        const { config } = storeConfig && storeConfig.simiStoreConfig || null;
        const { delivery_returns, preorder_deposit } = config;
        const product = prepareProduct(props.product);
        const { is_dummy_data, name, simiExtraField } = product;
        const short_desc = (product.short_description && product.short_description.html)?product.short_description.html:'';
        // const hasReview = simiExtraField && simiExtraField.app_reviews && simiExtraField.app_reviews.number;
        const {attribute_values: {pre_order, try_to_buy, reservable, is_salable}} = simiExtraField;
        let addToCartBtn = (
            <Colorbtn
                className="add-to-cart-btn btn btn__black"
                onClick={addToCart}
                text={Identify.__('Add to Cart')} />
        )
        if (simiExtraField && simiExtraField.attribute_values) {
            if (!parseInt(is_salable) && parseInt(pre_order) && !isProductConfigurable(props.product)) {
                addToCartBtn = (
                    <Colorbtn
                        style={{ backgroundColor: '#101820', color: '#FFF' }}
                        className="pre-order-btn btn btn__black"
                        onClick={this.preorderAction}
                        text={Identify.__('Pre-order')} />
                )
            } else if (!parseInt(is_salable)) {
                addToCartBtn = (
                    <Colorbtn
                        style={{ backgroundColor: '#101820', color: '#FFF', opacity: 0.5 }}
                        className="add-to-cart-btn btn out-of-stock"
                        text={Identify.__('Out of stock')} />
                )
            }
        }
        analyticsViewDetailsGTM(product)
        return (
            <div className={`container product-detail-root ${getOS()} ${isPhone ? 'mobile':''}`}>
                {this.breadcrumb(product)}
                {TitleHelper.renderMetaHeader({
                    title: product.meta_title?product.meta_title:product.name?product.name:'',
                    desc: product.meta_description?product.meta_description:product.description?product.description:''
                })}
                <div className="detail-top">
                    <div className="top-col col-left">
                        <div className="image-carousel">
                            <ProductImage 
                                optionCodes={optionCodes}
                                optionSelections={optionSelections}
                                product={product}
                            />
                            {/* {parseInt(is_salable) === 0 && parseInt(pre_order) !== 1 && 
                                <div className="out-of-stock"><span>{Identify.__('Out of stock')}</span></div>
                            } */}
                            {isPhone && (parseInt(pre_order) === 1 || parseInt(is_salable) === 1) && 
                                <div className="wishlist-actions action-icon">
                                    <button onClick={addToWishlist} title={Identify.__('Add to Favourites')}><Favorite /></button>
                                </div>
                            }
                            {isPhone &&
                                <div className="compare-actions action-icon">
                                    <button onClick={()=>{
                                        addToCompare();
                                    }} title={Identify.__('Compare')}><CompareIcon /></button>
                                    <CompareProduct openModal={this.state.openCompareModal} closeModal={this.closeCompareModal}/>
                                </div>
                            }
                        </div>
                    </div>
                    <div className="top-col col-right">
                        <div className="title">
                            <h1 className="product-name">
                                <span>{ReactHTMLParse(name)}</span>
                            </h1>
                        </div>
                        <div className="main-actions">
                            <div className="vendor-name">{this.vendorName()}</div>
                            <div className="top-review">
                                <TopReview app_reviews={product.simiExtraField.app_reviews}/>
                                <div role="presentation" className="review-btn" onClick={this.handleAddYourReview}>
                                    {Identify.__('Add your review')}
                                </div>
                            </div>
                            <div className="product-price">
                                <ProductPrice ref={(price) => this.Price = price} data={product} configurableOptionSelection={optionSelections}/>
                            </div>
                            <div className="product-short-desc">
                                {ReactHTMLParse(short_desc)}
                                {!parseInt(is_salable) && pre_order === '1' && preorder_deposit ? 
                                    <p className="deposit">{Identify.__(`Deposit ${preorder_deposit}%`)}</p> 
                                    : null }
                            </div>
                            <div className="options">{productOptions}</div>
                            {
                                is_dummy_data ?
                                <Loading /> :
                                <div className="cart-actions">
                                    {/* {
                                        type_id !== 'grouped' &&
                                        <Quantity
                                            initialValue={this.quantity}
                                            onValueChange={this.setQuantity}
                                        />
                                    } */}
                                    <div className="cart-ctn">
                                        {addToCartBtn}
                                    </div>
                                    {parseInt(is_salable) === 1 ? 
                                        <div className="cart-ctn">
                                            <Whitebtn className="buy-1-click-btn btn btn__white" 
                                                onClick={() => this.buy1ClickAction({buy1click: '1'})} 
                                                text={Identify.__('Buy with 1-click')}
                                            />
                                        </div>
                                        :
                                        parseInt(pre_order) === 1 && 
                                        <div className="cart-ctn">
                                            <Whitebtn className="buy-1-click-btn btn btn__white" 
                                                onClick={() => this.buy1ClickAction({buy1click: '1', pre_order: parseInt(pre_order)})} 
                                                text={Identify.__('Buy with 1-click')}
                                            />
                                        </div>
                                    }
                                    
                                    {parseInt(is_salable) === 1 && reservable === '1' && 
                                        <div className="cart-ctn">
                                            <Whitebtn className="reserve-btn btn btn__white" onClick={reserveAction} text={Identify.__('Reserve')}/>
                                        </div>
                                    }
                                    {!isPhone && parseInt(is_salable) === 1 && 
                                        <div className="wishlist-actions action-icon">
                                            <button onClick={addToWishlist} title={Identify.__('Add to Favourites')}><Favorite /></button>
                                        </div>
                                    }
                                    {!isPhone &&
                                        <div className="compare-actions action-icon">
                                            <button onClick={()=>{
                                                addToCompare();
                                            }} title={Identify.__('Compare')}><CompareIcon /></button>
                                            <CompareProduct openModal={this.state.openCompareModal} closeModal={this.closeCompareModal}/>
                                        </div>
                                    }
                                </div>
                            }
                            <div className="social-share"><SocialShare id={product.id} className="social-share-item" /></div>
                        </div>
                    </div>
                </div>
                <div className="main-info">
                    <Tabs activeItem={0} 
                        scrollTo={() => smoothScrollToView($('#product-detail-new-review'))} 
                        objRef={(tabs) => this.tabs = tabs}
                    >
                        <div label={Identify.__('Delivery & Returns')}>
                            <div className="delivery-returns">
                                {delivery_returns}
                            </div>
                        </div>
                        <div label={Identify.__('More Information')}>
                            <div className="description"><Description product={product}/></div>
                        </div>
                        <div label={Identify.__('Reviews')}>
                            <div className="review-list"><ReviewList product_id={product.id}/></div>
                            <div className="new-review" id="product-detail-new-review">
                                <NewReview product={product} toggleMessages={this.props.toggleMessages}/>
                            </div>
                        </div>
                        {/* <div label={Identify.__('Additional Information')}>
                            {(simiExtraField && simiExtraField.additional && simiExtraField.additional.length) ?
                                <div className="item">
                                    <div className="techspec"><Techspec product={product}/></div> 
                                </div>
                            : ''}
                        </div> */}
                    </Tabs>
                </div>
                {
                    (this.props.hideRelatedProduct !== true && product && product.product_links && product.product_links.length) &&
                    <LinkedProduct product={product} link_type="related" history={this.props.history}/>
                }
                {/* <LinkedProduct product={product} link_type="crosssell" history={this.props.history}/> */}
                <Modal open={this.state.reserveModalMessage} onClose={this.onCloseReserveModalMessage}
                    modalId={'reserve-modal-message'} 
                    classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
                    closeIconId={'reserve-modal-close'}
                    closeIconSize={16}
                    closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
                >
                    {Identify.__('Please select the options required (*)')}
                </Modal>
                <Modal open={this.state.openModal} onClose={this.onCloseReserve}
                    overlayId={'reserve-modal-overlay'}
                    modalId={'reserve-modal'}
                    classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
                    closeIconId={'reserve-modal-close'}
                    closeIconSize={16}
                    closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
                >
                    <div className={`reserve-modal-content ${isPhone ? 'mobile':''}`}>
                        <div className="modal-title">
                            <h2>{Identify.__('RESERVE')}</h2>
                        </div>
                        {!this.state.reserveSuccess && 
                            <div className="modal-header">
                                <p>{Identify.__('Please visit chosen store in next working day to try your item. Contact us if you have any question.')}</p>
                            </div>
                        }
                        {
                            this.state.reserveSubmited ? 
                                this.state.reserveSuccess ? 
                                <div className="modal-body">
                                    <div className="reserve-success"><h3>{Identify.__('Thank you for your reservation!')}</h3></div>
                                </div> :
                                <div className="modal-body">
                                    <div className="reserve-error error">{Identify.__(this.state.reserveMessage)}</div>
                                </div>
                            :
                            <div className="modal-body">
                                <div className="locations-select">
                                    <label>{Identify.__('Location')}</label>
                                    <div className="option-select" ref={this.reserveSelectTrigger}>
                                        {this.storesOptions && this.storesOptions.length &&
                                            <Select className="store-input"
                                                triggerRef={this.reserveSelectTrigger}
                                                showSelected={true} placeholder={Identify.__('Choose a store')} 
                                                items={this.storesOptions} onChange={this.onReserveChooseStore} 
                                            />
                                        }
                                        {/* <select onChange={this.onReserveChooseStore}>
                                            <option value="" hidden>{Identify.__('Choose a store')}</option>
                                            {
                                                this.stores && 
                                                this.stores.map((store, index) => {
                                                    return <option value={store.simistorelocator_id} key={index}>{store.store_name}</option>
                                                })
                                            }
                                        </select> */}
                                    </div>
                                    {this.state.reserveError && <div className="error">{this.state.reserveError}</div>}
                                </div>
                                {
                                    this.state.reserveSubmitting ? 
                                    <Loading /> :
                                    <div className="submit-btn" onClick={() => this.handleReserveSubmit({
                                        product_id: product.id,
                                        product_name: product.name,
                                    })}>
                                        <span>{Identify.__('Submit')}</span>
                                    </div>
                                }
                            </div>
                        }
                    </div>
                </Modal>
                <SizeGuide isPopup={this.state.isOpenSizeGuide} onClose={this.onCloseSizeGuide} 
                    product={product} 
                    isSignedIn={this.props.isSignedIn} 
                    customerId={this.props.customerId} 
                    customerFirstname={this.props.customerFirstname} 
                    customerLastname={this.props.customerLastname}
                    history={this.props.history}
                />
                {
                    this.state.isErrorPreorder && 
                    <Modal open={this.state.isErrorPreorder} onClose={this.onCloseErrorPopup}
                        classNames={{overlay: Identify.isRtl()?"rtl-root":""}}
                        overlayId={'error-modal-overlay'}
                        modalId={'error-modal'}
                        closeIconSvgPath={''}
                        >
                        <div className="error-wrap">
                            <div className="message">
                                {Identify.__('Pre-order products can not be added to the same cart with regular products. Please checkout with existing products in cart first.')}
                            </div>
                            <div className="go-checkout-text">
                                <a href="/checkout.html" alt="Go to Checkout">
                                    {Identify.__('GO TO CHECKOUT')}
                                </a>
                            </div>
                        </div>
                    </Modal>
                }
            </div>
        );
    }
}

ProductFullDetail.propTypes = {
    product: shape({
        __typename: string,
        id: number,
        sku: string.isRequired,
        price: shape({
            regularPrice: shape({
                amount: shape({
                    currency: string.isRequired,
                    value: number.isRequired
                })
            }).isRequired
        }).isRequired,
        media_gallery_entries: arrayOf(
            shape({
                label: string,
                position: number,
                disabled: bool,
                file: string.isRequired
            })
        ),
        description: object
    }).isRequired,
    isSignedIn: bool,
    customerFirstname: string,
    customerLastname: string,
    customerId: number
};

const mapStateToProps = ({ user }) => {
    const { currentUser, isSignedIn } = user;
    const { firstname, lastname, id } = currentUser;

    return {
        isSignedIn,
        customerFirstname: firstname,
        customerLastname: lastname,
        customerId: id
    };
}

export default compose(connect(mapStateToProps), withRouter)(ProductFullDetail);