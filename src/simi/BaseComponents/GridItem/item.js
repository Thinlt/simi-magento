import React , { useState } from 'react';
import defaultClasses from './item.css'
import {configColor} from 'src/simi/Config';
import PropTypes from 'prop-types';
import ReactHTMLParse from 'react-html-parser'
import { mergeClasses } from 'src/classify'
import Price from 'src/simi/BaseComponents/Price';
import {prepareProduct} from 'src/simi/Helper/Product'
import { Link } from 'src/drivers';
import LazyLoad from 'react-lazyload';
import { logoUrl } from 'src/simi/Helper/Url'

const productUrlSuffix = '.html';

const Griditem = props => {
    const item = prepareProduct(props.item)
    const logoUrl = logoUrl()
    const { classes } = props
    if (!item) return '';
    const itemClasses = mergeClasses(defaultClasses, classes);
    const { name, url_key, id, small_image, price, type_id } = item
    const location = {
        pathname: `/${url_key}${productUrlSuffix}`,
        state: {
            product_id: id,
            item_data: item
        },
    }
    
    const [imageUrl, setImageUrl] = useState(small_image)

    const image = (
        <div 
            role="presentation"
            className={itemClasses["siminia-product-image"]}
            style={{borderColor: configColor.image_border_color,
                backgroundColor: 'white'
            }}
            onError={() => {if(imageUrl !== logoUrl) setImageUrl(logoUrl)}}
            >
            <div style={{position:'absolute',top:0,bottom:0,width: '100%', padding: 1}}>
                <Link to={location}>
                    {<img src={imageUrl} alt={name}/>}
                </Link>
            </div>
        </div>
    )

    return (
        <div className={`${itemClasses["product-item"]} ${itemClasses["siminia-product-grid-item"]}`}>
            {
                props.lazyImage?
                (<LazyLoad placeholder={<img alt={name} src={logoUrl} style={{maxWidth: 60, maxHeight: 60}}/>}>
                    {image}
                </LazyLoad>):
                image
            }
            <div className={itemClasses["siminia-product-des"]}>
                <div role="presentation" className={`${itemClasses["product-name"]} ${itemClasses["small"]}`} onClick={()=>props.handleLink(location)}>{ReactHTMLParse(name)}</div>
                <div role="presentation" className={itemClasses["prices-layout"]} id={`price-${id}`} onClick={()=>props.handleLink(location)}>
                    <Price
                        prices={price} type={type_id}
                    />
                </div>
            </div>
        </div>
    )
}

Griditem.contextTypes = {
    item: PropTypes.object,
    handleLink: PropTypes.func,
    classes: PropTypes.object,
    lazyImage: PropTypes.bool,
}

export default Griditem;
