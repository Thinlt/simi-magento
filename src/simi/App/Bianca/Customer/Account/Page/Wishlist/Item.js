import React from "react";
import Identify from "src/simi/Helper/Identify";
import {Colorbtn} from 'src/simi/BaseComponents/Button';
// import {configColor} from 'src/simi/Config'
import ReactHTMLParse from 'react-html-parser';
import { Link } from 'src/drivers';
import Deleteicon from 'src/simi/App/Bianca/BaseComponents/Icon/Trash';
import { removeWlItem, addWlItemToCart } from 'src/simi/Model/Wishlist';
import {hideFogLoading, showFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { resourceUrl } from 'src/simi/Helper/Url';
import { formatPrice } from 'src/simi/Helper/Pricing';
import {productUrlSuffix} from 'src/simi/Helper/Url';

class Item extends React.Component {
    processData(data) {
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
        } else if (this.addCart || this.removeItem) {
            if (this.addCart) {
                this.props.toggleMessages([{type: 'success', message: Identify.__('This product has been moved to cart'), auto_dismiss: true}])
                const { getCartDetails } = this.props;
                if (getCartDetails)
                    getCartDetails()
                showFogLoading()
                this.props.getWishlist()
            }
            if (this.removeItem) {
                this.props.toggleMessages([{type: 'success', message: Identify.__('This product has been removed from your wishlist'), auto_dismiss: true}])
                showFogLoading()
                this.props.getWishlist()
            }
        }

        this.addCart = false
        this.removeItem = false;
    }

    addToCart(id, location = false) {
        const item = this.props.item;
        if (item.type_id !== 'simple') {
            if (location)
                this.props.history.push(location)
            return
        }
        this.addCart = true;
        addWlItemToCart(id, this.processData.bind(this))
    }

    onTrashItem = (id) => {
        if(id){
            if (confirm(Identify.__('Are you sure you want to delete this product?')) == true) {
                this.handleTrashItem(id)
            }
        }
    }

    handleTrashItem = (id) => {
        showFogLoading();
        this.removeItem = true;
        removeWlItem(id, this.processData.bind(this))
    }

    handleLink(url) {
        if (url && this.props.history) {
            this.props.history.push(url)
        }
    }

    render() {
        const storeConfig = Identify.getStoreConfig()
        if (!this.currencyCode)
            this.currencyCode = storeConfig?storeConfig.simiStoreConfig?storeConfig.simiStoreConfig.currency:storeConfig.storeConfig.default_display_currency_code:null
        const {item} = this.props;
        this.location = {
            pathname: item.product_url_key + productUrlSuffix(),
            state: {
                product_sku: item.product_sku,
                product_id: item.product_id,
                item_data: item
            },
        }
        
        const addToCartString = Identify.__('Add To Cart')
        
        const image = item.product_image && (
            <div className="wishlist-siminia-product-image">
                    <div className="product-image-ctn" role="presentation"
                        onClick={()=>this.handleLink(item.product_url_key + productUrlSuffix())}
                        style={{backgroundImage: `url('${resourceUrl(item.product_image, {type: 'image-product', width: 100})}')`}}>
                    </div>
                
                <span 
                    role="presentation"
                    className="trash-item"
                    onClick={() => this.onTrashItem(item.wishlist_item_id)}>
                    <Deleteicon style={{ width: '16px', height: '16px', marginRight: '8px', color:'#727272' }} />
                </span>
            </div>
        );

        const itemAction = 
            <div className="product-item-action">
                {
                    item.type_id === 'simple' &&
                    <Colorbtn 
                        className="grid-add-cart-btn"
                        onClick={() => this.addToCart(item.wishlist_item_id, this.location)}
                        text={addToCartString}/>
                }
            </div>
        
        let vendorName = ''
        if (item.vendor_id && item.vendor_id !== 'default' && item.vendor_name) {
            vendorName = <div className="vendor-name"><Link to={`/designers/${item.vendor_id}.html`}>{item.vendor_name}</Link></div>
        }  else if (item.vendor_id !== 'default') {
            vendorName = <div className="vendor-name">{item.vendor_name}</div>
        }

        return (
            <div className='wishlist-product-grid-item'>
                {image}
                <div className="wishlistitem-product-des">
                    <Link to={this.location} className="product-name">{ReactHTMLParse(item.product_name)}</Link>
                    {parseFloat(item.product_price) > 0 &&
                        <div className="prices-layout"
                            id={`price-${item.type_id}`}>
                            <div className="price">{formatPrice(parseFloat(item.product_price))}</div>
                            {parseFloat(item.product_regular_price) > 0 && parseFloat(item.product_regular_price) !== parseFloat(item.product_price) && 
                                <div className="price regular">{formatPrice(parseFloat(item.product_regular_price))}</div>
                            }
                        </div>
                    }
                    {vendorName}
                    {itemAction}
                </div>
            </div>
        );
    }
}

export default Item