import React, { Component, Suspense } from 'react';
import { arrayOf, bool, func, number, shape, string } from 'prop-types';
import { Form } from 'informed';

import classify from 'src/classify';
import Button from 'src/components/Button';
import Loading from 'src/simi/BaseComponents/Loading'
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import Carousel from './ProductImageCarousel';
import Quantity from './ProductQuantity';
import RichText from 'src/simi/BaseComponents/RichText';
import defaultClasses from './productFullDetail.css';
import appendOptionsToPayload from 'src/util/appendOptionsToPayload';
import findMatchingVariant from 'src/util/findMatchingProductVariant';
import isProductConfigurable from 'src/util/isProductConfigurable';
import Identify from 'src/simi/Helper/Identify';
import {prepareProduct} from 'src/simi/Helper/Product'

import ProductPrice from '../Component/Productprice';
import CustomOptions from './CustomOptions';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';

const ConfigurableOptions = React.lazy(() => import('./ConfigurableOptions'));

class ProductFullDetail extends Component {
    static propTypes = {
        classes: shape({
            cartActions: string,
            description: string,
            descriptionTitle: string,
            details: string,
            detailsTitle: string,
            imageCarousel: string,
            options: string,
            productName: string,
            productPrice: string,
            quantity: string,
            quantityTitle: string,
            root: string,
            title: string
        }),
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
        }).isRequired,
        addToCart: func.isRequired,
        getCartDetails: func.isRequired,
        toggleMessages: func.isRequired,
    };
    
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

    state = {
        optionCodes: new Map(),
        optionSelections: new Map(),
        quantity: 1
    };

    setQuantity = quantity => this.setState({ quantity });

    addToCart = () => {
        const { props, state } = this;
        const { optionSelections, quantity, optionCodes } = state;
        const { addToCart, product } = props;
        const payload = {
            item: product,
            productType: product.__typename,
            quantity
        };
        if (Identify.hasConnector()) {
            const params = {product: String(product.id), qty: quantity?String(quantity):'1'}
            if (this.customOption) {
                const customOptParams = this.customOption.getParams()
                if (customOptParams && customOptParams.options) {
                    params['options'] = customOptParams.options
                }
            }
            if (optionSelections) {
                const super_attribute = {}
                optionSelections.forEach((value, key) => {
                    super_attribute[String(key)] = String(value)
                })
                params['super_attribute'] = super_attribute
            }
            console.log(params)
            showFogLoading()
            simiAddToCart(this.addToCartCallBack, params)
        } else {
            if (isProductConfigurable(product)) {
                appendOptionsToPayload(payload, optionSelections, optionCodes);
            }
            showFogLoading()
            addToCart(payload);
        }
    };

    addToCartCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
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
        } else {
            if (data.message && data.message.length) {
                this.props.toggleMessages([{
                    type: 'success',
                    message: data.message[0],
                    auto_dismiss: true
                }])
            }
            this.props.getCartDetails()
        }
    }

    handleSelectionChange = (optionId, selection) => {
        this.setState(({ optionSelections }) => ({
            optionSelections: new Map(optionSelections).set(
                optionId,
                Array.from(selection).pop()
            )
        }));
    };

    get fallback() {
        return <Loading />;
    }

    get productOptions() {
        const { fallback, handleSelectionChange, props } = this;
        const { simiExtraField } = props
        const { configurable_options } = props.product;
        const isConfigurable = isProductConfigurable(props.product);

        return (
            <Suspense fallback={fallback}>
                {
                    ( simiExtraField && simiExtraField.app_options) &&
                    <CustomOptions 
                        key={Identify.randomString(5)}
                        app_options={simiExtraField.app_options}
                        product_id={this.props.product.entity_id}
                        ref={(e) => this.customOption = e}
                        parent={this}
                    />
                }
                {
                    isConfigurable &&
                    <ConfigurableOptions
                        options={configurable_options}
                        onSelectionChange={handleSelectionChange}
                    />
                }
            </Suspense>
        );
    }

    get mediaGalleryEntries() {
        const { props, state } = this;
        const { product } = props;
        const { optionCodes, optionSelections } = state;
        const { media_gallery_entries, variants } = product;

        const isConfigurable = isProductConfigurable(product);

        if (
            !isConfigurable ||
            (isConfigurable && optionSelections.size === 0)
        ) {
            return media_gallery_entries;
        }

        const item = findMatchingVariant({
            optionCodes,
            optionSelections,
            variants
        });

        if (!item) {
            return media_gallery_entries;
        }

        const images = [
            ...item.product.media_gallery_entries,
            ...media_gallery_entries
        ];
        //filterout the duplicate images
        const returnedImages = []
        var obj = {};
        images.forEach(image=> {
            if (!obj[image.file]) {
                obj[image.file] = true
                returnedImages.push(image)
            }
        })
        return returnedImages
    }

    get isMissingOptions() {
        const { product } = this.props;

        // Non-configurable products can't be missing options
        if (!isProductConfigurable(product)) {
            return false;
        }

        // Configurable products are missing options if we have fewer
        // option selections than the product has options.
        const { configurable_options } = product;
        const numProductOptions = configurable_options.length;
        const numProductSelections = this.state.optionSelections.size;

        return numProductSelections < numProductOptions;
    }

    render() {
        hideFogLoading()
        const {
            addToCart,
            isMissingOptions,
            mediaGalleryEntries,
            productOptions,
            props
        } = this;
        const { classes, isAddingItem } = props;

        const product = prepareProduct(props.product)

        // We want this key to change whenever mediaGalleryEntries changes.
        // Make it dependent on a unique value in each entry (file),
        // and the order.
        const carouselKey = mediaGalleryEntries.reduce((fullKey, entry) => {
            return `${fullKey},${entry.file}`;
        }, '');

        return (
            <Form className={classes.root}>
                <section className={classes.title}>
                    <h1 className={classes.productName}>
                        <span>{product.name}</span>
                    </h1>
                    <ProductPrice ref={(price) => this.Price = price} data={product}/>
                </section>
                <section className={classes.imageCarousel}>
                    <Carousel images={mediaGalleryEntries} key={carouselKey} />
                </section>
                <section className={classes.options}>{productOptions}</section>
                <section className={classes.quantity}>
                    <h2 className={classes.quantityTitle}>
                        <span>Quantity</span>
                    </h2>
                    <Quantity
                        initialValue={this.state.quantity}
                        onValueChange={this.setQuantity}
                    />
                </section>
                <section className={classes.cartActions}>
                    <Button
                        priority="high"
                        onClick={addToCart}
                        disabled={isAddingItem || isMissingOptions}
                    >
                        <span>Add to Cart</span>
                    </Button>
                </section>
                <section className={classes.description}>
                    <h2 className={classes.descriptionTitle}>
                        <span>Product Description</span>
                    </h2>
                    <RichText content={product.description} />
                </section>
                <section className={classes.details}>
                    <h2 className={classes.detailsTitle}>
                        <span>SKU</span>
                    </h2>
                    <strong>{product.sku}</strong>
                </section>
            </Form>
        );
    }
}

export default classify(defaultClasses)(ProductFullDetail);
