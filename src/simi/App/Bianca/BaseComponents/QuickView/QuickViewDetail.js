import React, { Component, Suspense } from 'react';
import { arrayOf, bool, number, shape, string, object } from 'prop-types';
import {smoothScrollToView} from 'src/simi/Helper/Behavior'
import Loading from 'src/simi/BaseComponents/Loading'
import { Colorbtn, Whitebtn } from 'src/simi/BaseComponents/Button'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import ProductImage from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/ProductImage';
import Quantity from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/ProductQuantity';
import isProductConfigurable from 'src/util/isProductConfigurable';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper'
import {prepareProduct} from 'src/simi/Helper/Product'
import ProductPrice from 'src/simi/App/Bianca/RootComponents/Product/Component/Productprice';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { addToWishlist as simiAddToWishlist } from 'src/simi/Model/Wishlist';
import {configColor} from 'src/simi/Config'
import {showToastMessage} from 'src/simi/Helper/Message';
import ReactHTMLParse from 'react-html-parser';
import BreadCrumb from "src/simi/BaseComponents/BreadCrumb"
import { TopReview, ReviewList, NewReview } from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Review/index'
import SocialShare from 'src/simi/BaseComponents/SocialShare';
import Description from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Description';
import Techspec from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Techspec';
import LinkedProduct from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/LinkedProduct';
import ProductFullDetail from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/ProductFullDetail';

const ConfigurableOptions = React.lazy(() => import('src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Options/ConfigurableOptions'));
const CustomOptions = React.lazy(() => import('src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Options/CustomOptions'));
const BundleOptions = React.lazy(() => import('src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Options/Bundle'));
const GroupedOptions = React.lazy(() => import('src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Options/GroupedOptions'));
const DownloadableOptions = React.lazy(() => import('src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Options/DownloadableOptions'));
const GiftcardOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/GiftcardOptions'));
const TrytobuyOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/TrytobuyOptions'));

class QuickViewDetail extends ProductFullDetail{
    constructor(props){
        super(props)
        this.state= {
            optionCodes: new Map(),
            optionSelections: new Map(),
            deliveryDisplay: "block",
            informationDisplay: "none",
            reivewsDisplay: "none"
        };
    }

    quantity = 1
      
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
        if (optionSelections && optionSelections.size) { //configurable option
            if (this.isMissingConfigurableOptions) {
                this.missingOption = true
            }
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
    addToReserve = () => {

    }
    addToCompare = () =>{
        
    }
    addToCart = () => {
        const { props } = this;
        const {  product } = props;
        if (product && product.id) {
            this.missingOption = false
            const params = this.prepareParams()
            if (this.missingOption) {
                showToastMessage(Identify.__('Please select the options required (*)'));
                return
            }
            showFogLoading()
            simiAddToCart(this.addToCartCallBack, params)
        }
    };

    addToCartCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
            this.showError(data)
        } else {
            this.showSuccess(data)
            this.props.updateItemInCart()
        }
    }

    addToWishlist = () => {
        const {product, isSignedIn, history} = this.props
        if (!isSignedIn) {
            history.push('/login.html')
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

    shouldComponentUpdate(nextProps, nextState){
        return ((nextState.deliveryDisplay!= this.state.deliveryDisplay) ||(nextState.informationDisplay!=this.state.informationDisplay)||(nextState.reivewsDisplay!= this.state.reivewsDisplay))
    }

    handleClickTitle = (value) =>{
        console.log(value);
        switch(value){
            case "delivery": {
                $('.three-titles .delivery').css('color', '#101820')
                $('.three-titles .information').css('color', '#727272')
                $('.three-titles .reviews').css('color', '#727272')
                this.setState({
                    deliveryDisplay: "block",
                    informationDisplay: "none",
                    reivewsDisplay: "none"
                })
                break
            }
            case "information": {
                $('.three-titles .delivery').css('color', '#727272')
                $('.three-titles .information').css('color', '#101820')
                $('.three-titles .reviews').css('color', '#727272')
                this.setState({
                    deliveryDisplay: "none",
                    informationDisplay: "block",
                    reivewsDisplay: "none"
                })
                break
            }
            case "reviews": {
                $('.three-titles .delivery').css('color', '#727272')
                $('.three-titles .information').css('color', '#727272')
                $('.three-titles .reviews').css('color', '#101820')
                this.setState({
                    deliveryDisplay: "none",
                    informationDisplay: "none",
                    reivewsDisplay: "block"
                })
                break
            }
        }
    }

    handleConfigurableSelectionChange = (optionId, selection) => {
        this.setState(({ optionSelections }) => ({
            optionSelections: new Map(optionSelections).set(
                optionId,
                Array.from(selection).pop()
            )
        }));
    };

    get isMissingConfigurableOptions() {
        const { product } = this.props;
        const { configurable_options } = product;
        const numProductOptions = configurable_options.length;
        const numProductSelections = this.state.optionSelections.size;
        return numProductSelections < numProductOptions;
    }

    get fallback() {
        return <Loading />;
    }

    get productOptions() {
        const { fallback, handleConfigurableSelectionChange, props } = this;
        const { configurable_options, simiExtraField, type_id, is_dummy_data } = props.product;
        const isConfigurable = isProductConfigurable(props.product);
        if (is_dummy_data)
            return <Loading />
        return (
            <Suspense fallback={fallback}>
                {
                    isConfigurable &&
                    <ConfigurableOptions
                        options={configurable_options}
                        onSelectionChange={handleConfigurableSelectionChange}
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
                {
                    <TrytobuyOptions className={"try-to-buy"} 
                    cbRef={el => this.trytobuyOptionsRef = el}
                     />
                }
            </Suspense>
        );
    }

    breadcrumb = (product) => {
        return <BreadCrumb breadcrumb={[{name:'Home',link:'/'},{name:product.name}]} history={this.props.history}/>
    }
    render() {
        hideFogLoading()
        const { addToReserve, addToCart, productOptions, props, state, addToWishlist, addToCompare } = this;
        const { optionCodes, optionSelections, } = state
        const product = prepareProduct(props.product)
        const { type_id, name, simiExtraField } = product;
        const short_desc = (product.short_description && product.short_description.html)?product.short_description.html:''
        const hasReview = simiExtraField && simiExtraField.app_reviews && simiExtraField.app_reviews.number
        return (
            <div className="container product-detail-root">
                {this.breadcrumb(product)}
                {TitleHelper.renderMetaHeader({
                    title: product.meta_title?product.meta_title:product.name?product.name:'',
                    desc: product.meta_description?product.meta_description:product.description?product.description:''
                })}
                <div className="image-carousel">
                    <ProductImage 
                        optionCodes={optionCodes} 
                        optionSelections={optionSelections} 
                        product={product}
                    />
                </div>
                <div className="main-actions">
                    <div className="title">
                        <h1 className="product-name">
                            <span>{ReactHTMLParse(name)}</span>
                        </h1>
                    </div>
                    <div className="vendor-name">
                        Desinger name
                    </div>
                    {hasReview ? <div className="top-review"><TopReview app_reviews={product.simiExtraField.app_reviews}/></div> : ''}
                    <div className="product-price">
                        <ProductPrice ref={(price) => this.Price = price} data={product} configurableOptionSelection={optionSelections}/>
                    </div>
                    <div className="product-short-desc">{ReactHTMLParse(ReactHTMLParse(short_desc))}</div>
                    <div className="options">{productOptions}</div>
                    <div className="cart-wishlist-compare">
                        <div className="cart-actions">
                            <div 
                                className="add-to-cart-ctn" >
                                <Colorbtn 
                                    className="add-to-cart-btn"
                                    onClick={addToCart}
                                    text={Identify.__('Add to Cart')}/>
                            </div>
                        </div>
                        <div className="reserve-actions">
                            <div 
                                className="reserve-ctn" >
                                <Colorbtn 
                                    className="add-to-reserve-btn"
                                    onClick={addToReserve}
                                    text={Identify.__('Reserve')}/>
                            </div>
                        </div>
                        <div className="wishlistAction">
                            <div className="wishlist">
                                <span
                                    className="add-to-wishlist-btn icon-chef"
                                    onClick={addToWishlist}
                                >
                                </span>
                            </div>
                        </div>
                        <div className="compareAction">
                            <div className="compare">
                                <span
                                    className="add-to-compare-btn icon-bench-press"
                                    onClick={addToCompare}
                                >
                                </span>
                            </div>
                        </div>
                    </div>
                    <div className="social-share"><SocialShare id={product.id} className="social-share-item" /></div>
                </div>
                <div className="description-and-review">
                    <div className="three-titles">
                        <span className="title delivery"
                            onClick={()=>this.handleClickTitle("delivery")}
                        >
                            {Identify.__('Delivery & Returns')}
                        </span>
                        <span className="title information"
                            onClick={()=>this.handleClickTitle("information")}
                        >
                            {Identify.__('More Information')}
                        </span>
                        <span className="title reviews"
                            onClick={()=>this.handleClickTitle("reviews")}
                        >
                            {Identify.__('Reviews')}
                        </span>
                    </div>
                    <div className="three-contents">
                        {product.description && 
                            <div className="content description"
                                style={{display: this.state.deliveryDisplay}} >
                                <Description product={product}/>
                            </div>
                        }
                        {(simiExtraField && simiExtraField.additional && simiExtraField.additional.length) ?
                            <div className="content techspec"
                                style={{display: this.state.informationDisplay}} >
                                <Techspec product={product}/>
                            </div> : ''
                        }
                        <div className="content review-list"
                            style={{display: this.state.reivewsDisplay}} >
                            <ReviewList product_id={product.id}/>
                        </div>
                    </div>
                </div>

                <LinkedProduct product={product} link_type="related" history={this.props.history}/>
                <LinkedProduct product={product} link_type="crosssell" history={this.props.history}/>
            </div>
        );
    }
}
QuickViewDetail.propTypes = {
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
    }).isRequired
};
export default QuickViewDetail