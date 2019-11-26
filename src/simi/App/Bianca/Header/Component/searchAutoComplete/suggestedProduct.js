import React from 'react';
import { func, number, shape, string, bool } from 'prop-types';
import Price from 'src/simi/App/Bianca/BaseComponents/Price';
import classify from 'src/classify';
import { Link } from 'src/drivers';
import { resourceUrl } from 'src/simi/Helper/Url'
import ReactHTMLParse from 'react-html-parser';
import LazyLoad from 'react-lazyload';

import defaultClasses from './suggestedProduct.css';
import { logoUrl } from 'src/simi/Helper/Url'
import Image from 'src/simi/BaseComponents/Image'
import { productUrlSuffix } from 'src/simi/Helper/Url';

const SuggestedProduct = props => {
    const handleClick = () => {
        const { onNavigate } = props;
        if (typeof onNavigate === 'function') {
            onNavigate();
        }
    }
    const logo_url = logoUrl()
    const {classes, url_key, small_image, name, price, type_id, simiExtraField } = props;
    const uri = resourceUrl(`/${url_key}${productUrlSuffix()}`);
    const place_holder_img = <img alt={name} src={logo_url} style={{maxWidth: 60, maxHeight: 60}}/>

    return (
        <Link className={classes.root} to={uri} onClick={handleClick}>
            <span className={classes.image}>
                <LazyLoad 
                    placeholder={place_holder_img}>
                    <Image
                        alt={name}
                        src={small_image? resourceUrl(small_image, {
                            type: 'image-product',
                            width: 60
                        }) : logoUrl()}
                        style={{maxWidth: 60, maxHeight: 60}}
                    />
                </LazyLoad>
            </span>
            <span className="right-label">
                <span className={classes.name}>{ReactHTMLParse(name)}</span>
                <span className={classes.price}>
                    <Price prices={price} type={type_id} classes={classes} />
                </span>
                {
                    simiExtraField && simiExtraField.attribute_values && simiExtraField.attribute_values.vendor_name && 
                    <div className="vendor"><span>{simiExtraField.attribute_values.vendor_name}</span></div>
                }
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
        has_special_price: bool,
        maximalPrice: shape({
            amount: shape({
                currency: string,
                value: number
            })
        }),
        minimalPrice: shape({
            amount: shape({
                currency: string,
                value: number
            })
        }),
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
