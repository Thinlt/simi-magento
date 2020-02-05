import React from 'react'
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import Identify from 'src/simi/Helper/Identify'
import { configColor } from 'src/simi/Config'
import Deleteicon from 'src/simi/App/Bianca/BaseComponents/Icon/Trash'
import Image from 'src/simi/BaseComponents/Image'
import ReactHTMLParse from 'react-html-parser';
import { Price } from '@magento/peregrine'
import {updateSubProductSpecialItem} from 'src/simi/Model/Cart'
import { getCartDetails } from 'src/actions/cart';
import { connect } from 'src/drivers';
import { productUrlSuffix } from 'src/simi/Helper/Url';
import { showFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';

require('./SpecialCartItem.scss')

const getVendorName = (vendorId) => {
    const storeConfig = Identify.getStoreConfig()
    const vendorList = storeConfig.simiStoreConfig.config.vendor_list;
    const vendorName = vendorList.find(vendor => {
        return vendor.entity_id === vendorId; //entity_id is Vendor ID in vendor model
    })
    return (vendorName && vendorName.vendor_id)?vendorName.vendor_id:'';
}

const SpecialCartItem = props => {
    const {itemTotal, handleLink, currencyCode, isPhone} = props
    console.log(props)
    let subItems = null
    if (itemTotal.simi_pre_order_option) {
        subItems = JSON.parse(itemTotal.simi_pre_order_option)
    } else if (itemTotal.simi_trytobuy_option) {
        subItems = JSON.parse(itemTotal.simi_trytobuy_option)
    }
    if (!subItems)
        return ''
        
    const {simi_image, name} = itemTotal

    const removeFromCart = (subProductSku) => {
        if (confirm(Identify.__('Are you sure?')) === true) {
            showFogLoading();
            updateSubProductSpecialItem(
                () => {
                    props.getCartDetails();
                },
                itemTotal.item_id,
                subProductSku,
                0
            )
        }
    }
    return (
        <React.Fragment>
            <div className="special-product-header">
                <div className="sp-image">
                    <img src={simi_image} alt="special cart item" />
                </div>
                <div className="sp-name">
                    {name}
                </div>
            </div>
            {
                subItems.map((subItem, index) => {

                    const itemPrice = (
                        <div className="sub-item item-price">
                            {isPhone && <div className='item-label'>{Identify.__('Unit Price')}</div>}
                            <div className='cart-item-value'>
                                <Price
                                    currencyCode={currencyCode}
                                    value={parseFloat(subItem.product_final_price)}
                                />
                            </div>
                        </div>
                    )
                    
                    const itemSubTotal = (
                        <Price
                            currencyCode={currencyCode}
                            value={subItem.quantity * parseFloat(subItem.product_final_price)}
                        />
                    )
                    const image = subItem.image

                    const optionText = [];
                    if (subItem.frontend_option) {
                        const options = subItem.frontend_option;
                        for (const i in options) {
                            const option = options[i];
                            optionText.push(
                                <div key={Identify.randomString(5)}>
                                    <span>{option.label}</span> : {ReactHTMLParse(option.value)}
                                </div>
                            );
                        }
                    }

                    const itemInfo = (
                        <div className='cart-item-info'>
                            <div className='des-cart'>
                                <div
                                    role="presentation"
                                    style={{ color: configColor.content_color }}
                                    onClick={() => {
                                        handleLink(`/${subItem.url_key}${productUrlSuffix()}`)
                                    }}>
                                    <div className="item-name">{subItem.name}</div>
                                </div>
                                <div className='item-options'>{optionText.reverse()}</div>
                                {!props.isOpen
                                ?   
                                    <div className='designer-name'>{subItem.vendor_id && getVendorName(subItem.vendor_id)}</div>
                                :
                                    null
                                }
                            </div>
                        </div>
                    )

                    const itemQty = (
                        <div className='sub-item item-qty'>
                            {isPhone && <div className='item-label'>{Identify.__('Qty')}</div>}
                            <div className="minicart-qty-title">{Identify.__('Quantity')}</div>
                            <input
                                min={1}
                                readOnly={true}
                                type="number"
                                pattern="[1-9]*"
                                defaultValue={subItem.quantity}
                            />
                        </div>
                    )
                    
                    return (
                        <div key={subItem.sku} className='cart-siminia-item special-cart-subitem'>
                            <div
                                role="presentation"
                                onClick={() => {
                                    handleLink(location)
                                }}
                                className='img-cart-container'
                                style={{ borderColor: configColor.image_border_color }}>
                                <Image
                                    src={
                                        image ?
                                            resourceUrl(image, {
                                                type: 'image-product',
                                                width: 300
                                            }) :
                                            logoUrl()
                                    }
                                    alt="special product" />
                            </div>
                            <div className="cart-item-detail">
                                {props.isOpen
                                ?   <div>
                                        {itemInfo}
                                        {itemQty}
                                    </div> 
                                :   itemInfo
                                }
                                {!props.isOpen? itemPrice : null
                                }
                                {!props.isOpen ? itemQty : null}
                                <div>
                                    {itemSubTotal}
                                    <div
                                        role="button"
                                        tabIndex="0"
                                        className='sub-item item-delete'
                                        onClick={() => removeFromCart(subItem.sku)}
                                        onKeyUp={() => removeFromCart(subItem.sku)}
                                    >
                                        <Deleteicon
                                            style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                                        {Identify.__('Remove')}
                                    </div>
                                </div>
                            </div>
                        </div>
                    )
                })
            }
        </React.Fragment>
    )
}
const mapDispatchToProps = {
    getCartDetails
};

export default connect(
    null,
    mapDispatchToProps
)(SpecialCartItem);
