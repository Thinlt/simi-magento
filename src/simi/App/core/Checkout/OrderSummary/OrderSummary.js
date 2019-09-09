import React from 'react';
import Panel from 'src/simi/BaseComponents/Panel';
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config';
import { Price } from '@magento/peregrine';
import Arrow from 'src/simi/BaseComponents/Icon/Arrowup';
import Total from 'src/simi/BaseComponents/Total';
import isObjectEmpty from 'src/util/isObjectEmpty';
import AddressItem from 'src/simi/BaseComponents/Address';
import { resourceUrl, logoUrl } from 'src/simi/Helper/Url';
import Image from 'src/simi/BaseComponents/Image';

require('./OrderSummary.scss')

const $ = window.$;

const OrderSummary = (props) => {

    const { cart, cartCurrencyCode, checkout, panelClassName } = props;
    const { details } = cart;
    const { shippingAddress } = checkout;

    const totalLabel = details && details.hasOwnProperty('items_count') && details.items_count + Identify.__(' items in cart');

    const { is_virtual } = details;

    const orderItem = details && details.items && details.items.map(o_item => {
        let itemsOption = '';
        let optionElement = ''
        if (o_item.options.length > 0) {
            itemsOption = o_item.options.map((optionObject) => {
                return (
                    <div key={Identify.randomString()}>
                        <span className='option-title'>{optionObject.label}: </span>
                        <span className='option-value'>{optionObject.value}</span>
                    </div>
                );
            });

            optionElement = (
                <div className='item-options'>
                    <div className='show-label' onClick={(e) => handleToggleOption(e)} role="presentation">
                        <span>{Identify.__('See details')}</span>
                        <Arrow className='arrow-down' />
                    </div>
                    <div className={'options-selected'} style={{ display: 'none' }}>
                        {itemsOption}
                    </div>
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
                <div className='item-info' style={{ width: '100%' }}>
                    <label className='item-name'>{o_item.name}</label>
                    <div className='item-qty-price'>
                        <span className='qty'>{Identify.__("Qty")}: {o_item.qty}</span>
                        <span className='price'><Price currencyCode={cartCurrencyCode} value={o_item.price} /></span>
                    </div>
                    {optionElement}
                </div>
            </li>
        );

    })

    const handleToggleItems = (e) => {
        const parent = $(e.currentTarget);
        parent.next('ul').slideToggle('fast');
        parent.find('.expand_icon').toggleClass('rotate-180')
    }

    const handleToggleOption = (e) => {
        const parent = $(e.currentTarget);
        parent.next('.options-selected').slideToggle('fast');
        parent.find('svg').toggleClass('rotate-0');
    }

    const totalsSummary = (
        <Total data={cart.totals} currencyCode={cartCurrencyCode} />
    )

    const summaryItem = (
        <div className='order-review-container'>
            <div className='order-review item-box'>
                <div className='order-items-header' key={Identify.randomString()} id="order-items-header" onClick={(e) => handleToggleItems(e)} role="presentation">
                    <div className='item-count'>
                        <span>{totalLabel} </span>
                        <Arrow className={'expand_icon'} />
                    </div>
                </div>
                <ul className='items'>
                    {orderItem}
                </ul>
            </div>
        </div>
    )

    const renderBlockShippingDetail = (
        <div className='shipping-address-detail'>
            <div className='item-box'>
                <div className='block-header'>
                    <span className='title'>{Identify.__('Ship To') + ":"}</span>
                </div>
                <AddressItem data={shippingAddress} />
            </div>
        </div>
    )

    const renderView = (
        <div className='order-summary-content'>
            {summaryItem}
            {shippingAddress && !isObjectEmpty(shippingAddress) && !is_virtual && renderBlockShippingDetail}
            {cart.totals && !isObjectEmpty(cart.totals) && totalsSummary}
        </div>
    )

    return <div className='order-summary' id="order-summary">
        <Panel title={<div className='checkout-section-title'>{Identify.__('Order Summary')}</div>}
            className={panelClassName}
            renderContent={renderView}
            isToggle={false}
            expanded={true}
        />
    </div>
}

export default OrderSummary;
