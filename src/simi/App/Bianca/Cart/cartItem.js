import React, {useRef, useState, Fragment} from 'react'
import Identify from 'src/simi/Helper/Identify'
import Deleteicon from 'src/simi/App/Bianca/BaseComponents/Icon/Trash'
import EditIcon from 'src/simi/BaseComponents/Icon/Pencil'
import Image from 'src/simi/BaseComponents/Image'
import { configColor } from 'src/simi/Config'
import { Price } from '@magento/peregrine'
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import {
    showFogLoading,
    hideFogLoading
} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import ReactHTMLParse from 'react-html-parser';
import SpecialCartItem from './SpecialCartItem'
import { productUrlSuffix } from 'src/simi/Helper/Url';

require('./cartItem.scss')

const CartItem = props => {
    const inputQty = useRef(null)
    const { currencyCode, item, isPhone, itemTotal, handleLink } = props
    if (itemTotal && (itemTotal.simi_pre_order_option || itemTotal.simi_trytobuy_option))
        return <SpecialCartItem itemTotal={itemTotal}
            handleLink={handleLink} currencyCode={currencyCode} isPhone={isPhone} isOpen={props.isOpen}/>
    const tax_cart_display_price = 3; // 1 - exclude, 2 - include, 3 - both

    const rowPrice = tax_cart_display_price == 1 ? itemTotal.price : itemTotal.price_incl_tax
    const itemprice = (
        <Price
            currencyCode={currencyCode}
            value={rowPrice}
        />
    )
    const rowTotal = tax_cart_display_price == 1 ? itemTotal.row_total : itemTotal.row_total_incl_tax
    const subtotal = itemTotal && rowTotal && <Price
        currencyCode={currencyCode}
        value={rowTotal}
    />

    const optionText = [];
    if (item.options) {
        const options = item.options;
        for (const i in options) {
            const option = options[i];
            optionText.push(
                <div key={Identify.randomString(5)}>
                    <span>{option.label}</span> : {ReactHTMLParse(option.value)}
                </div>
            );
        }
    }

    const getVendorName = (vendorId) => {
        const storeConfig = Identify.getStoreConfig();
        let vendorList;
        if(storeConfig){
            vendorList = storeConfig.simiStoreConfig.config.vendor_list;
            const vendor = vendorList.find(vendor => {
                if(vendorId === 'default'){
                    return null;
                }
                return vendor.entity_id === vendorId; //entity_id is Vendor ID in vendor model
            })
            let vendorName = '';
            if (vendor && vendor.firstname) vendorName = `${vendor.firstname}`;
            if (vendor && vendor.lastname) vendorName = `${vendorName} ${vendor.lastname}`;
            const {profile} = vendor || {}
            vendorName = profile && profile.store_name || vendorName;
            if (vendorName) return vendorName;
            // return (vendorName && vendorName.vendor_id)?vendorName.vendor_id:'';
        }
    }

    const itemInfo = (
        <div className='cart-item-info'>
            <div className='des-cart'>
                <div
                    role="presentation"
                    style={{ color: configColor.content_color }}
                    onClick={() => {
                        handleLink(`/${item.url_key}${productUrlSuffix()}`)
                    }}>
                    <div className="item-name">{item.name}</div>
                </div>
                {/* <div className='item-sku'>{Identify.__('Product code:')} {item.sku}</div> */}
                {Array.isArray(optionText) && optionText.length
                ?
                    <div className='item-options'>{optionText.reverse()}</div>
                :   null
                }
                {item.giftcard_values ?
                    <React.Fragment>
                    {item.giftcard_values.aw_gc_amount && 
                        <div className='item-options-extra' key={1}>
                            <span>{Identify.__('Card Value')}</span>:&nbsp;<Price
                                    currencyCode={currencyCode}
                                    value={parseFloat(item.giftcard_values.aw_gc_amount)}
                                />
                        </div>
                    }
                    {item.giftcard_values.aw_gc_recipient_name && item.giftcard_values.aw_gc_recipient_email !== props.email && 
                        <div className='item-options-extra' key={2}>
                            <span>{Identify.__('Recipient Name')}</span>: {item.giftcard_values.aw_gc_recipient_name}
                        </div>
                    }
                    {item.giftcard_values.aw_gc_recipient_email && item.giftcard_values.aw_gc_recipient_email !== props.email && 
                        <div className='item-options-extra' key={3}>
                            <span>{Identify.__('Recipient Email')}</span>: {item.giftcard_values.aw_gc_recipient_email}
                        </div>
                    }
                    {item.giftcard_values.aw_gc_delivery_date && 
                        <div className='item-options-extra' key={4}>
                            <span>{Identify.__('Send Date')}</span>: {item.giftcard_values.aw_gc_delivery_date}
                        </div>
                    }
                    </React.Fragment>
                    : null
                }
                {!props.isOpen && !isPhone
                ?   
                    <div className='designer-name'>{item.attribute_values && getVendorName(item.attribute_values.vendor_id)}</div>
                :
                    null
                }
            </div>
        </div>
    )

    const itemPrice = (
        <div className="sub-item item-price">
            {/* {isPhone && <div className='item-label'>{Identify.__('Unit Price')}</div>} */}
            <div className='cart-item-value'>{itemprice}</div>
        </div>
    )

    const itemQty = (
        <div className='sub-item item-qty'>
            {/* {isPhone && <div className='item-label'>{Identify.__('Qty')}</div>} */}
            <div className="minicart-qty-title">{Identify.__('Quantity')}</div>
            <input
                min={1}
                // eslint-disable-next-line jsx-a11y/no-autofocus
                type="number"
                pattern="[1-9]*"
                defaultValue={item.qty}
                onBlur={(event) => {
                    if (parseInt(event.target.value, 10) !== parseInt(item.qty, 10)){
                        props.updateCartItem(item,parseInt(event.target.value, 10))
                    }
                }
                }
                onKeyUp={e => {
                    // if (e.keyCode === 13) {
                    //     if (parseInt(event.target.value, 10) !== parseInt(item.qty, 10))
                    //         updateCartItem(parseInt(event.target.value, 10))
                    // }
                }}
            />
            {isPhone && 
                <div
                    role="button"
                    tabIndex="0"
                    className="item-edit"
                    onClick={() => {
                        handleLink(location)
                    }}
                    onKeyUp={() => {}}
                    >
                    <EditIcon 
                        style={{width: '16px', height: '16px', marginRight: '8px', marginLeft: 'auto' }}/>
                    <div>{Identify.__('Edit')}</div>
                </div>
            }
        </div>
    )

    const itemSubTotal = (
        <div className='sub-item  item-subtotal'>
            {/* {isPhone && <div className='item-label'>{Identify.__('Total Price')}</div>} */}
            <div className='cart-item-value'>{subtotal}</div>
        </div>
    )

    
    const location = `/product.html?sku=${item.simi_sku ? item.simi_sku : item.sku}`
    const image = (item.image && item.image.file) ? item.image.file : item.simi_image
    const renderItemMobile = (
        <div key={Identify.randomString(5)} className='cart-siminia-item'>
            {!props.isOpen
            ?   
                <div className='designer-name'>{item.attribute_values && getVendorName(item.attribute_values.vendor_id)}</div>
            :
                null
            }
            <div style={{display:"flex"}}>
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
                        alt={item.name} />
                </div>
                <div className="cart-item-detail">
                    {props.isOpen || isPhone
                        ?   <div>
                                {itemInfo}
                                {itemQty}
                            </div> 
                        :   itemInfo
                    }
                    {/* {!props.isOpen? itemPrice : null} */}
                    <div style={{display:'flex'}}>
                        {itemSubTotal}
                        <div
                            role="button"
                            tabIndex="0"
                            className='sub-item item-delete'
                            onClick={() => props.removeFromCart(item)}
                            onKeyUp={() => props.removeFromCart(item)}
                        >
                            <Deleteicon
                                style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                            <div>{Identify.__('Remove')}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    )
    hideFogLoading()
    return (
        <Fragment>
            {isPhone
            ?
                <Fragment>
                    {renderItemMobile}
                </Fragment>
            :
                <div key={Identify.randomString(5)} className='cart-siminia-item'>
                    
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
                            alt={item.name} />
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
                                className="item-edit"
                                onClick={() => {
                                    handleLink(location)
                                }}
                                onKeyUp={() => {}}
                            >
                                <EditIcon 
                                    style={{width: '16px', height: '16px', marginRight: '8px', marginLeft: 'auto' }}/>
                                <div>{Identify.__('Edit')}</div>
                            </div>
                            <div
                                role="button"
                                tabIndex="0"
                                className='sub-item item-delete'
                                onClick={() => props.removeFromCart(item)}
                                onKeyUp={() => props.removeFromCart(item)}
                            >
                                <Deleteicon
                                    style={{ width: '16px', height: '16px', marginRight: '8px' }} />
                                <div>{Identify.__('Remove')}</div>
                            </div>
                        </div>
                    </div>
                </div>
            }
        </Fragment>
    );
}
export default CartItem;