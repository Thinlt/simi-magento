import React from 'react';
import PropTypes from 'prop-types';
import ReactHTMLParse from 'react-html-parser'
import Price from 'src/simi/App/Bianca/BaseComponents/Price';
import { prepareProduct } from 'src/simi/Helper/Product'
import { Link } from 'src/drivers';
import LazyLoad from 'react-lazyload';
import { logoUrl } from 'src/simi/Helper/Url'
import Image from 'src/simi/BaseComponents/Image'
// import {StaticRate} from 'src/simi/BaseComponents/Rate'
import Identify from 'src/simi/Helper/Identify'
import { productUrlSuffix, saveDataToUrl } from 'src/simi/Helper/Url';
import { Colorbtn } from 'src/simi/BaseComponents/Button'
import QuickView from 'src/simi/App/Bianca/BaseComponents/QuickView';
import { addToWishlist as simiAddToWishlist } from 'src/simi/Model/Wishlist';
import { Util } from '@magento/peregrine';
const { BrowserPersistence } = Util;
import {showToastMessage} from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { withRouter } from 'react-router-dom';

const $ = window.$;
require('./item.scss')


class Griditem extends React.Component {
    constructor(props) {
        super(props)
        const isPhone = window.innerWidth < 1024 
        this.state = ({
            openModal : false,
            isPhone: isPhone,
        })
        this.vendorName = ''
        this.setIsPhone()
    }

    setIsPhone(){
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width < 1024;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
    }

    addToCart = () => {
        const {item} = this.props
        if (item && item.simiExtraField && item.simiExtraField.attribute_values) {
            const {attribute_values} = item.simiExtraField
            if ((!parseInt(attribute_values.has_options)) && attribute_values.type_id === 'simple') {
                const params = {product: String(item.id), qty: '1'}
                showFogLoading()
                simiAddToCart(this.addToCartCallBack, params)
                return
            }
        }
        const { url_key } = item
        const { history } = this.props
        const product_url = `/${url_key}${productUrlSuffix()}`
        history.push(product_url)
    }

    addToCartCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
            showToastMessage(Identify.__('Problem occurred.'))
        } else {
            console.log(data)
        }
    }

    addToWishlist = () => {
        const storage = new BrowserPersistence()
        const isSignedIn = storage.getItem('signin_token')
        const {item} = this.props
        if (!isSignedIn) {
            showToastMessage(Identify.__('You must login or register to add items to your wishlist.'))
        } else if (item && item.id) {
            const params = {product: String(item.id), qty: '1'}
            showFogLoading()
            simiAddToWishlist(this.addToWishlistCallBack, params)
        }
    }

    addToWishlistCallBack = (data) => {
        hideFogLoading()
        if (data.errors) {
            showToastMessage(Identify.__('Problem occurred.'))
        } else {
            if (this.wlBtnRef) {
                this.wlBtnRef.classList.add("added-item")
            }
        }
    }

    addToCompare = () => {
        console.log("add to compare")
    }

    showBtnQuickView = (id) => {
        $(`.view-item-${id}`).css('display', 'block')
        //$(`img.img-${id}`).css('object-fit','cover')
    }
    hideBtnQuickView = (id) => {
        $(`.view-item-${id}`).css('display', 'none')
        //$(`img.img-${id}`).css('object-fit','contain')
    }
    showModalQuickView = () => {
        this.setState({
            openModal : true
        })
    }
    closeModal = () =>{
        this.setState({
            openModal : false
        })
    }

    renderVendorName = (item) => {
        if (item && item.simiExtraField && item.simiExtraField.attribute_values) {
            const {attribute_values} = item.simiExtraField
            if (attribute_values && attribute_values.vendor_name) {
                this.vendorName = attribute_values.vendor_name
            }
        }
        return this.vendorName
    }

    wishlistCompareAction = () => {
        const { addToWishlist, addToCompare } = this
        return (
            <React.Fragment>
                <div className="wishlistAction">
                    <div className="wishlist">
                        <span
                            ref={(item)=> {this.wlBtnRef = item}}
                            role="presentation"
                            className="add-to-wishlist-btn icon-chef"
                            onClick={addToWishlist}
                        >
                        </span>
                    </div>
                </div>
                <div className="compareAction">
                    <div className="compare">
                        <span
                            role="presentation"
                            className="add-to-compare-btn icon-bench-press"
                            onClick={addToCompare}
                        >
                        </span>
                    </div>
                </div>
            </React.Fragment>
        )
    }

    render() {
        const { props, addToCart } = this
        const item = prepareProduct(props.item)
        const logo_url = logoUrl()
        if (!item) return '';
        const { name, url_key, id, price, type_id, small_image } = item
        const product_url = `/${url_key}${productUrlSuffix()}`
        saveDataToUrl(product_url, item)
        const location = {
            pathname: product_url,
            state: {
                product_id: id,
                item_data: item
            },
        }

        const image = (
            <div
                role="presentation"
                className="siminia-product-image"
                style={{
                    backgroundColor: 'white'
                }}
                onMouseOver={(e) => this.showBtnQuickView(id)}
                onFocus={(e) => this.showBtnQuickView(id)}
                onMouseOut={(e) => this.hideBtnQuickView(id)}
                onBlur={(e) => this.hideBtnQuickView(id)}
            >
                <div
                    style={{ position: 'absolute', left: 0, top: 0, bottom: 0, width: '100%' }}>
                    <Link to={location}>
                        {<Image className={`img-${id}`} src={small_image} alt={name} />}
                    </Link>
                </div>
                <div 
                    role="presentation"
                    className={`quick-view view-item-${id}`}
                    onClick={() => this.showModalQuickView()}
                >
                    <div className="btn-quick-view">
                        {Identify.__('quick view')}
                    </div>
                </div>
            </div>
        )

        return (
            <div
                className="siminia-product-grid-item">
                <QuickView openModal={this.state.openModal} closeModal={this.closeModal} product={item} />
                <div style={{position: 'relative'}}>
                    {
                        props.lazyImage ?
                            (<LazyLoad placeholder={<img alt={name} src={logo_url} style={{ maxWidth: 60, maxHeight: 60 }} />}>
                                {image}
                            </LazyLoad>) :
                            image
                    }  
                    {this.state.isPhone && this.wishlistCompareAction()}
                </div>
                
                <div className="siminia-product-des">
                    <div className="product-des-info">
                        <div className="product-name">
                            <div role="presentation" className="product-name small" onClick={() => props.handleLink(location)}>{ReactHTMLParse(name)}</div>
                        </div>
                        <div className="vendor-and-price">
                            <div role="presentation" className={`prices-layout ${Identify.isRtl() ? "prices-layout-rtl" : ''}`} id={`price-${id}`} onClick={() => props.handleLink(location)}>
                                <Price
                                    prices={price} type={type_id}
                                />
                            </div>
                            <div className="vendor">
                                {this.renderVendorName(item)}
                            </div>
                        </div>
                    </div>
                    <div className="cart-wishlish-compare">
                        <Colorbtn
                            style={{ backgroundColor: '#101820', color: '#FFF' }}
                            className="add-to-cart-btn"
                            onClick={addToCart}
                            text={Identify.__('Add to Cart')} />
                        {!this.state.isPhone && this.wishlistCompareAction()}
                    </div>
                </div>
            </div>
        )
    }
}

Griditem.contextTypes = {
    item: PropTypes.object,
    handleLink: PropTypes.func,
    classes: PropTypes.object,
    lazyImage: PropTypes.bool,
}

export default (withRouter)(Griditem);