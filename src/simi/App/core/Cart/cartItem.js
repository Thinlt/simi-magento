import React from 'react'
import Identify from 'src/simi/Helper/Identify'
import Deleteicon from 'src/simi/BaseComponents/Icon/Trash'
import {configColor} from 'src/simi/Config'
import { Price } from '@magento/peregrine'
import { resourceUrl } from 'src/drivers'

import defaultClasses from './cartItem.css'

const CartItem = props => {
    const { currencyCode, item, isPhone, itemTotal, handleLink } = props
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
                    <b>{option.label}</b> : {option.value}
                </div>
            );
        }
    }

    const updateItemInCart = (quantity) => {
        const { updateItemInCart } = props;
        const payload = {
            item: item,
            quantity: quantity
        };
        updateItemInCart(payload, item.item_id);
    }
    
    const location = `/product.html?sku=${item.sku}`
    return (
        <div key={Identify.randomString(5)} className={defaultClasses['cart-siminia-item']}>
            <div className={defaultClasses['img-and-name']}>
                <div 
                    role="presentation"
                    onClick={() => {
                        handleLink(location)
                    }}
                    className={defaultClasses['img-cart-container']}
                    style={{borderColor: configColor.image_border_color}}>
                    <img 
                        src={resourceUrl(item.image.file, {
                            type: 'image-product',
                            width: 300
                        })} 
                        alt={item.name} />
                </div>
                <div className={defaultClasses['cart-item-info']}>
                    <div className={defaultClasses['des-cart']}>
                        <div 
                        role="presentation"
                            style={{color: configColor.content_color}}
                            onClick={()=>{
                                handleLink(location)
                            }}>
                            <div className="item-name">{item.name}</div>
                        </div>
                        <div className={defaultClasses['item-sku']}>{Identify.__('Product code:')} {item.sku}</div>
                        <div className={defaultClasses['item-options']}>{optionText}</div>
                    </div>
                    
                </div>
            </div>
            <div className={`${defaultClasses['sub-item']} ${defaultClasses['item-price']}`}>
                {isPhone && <div className={defaultClasses['item-label']}>{Identify.__('Unit Price')}</div>}
                <div className={defaultClasses['cart-item-value']} style={{color: configColor.price_color}}>{itemprice}</div>
            </div>
            <div className={`${defaultClasses['sub-item']} ${defaultClasses['item-qty']}`}>
                {isPhone &&<div className={defaultClasses['item-label']}>{Identify.__('Qty')}</div>}
                <input
                    min={1}
                    type="number"
                    pattern="[1-9]*"
                    defaultValue={item.qty}
                    onBlur={(event) =>
                        {
                            if(parseInt(event.target.value, 10) !== parseInt(item.qty, 10))
                                updateItemInCart(parseInt(event.target.value, 10))
                        }
                    }
                    onKeyUp={e => {
                        if(e.keyCode === 13){
                            if(parseInt(event.target.value, 10) !== parseInt(item.qty, 10))
                                updateItemInCart(parseInt(event.target.value, 10))
                        }
                    }}
                />
            </div>
            <div className={`${defaultClasses['sub-item']} ${defaultClasses['item-subtotal']}`}>
                {isPhone && <div className={defaultClasses['item-label']}>{Identify.__('Total Price')}</div>}
                <div className={defaultClasses['cart-item-value']} style={{color: configColor.price_color}}>{subtotal}</div>
            </div>
            <div 
                role="button"
                tabIndex="0"
                className={`${defaultClasses['sub-item']} ${defaultClasses['item-delete']}`} 
                onClick={() => props.removeItemFromCart({item: item})} 
                onKeyUp={() => props.removeItemFromCart({item: item})}
            >
                <Deleteicon
                    style={{width: '22px', height: '22px'}} />
            </div>
        </div>
    );
}
export default CartItem;