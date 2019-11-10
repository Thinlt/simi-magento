import React from 'react';
import { configColor } from 'src/simi/Config';
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
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { addToCart as simiAddToCart } from 'src/simi/Model/Cart';
import { Colorbtn } from 'src/simi/BaseComponents/Button'
import { updateItemInCart } from 'src/actions/cart'
import QuickView from '../../App/Bianca/BaseComponents/QuickView';

const $ = window.$;
require('./item.scss')


class Griditem extends React.Component {
    constructor(props) {
        super(props)
        this.state = ({
            openModal : false
        })
    }

    addToCart = () => {
        console.log("add to cart")
    }

    addToWishlist = () => {
        console.log("add to wishlist")
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

    render() {
        const { props, addToCart, addToWishlist, addToCompare } = this
        const item = prepareProduct(props.item)
        const logo_url = logoUrl()
        if (!item) return '';
        const { name, url_key, id, price, type_id, small_image, simiExtraField } = item
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
                onMouseOut={(e) => this.hideBtnQuickView(id)}
            >
                <div
                    style={{ position: 'absolute', left: 0, top: 0, bottom: 0, width: '100%' }}>
                    <Link to={location}>
                        {<Image className={`img-${id}`} src={small_image} alt={name} />}
                    </Link>
                </div>
                <div className={`quick-view view-item-${id}`}
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
                className="product-item siminia-product-grid-item">
                <QuickView openModal={this.state.openModal} closeModal={this.closeModal} product={item} />
                {
                    props.lazyImage ?
                        (<LazyLoad placeholder={<img alt={name} src={logo_url} style={{ maxWidth: 60, maxHeight: 60 }} />}>
                            {image}
                        </LazyLoad>) :
                        image
                }
                <div className="siminia-product-des">
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
                            vendor name
                        </div>
                    </div>
                    <div className="cart-wishlish-compare">
                        <Colorbtn
                            style={{ backgroundColor: '#101820', color: '#FFF' }}
                            className="add-to-cart-btn"
                            onClick={addToCart}
                            text={Identify.__('Add to Cart')} />
                        <div className="wishlistAction">
                            <div className="wishlist">
                                <span
                                    className="add-to-wishlist-btn icon-chef"
                                    onClick={addToWishlist}
                                >
                                </span>
                            </div>
                        </div>
                        <div className="compareAction">
                            <div className="compare">
                                <span
                                    className="add-to-compare-btn icon-bench-press"
                                    onClick={addToCompare}
                                >
                                </span>
                            </div>
                        </div>
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

export default Griditem;
