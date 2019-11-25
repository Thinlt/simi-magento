import React from 'react';
import Image from 'src/simi/BaseComponents/Image';
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config';
import { Price } from '@magento/peregrine';

const OrderItems = (props) => {
    const { items, cartCurrencyCode } = props;

    return items && items.length ? items.map(o_item => {
        let itemsOption = '';
        let optionElement = ''
        if (o_item.options.length > 0) {
            itemsOption = o_item.options.map((optionObject) => {
                return (
                    <div key={Identify.randomString()} className="option-selected-item">
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
            <li key={Identify.randomString()} className='order-item'>
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
                    {optionElement}
                    <div className='item-qty-price'>
                        <span className='qty'>{Identify.__("Quantity")}: {o_item.qty}</span>
                        <span className='price'><Price currencyCode={cartCurrencyCode} value={o_item.price} /></span>
                    </div>
                </div>
            </li>
        );
    }) : null;

}

export default OrderItems;
