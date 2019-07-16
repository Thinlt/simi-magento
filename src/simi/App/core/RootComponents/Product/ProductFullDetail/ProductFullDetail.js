import React, { Component, Suspense } from 'react';
import { arrayOf, bool, number, shape, string } from 'prop-types';
import classify from 'src/classify';
import Loading from 'src/simi/BaseComponents/Loading'
import { Colorbtn, Whitebtn } from 'src/simi/BaseComponents/Button'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import Carousel from './ProductImageCarousel';
import Quantity from './ProductQuantity';
import RichText from 'src/simi/BaseComponents/RichText';
import defaultClasses from './productFullDetail.css';
import appendOptionsToPayload from 'src/util/appendOptionsToPayload';
import isProductConfigurable from 'src/util/isProductConfigurable';
import Identify from 'src/simi/Helper/Identify';
import {prepareProduct} from 'src/simi/Helper/Product'
import ProductPrice from '../Component/Productprice';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { addToWishlist as simiAddToWishlist } from 'src/simi/Model/Wishlist';
import {configColor} from 'src/simi/Config'
import {showToastMessage} from 'src/simi/Helper/Message';
import Techspec from './Techspec';
import ReactHTMLParse from 'react-html-parser';

const ConfigurableOptions = React.lazy(() => import('./Options/ConfigurableOptions'));
const CustomOptions = React.lazy(() => import('./Options/CustomOptions'));
const BundleOptions = React.lazy(() => import('./Options/Bundle'));
const GroupedOptions = React.lazy(() => import('./Options/GroupedOptions'));
const DownloadableOptions = React.lazy(() => import('./Options/DownloadableOptions'));

class ProductFullDetail extends Component {  
    state = {
        optionCodes: new Map(),
        optionSelections: new Map(),
    };
    quantity = 1
      
    static getDerivedStateFromProps(props, state) {
        const { configurable_options } = props.product;
        const optionCodes = new Map(state.optionCodes);
        // if this is a simple product, do nothing
        if (!isProductConfigurable(props.product)) {
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
        return params
    }

    addToCart = () => {
        const { props, state, quantity } = this;
        const { optionSelections, optionCodes } = state;
        const { addItemToCart, product } = props;

        if (Identify.hasConnector() && product && product.id) {
            this.missingOption = false
            const params = this.prepareParams()
            if (this.missingOption) {
                showToastMessage(Identify.__('Please select the options required (*)'));
                return
            }
            showFogLoading()
            simiAddToCart(this.addToCartCallBack, params)
        } else {
            const payload = {
                item: product,
                productType: product.__typename,
                quantity
            };
            if (isProductConfigurable(product)) {
                appendOptionsToPayload(payload, optionSelections, optionCodes);
            }
            showFogLoading()
            addItemToCart(payload);
        }
    };

    addToCartCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
            this.showError(data)
        } else {
            this.showSuccess(data)
            this.props.getCartDetails()
        }
    }

    addToWishlist = () => {
        const {product, isSignedIn, history} = this.props
        if (!isSignedIn) {
            history.push('/login.html')
        } else if (Identify.hasConnector() && product && product.id) {
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
        if (data.message && data.message.length) {
            this.props.toggleMessages([{
                type: 'success',
                message: data.message[0],
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
        const { configurable_options, simiExtraField, type_id } = props.product;
        const isConfigurable = isProductConfigurable(props.product);
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
                    ( simiExtraField && simiExtraField.app_options && simiExtraField.app_options.custom_options) &&
                    <CustomOptions 
                        key={Identify.randomString(5)}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        ref={e => this.customOption = e}
                        parent={this}
                    />
                }
            </Suspense>
        );
    }

    render() {
        hideFogLoading()
        const { addToCart, mediaGalleryEntries, productOptions, props, state, addToWishlist } = this;
        const { optionCodes, optionSelections, } = state
        const { classes } = props;
        const product = prepareProduct(props.product)
        const { type_id, name } = product;
        return (
            <div className={`${classes.root} container`}>
                <div className={classes.title}>
                    <h1 className={classes.productName}>
                        <span>{ReactHTMLParse(name)}</span>
                    </h1>
                </div>
                <div className={classes.imageCarousel}>
                    <Carousel 
                        images={mediaGalleryEntries} 
                        optionCodes={optionCodes} 
                        optionSelections={optionSelections} 
                        product={product}/>
                </div>
                <div className={classes.mainActions}>
                    <div className={classes.productPrice}>
                        <ProductPrice ref={(price) => this.Price = price} data={product} configurableOptionSelection={optionSelections}/>
                    </div>
                    <div className={classes.options}>{productOptions}</div>
                    <div className={classes.cartActions}>
                        {
                            type_id !== 'grouped' &&
                            <Quantity
                                classes={classes}
                                initialValue={this.quantity}
                                onValueChange={this.setQuantity}
                            />
                        }
                        <div className={classes["add-to-cart-ctn"]}>
                            <Colorbtn 
                                style={{backgroundColor: configColor.button_background, color: configColor.button_text_color}}
                                className={classes["add-to-cart-btn"]} 
                                onClick={addToCart}
                                text={Identify.__('Add to Cart')}/>
                        </div>
                    </div>
                    <div className={classes.wishlistActions}>
                        <Whitebtn 
                            className={classes["add-to-wishlist-btn"]} 
                            onClick={addToWishlist}
                            text={Identify.__('Add to Favourites')}/>
                    </div>
                </div>
                <div className={classes.description}>
                    <h2 className={classes.descriptionTitle}>
                        <span>{Identify.__('Description')}</span>
                    </h2>
                    <RichText content={product.description} />
                </div>
                <div className={classes.techspec}><Techspec product={product}/></div>
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
        description: string
    }).isRequired
};

export default classify(defaultClasses)(ProductFullDetail);