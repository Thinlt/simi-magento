import React, { useState } from 'react';
import { func, number, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from 'src/classify';
import { Link } from 'src/drivers';
import { resourceUrl } from 'src/simi/Helper/Url'
import ReactHTMLParse from 'react-html-parser';
import LazyLoad from 'react-lazyload';

import defaultClasses from './suggestedProduct.css';
import Identify from 'src/simi/Helper/Identify';

const productUrlSuffix = '.html';

const SuggestedProduct = props => {
    const handleClick = () => {
        const { onNavigate } = this.props;
        if (typeof onNavigate === 'function') {
            onNavigate();
        }
    }
    const logoUrl = Identify.logoUrl()
    const { classes, url_key, small_image, name, price } = props;
    const [imageUrl, setImageUrl] = useState(small_image)
    const uri = resourceUrl(`/${url_key}${productUrlSuffix}`);
    const place_holder_img = <img alt={name} src={logoUrl} style={{maxWidth: 60, maxHeight: 60}}/>

    return (
        <Link className={classes.root} to={uri} onClick={handleClick}>
            <span className={classes.image}>
                <LazyLoad 
                    placeholder={place_holder_img}>
                    <img
                        alt={name}
                        src={imageUrl? resourceUrl(imageUrl, {
                            type: 'image-product',
                            width: 60
                        }) : Identify.logoUrl()}
                        style={{maxWidth: 60, maxHeight: 60}}
                        onError={() => {if(imageUrl !== logoUrl) setImageUrl(logoUrl)}}
                    />
                </LazyLoad>
            </span>
            <span className={classes.name}>{ReactHTMLParse(name)}</span>
            <span className={classes.price}>
                <Price
                    currencyCode={price.regularPrice.amount.currency}
                    value={price.regularPrice.amount.value}
                />
            </span>
        </Link>
    );
}

SuggestedProduct.propTypes = {
    url_key: string.isRequired,
    small_image: string.isRequired,
    name: string.isRequired,
    onNavigate: func,
    price: shape({
        regularPrice: shape({
            amount: shape({
                currency: string,
                value: number
            })
        })
    }).isRequired,
    classes: shape({
        root: string,
        image: string,
        name: string,
        price: string
    })
};

export default classify(defaultClasses)(SuggestedProduct);
