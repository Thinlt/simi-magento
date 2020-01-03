import React from 'react';
import Image from 'src/simi/BaseComponents/Image';
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config';
import { Price } from '@magento/peregrine';
import { isArray } from 'util';
import Childproducts from './Childproducts'

const OrderItems = (props) => {
    const { cartCurrencyCode, totals, is_pre_order, is_try_to_buy } = props;
    let items = []
    if (totals && totals.items)
        items = totals.items
    return items && items.length ? items.map((o_item, o_index) => {
        let itemsOption = '';
        let optionElement = ''
        o_item.options = (o_item.options)?(isArray(o_item.options)?o_item.options:JSON.parse(o_item.options)):[]
        
        if (o_item.simi_pre_order_option) {
            optionElement = <Childproducts childProducts={o_item.simi_pre_order_option} cartCurrencyCode={cartCurrencyCode} />
        } else if (o_item.simi_trytobuy_option) {
            optionElement = <Childproducts childProducts={o_item.simi_trytobuy_option} cartCurrencyCode={cartCurrencyCode} />
        } else if (o_item.options.length > 0) {
            itemsOption = o_item.options.map((optionObject, optionObjectindex) => {
                return (
                    <div key={optionObjectindex} className="option-selected-item">
                        <span className='option-title'>{optionObject.label}: </span>
                        <span className='option-value'>{optionObject.value}</span>
                    </div>
                );
            });

            optionElement = (
                <div className='option-selected'>
                    {itemsOption}
                </div>
            );
        }
        const image = (o_item.image && o_item.image.file) ? o_item.image.file : o_item.simi_image

        return (
            <li key={o_index} className='order-item'>
                <div className='item-image' style={{ borderColor: configColor.image_border_color }}>
                    <Image
                        src={
                            image ?
                                resourceUrl(image, {
                                    type: 'image-product',
                                    width: 80
                                }) :
                                logoUrl()
                        }
                        alt={o_item.name} />
                </div>
                <div className='item-info'>
                    <label className='item-name'>{o_item.name}</label>
                    {
                        (!is_pre_order && !is_try_to_buy) &&
                        <React.Fragment>
                            {optionElement}
                            <div className='item-qty-price'>
                                <span className='qty'>{Identify.__("Quantity")}: {o_item.qty}</span>
                                <span className='price'><Price currencyCode={cartCurrencyCode} value={o_item.price} /></span>
                            </div>
                        </React.Fragment>
                    }
                </div>
                {(is_pre_order || is_try_to_buy) && optionElement}
            </li>
        );
    }) : null;

}

export default OrderItems;
