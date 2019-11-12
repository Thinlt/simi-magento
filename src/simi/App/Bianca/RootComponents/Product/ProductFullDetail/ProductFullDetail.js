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
import SocialShare from 'src/simi/BaseComponents/SocialShare';
import Description from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Description';
// import Techspec from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/Techspec';
import LinkedProduct from 'src/simi/App/Bianca/RootComponents/Product/ProductFullDetail/LinkedProduct';
import Favorite from 'src/simi/App/Bianca/BaseComponents/Icon/Favorite';
import CompareIcon from 'src/simi/App/Bianca/BaseComponents/Icon/SyncCompare';
import CloseIcon from 'src/simi/App/Bianca/BaseComponents/Icon/Close';
import Modal from 'react-responsive-modal';
import ListItemNested from 'src/simi/App/Bianca/BaseComponents/MuiListItem/Nested';
import { List } from '@magento/peregrine';


const ConfigurableOptions = React.lazy(() => import('./Options/ConfigurableOptions'));
const CustomOptions = React.lazy(() => import('./Options/CustomOptions'));
const BundleOptions = React.lazy(() => import('./Options/Bundle'));
const GroupedOptions = React.lazy(() => import('./Options/GroupedOptions'));
const DownloadableOptions = React.lazy(() => import('./Options/DownloadableOptions'));
const GiftcardOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/GiftcardOptions'));
const TrytobuyOptions = React.lazy(() => import('src/simi/App/Bianca/Components/Product/ProductFullDetail/Options/TrytobuyOptions'));

import {addRecentViewedProducts} from '../../../Helper/Biancaproduct'

require('./productFullDetail.scss');

class ProductFullDetail extends Component {  
    constructor(props) {
        super(props)
        if (props.product)
            addRecentViewedProducts(props.product)
    }

    state = {
        optionCodes: new Map(),
        optionSelections: new Map(),
        openModal: false
    };
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

    addToCartWithParams = (data = {}) => {
        const { product } = this.props;
        if (product && product.id) {
            this.missingOption = false
            const params = {...this.prepareParams(), ...data};
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

    addToCompare = () => {
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

    onCloseReserve = () => {
        this.setState({openModal: false});
    }

    reserveAction = () => {
        this.setState({openModal: true});
    };

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
        const {attribute_values: {pre_order, try_to_buy, reservable}} = simiExtraField;

        // map color options in simiExtraField to configurable_options
        if (simiExtraField && simiExtraField.app_options && simiExtraField.app_options.configurable_options && simiExtraField.app_options.configurable_options.attributes) {
            let optionColors = Object.values(simiExtraField.app_options.configurable_options.attributes);
            let optionColor = optionColors.find(item => item.code === 'color');
            if (optionColor && optionColor.options){
                let _optionColor = configurable_options.find(item => item.attribute_code === 'color');
                if (_optionColor && _optionColor.values) {
                    optionColor.options.map(item => {
                        let option = _optionColor.values.find(_optItem => _optItem.value_index === parseInt(item.id));
                        return option.option_value = item.option_value;
                    })
                }
            }
        }

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
                    try_to_buy === '1' && <TrytobuyOptions className={"try-to-buy"} cbRef={el => this.trytobuyOptionsRef = el} />
                }
            </Suspense>
        );
    }

    breadcrumb = (product) => {
        return <BreadCrumb breadcrumb={[{name:'Home',link:'/'},{name:product.name}]} history={this.props.history}/>
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

    vendorName = () => {
        const { product: {simiExtraField: {attribute_values: attribute_values} }} = this.props;
        if (attribute_values && attribute_values.vendor_id) {
            const configs = Identify.getStoreConfig();
            if (configs && configs.simiStoreConfig && configs.simiStoreConfig.config && configs.simiStoreConfig.config.vendor_list) {
                const vendorList = configs.simiStoreConfig.config.vendor_list;
                const vendor = vendorList.find((vendor) => {
                    return parseInt(vendor.entity_id) === parseInt(attribute_values.vendor_id);
                });
                let vendorName = '';
                if (vendor && vendor.firstname) vendorName = `${vendor.firstname}`;
                if (vendor && vendor.lastname) vendorName = `${vendorName} ${vendor.lastname}`;
                if (vendorName) return vendorName;
            }
        }
        return null
    }
    
    render() {
        hideFogLoading()
        const { addToCart, addToCartWithParams, addToCompare, reserveAction, productOptions, props, state, addToWishlist } = this;
        const { optionCodes, optionSelections, } = state
        const product = prepareProduct(props.product)
        const { type_id, name, simiExtraField } = product;
        const short_desc = (product.short_description && product.short_description.html)?product.short_description.html:''
        const hasReview = simiExtraField && simiExtraField.app_reviews && simiExtraField.app_reviews.number
        const {attribute_values: {pre_order, try_to_buy, reservable}} = simiExtraField;
        return (
            <div className="container product-detail-root">
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
                                {hasReview ? <TopReview app_reviews={product.simiExtraField.app_reviews}/> : null}
                                <div role="presentation" className="review-btn" onClick={()=>smoothScrollToView($('#product-detail-new-review'))}>
                                    {hasReview ? Identify.__('Add your review') : Identify.__('Be the first to review this product')}
                                </div>
                            </div>
                            <div className="product-price">
                                <ProductPrice ref={(price) => this.Price = price} data={product} configurableOptionSelection={optionSelections}/>
                            </div>
                            <div className="product-short-desc">{ReactHTMLParse(short_desc)}</div>
                            <div className="options">{productOptions}</div>
                            <div className="cart-actions">
                                {/* {
                                    type_id !== 'grouped' &&
                                    <Quantity
                                        initialValue={this.quantity}
                                        onValueChange={this.setQuantity}
                                    />
                                } */}
                                {
                                    pre_order === '1' && try_to_buy !== '1' && reservable !== '1' ? 
                                    <div className="cart-ctn">
                                        <Colorbtn className="pre-order-btn btn btn__black" onClick={() => addToCartWithParams({pre_order: '1'})} text={Identify.__('Pre-order')}/>
                                    </div>
                                    :
                                    <div className="cart-ctn">
                                        <Colorbtn className="add-to-cart-btn btn btn__black" onClick={addToCart} text={Identify.__('Add to Cart')}/>
                                    </div>
                                }
                                <div className="cart-ctn">
                                    <Whitebtn className="buy-1-click-btn btn btn__white" onClick={() => addToCartWithParams({buy1click: '1'})} text={Identify.__('Buy with 1-click')}/>
                                </div>
                                {
                                    reservable === '1' && 
                                    <div className="cart-ctn">
                                        <Whitebtn className="reserve-btn btn btn__white" onClick={reserveAction} text={Identify.__('Reserve')}/>
                                    </div>
                                }
                                <div className="wishlist-actions action-icon">
                                    <button onClick={addToWishlist} title={Identify.__('Add to Favourites')}><Favorite /></button>
                                </div>
                                <div className="compare-actions action-icon">
                                    <button onClick={addToCompare} title={Identify.__('Compare')}><CompareIcon /></button>
                                </div>
                            </div>
                            
                            <div className="social-share"><SocialShare id={product.id} className="social-share-item" /></div>
                        </div>
                    </div>
                </div>
                <div className="main-info">
                    <Tabs>
                        <div label={Identify.__('Delivery & Returns')}>
                            <div className="delivery-returns">
                            Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuribut also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets It was popularised in the 1960s with the release of Letraset sheets. 
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
                <LinkedProduct product={product} link_type="related" history={this.props.history}/>
                {/* <LinkedProduct product={product} link_type="crosssell" history={this.props.history}/> */}
                <Modal open={this.state.openModal} onClose={this.onCloseReserve}
                    overlayId={'reserve-modal-overlay'}
                    modalId={'reserve-modal'}
                    closeIconId={'reserve-modal-close'}
                    closeIconSize={16}
                    closeIconSvgPath={<CloseIcon style={{fill: '#101820'}}/>}
                >
                    <div className="reserve-modal-content">
                        <div className="modal-title">
                            <h2>RESERVE</h2>
                        </div>
                        <div className="modal-header">
                            <p>Please visit chosen store in next working day to try your item. Contact us if you have any question.</p>
                        </div>
                        <div className="modal-body">
                            <div className="locations-select">
                                <label>{Identify.__('Location')}</label>
                                <div className="option-select">
                                    <select>
                                        <option value="" hidden>{Identify.__('Choose a store')}</option>
                                        <option value="1">{Identify.__('Store 1')}</option>
                                        <option value="2">{Identify.__('Store 2')}</option>
                                    </select>
                                </div>
                            </div>
                            <div className="submit-btn">
                                <span>{Identify.__('Submit')}</span>
                            </div>
                        </div>
                    </div>
                </Modal>
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
    }).isRequired
};

export default (withRouter)(ProductFullDetail);