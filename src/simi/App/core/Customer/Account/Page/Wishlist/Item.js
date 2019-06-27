import React from "react";
import { compose } from 'redux';
import { connect } from 'src/drivers';
import Identify from "src/simi/Helper/Identify";
import {Colorbtn} from 'src/simi/BaseComponents/Button'
import {configColor} from 'src/simi/Config'
import ReactHTMLParse from 'react-html-parser';
import { Link } from 'src/drivers';
import Trash from 'src/simi/BaseComponents/Icon/Trash';
import {confirmAlert} from 'react-confirm-alert';
import classify from 'src/classify';
import itemStyle from './item.css';
import { Price } from '@magento/peregrine';
import { removeItem } from '/src/simi/Model/Wishlist'
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import confirmAllertStyles from 'react-confirm-alert/src/react-confirm-alert.css'

const defaultClasses = {...confirmAllertStyles, ...itemStyle }

const productUrlSuffix = '.html';

class Item extends React.Component {
    processData(data) {
        console.log(data)
        hideFogLoading()
        if (this.addCart || this.removeItem) {
            if (this.addCart) {

            }
            if (this.removeItem) {
                this.props.toggleMessages([{type: 'success', message: Identify.__('This product has been removed from your wishlist')}])
                this.props.setIsLoaded()
            }
            this.this.addCart = false
            this.removeItem = false;
        }
    }

    getWishList = () => {
        this.getWL = true;
    }
    addToCart(location = false) {
        // const item = this.props.item;
        // if (item.type_id !== 'simple') {
        //     if (location)
        //         this.handleLink(location)
        //     return
        // }
        // this.addCart = true;
        // Identify.showLoading();
        // this.wishlistModel.requestAddToCart(item.wishlist_item_id);
        // Analytics.analyticsTracking(
        //     {
        //         mixpanel : true,
        //         ga : false
        //     },
        //     {
        //         action: `clicked_add_to_cart_button`,
        //         name: `selected_product_${item.product_name}`,
        //         sku: `selected_product_${item.sku}`,
        //         product_id: `selected_product_${item.product_id}`,
        //     }
        // )
    }

    onTrashItem = (id) => {
        if(id){
            console.log(id)
            confirmAlert({
                title: '',                        // Title dialog
                message: Identify.__('Are you sure you want to delete this product?'),        // Message dialog
                buttons: [
                    {
                        label: Identify.__('Confirm'),
                        onClick: () => this.handleTrashItem(id)
                    },
                    {
                        label: Identify.__('Cancel'),
                    }
                ]
            });
        }
    }

    handleTrashItem = (id) => {
        showFogLoading();
        this.removeItem = true;
        removeItem(id, this.processData.bind(this))
    }

    render() {
        const storeConfig = Identify.getStoreConfig()
        if (!this.currencyCode)
            this.currencyCode = storeConfig?storeConfig.simiStoreConfig?storeConfig.simiStoreConfig.currency:storeConfig.storeConfig.default_display_currency_code:null
        const {item, classes} = this.props;
        if(!item || !item.product){
            return null;
        }
        
        this.location = {
            pathname: `/${item.product.url_key}${productUrlSuffix}`,
            state: {
                product_sku: item.product.sku,
                product_id: item.product.id,
                item_data: item
            },
        }
        
        const addToCartString = (item.is_salable && (parseInt(item.is_salable, 10) !== 1)) ? Identify.__('Out of stock') : Identify.__('Buy now');
        
        const image = item.product.small_image.url && (
            <div 
                className={classes["siminia-product-image"]}
                style={{borderColor: configColor.image_border_color,
                    backgroundColor: 'white'
                }}>
                <Link to={this.location}>
                    <div style={{position:'absolute',top:0,bottom:0,width: '100%', padding: 1}}>
                        <img src={item.product.small_image.url} alt={item.product.name}/>
                    </div>
                </Link>
                <span 
                    role="presentation"
                    className={classes["trash-item"]}
                    style={{position: 'absolute', bottom: 1, left: 1, width: 30, height: 30, cursor: 'pointer', zIndex: 22}} onClick={() => this.onTrashItem(item.id)}>
                    <Trash style={{fill: '#333132', width: 30, height: 30}} />
                </span>
            </div>
        );

        const itemAction = 
            <div className={classes["product-item-action"]}>
                <Colorbtn 
                    style={{backgroundColor: configColor.button_background, color: configColor.button_text_color}}
                    className={classes["grid-add-cart-btn"]} 
                    onClick={() => this.addToCart(this.location)}
                    text={addToCartString}/>
                <Link 
                    className={classes["view-link"]}
                    to={this.location}
                >{Identify.__('View')}</Link>
            </div>
        
        return (
            <div className={`${classes['product-item']} ${classes['siminia-product-grid-item']} ${this.props.showBuyNow?classes['two-btn']:classes['one-btn']}`}>
                {image}
                <div className={classes["siminia-product-des"]}>
                    <Link to={this.location} className={classes["product-name"]}>{ReactHTMLParse(item.product.name)}</Link>
                    <div className={classes["prices-layout"]} id={`price-${item.product_id}`}>
                        {
                            (item.product.price && item.product.price.regularPrice && item.product.price.regularPrice.amount) &&
                            <Price
                                value={parseFloat(item.product.price.regularPrice.amount.value)}
                                currencyCode={item.product.price.regularPrice.amount.currency}
                            />
                        }
                    </div>
                </div>
                {itemAction}
            </div>
        );
    }
}

const mapDispatchToProps = {
    toggleMessages
}
export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
)(Item);