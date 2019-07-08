import React, { Component } from 'react';
import { func, number, shape, string } from 'prop-types';
import { Price } from '@magento/peregrine';
import classify from 'src/classify';
import { Link } from 'src/drivers';
import { resourceUrl } from 'src/simi/Helper/Url'
import ReactHTMLParse from 'react-html-parser';

import defaultClasses from './suggestedProduct.css';

const productUrlSuffix = '.html';

class SuggestedProduct extends Component {
    static propTypes = {
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

    handleClick = () => {
        const { onNavigate } = this.props;

        if (typeof onNavigate === 'function') {
            onNavigate();
        }
    };

    render() {
        const { handleClick, props } = this;
        const { classes, url_key, small_image, name, price } = props;

        const uri = resourceUrl(`/${url_key}${productUrlSuffix}`);

        return (
            <Link className={classes.root} to={uri} onClick={handleClick}>
                <span className={classes.image}>
                    <img
                        alt={name}
                        src={small_image}
                        style={{maxWidth: 60, maxHeight: 60}}
                    />
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
}

export default classify(defaultClasses)(SuggestedProduct);