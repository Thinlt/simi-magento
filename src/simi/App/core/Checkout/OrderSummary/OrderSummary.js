import React from 'react';
import defaultClasses from './OrderSummary.css';
import Panel from 'src/simi/BaseComponents/Panel';
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config';
import { Price } from '@magento/peregrine';
import Arrow from 'src/simi/BaseComponents/Icon/Arrowup';
import Total from 'src/simi/BaseComponents/Total';
import isObjectEmpty from 'src/util/isObjectEmpty';
import AddressItem from 'src/simi/BaseComponents/Address';
import { logoUrl } from 'src/simi/Helper/Url';
import './style.scss';
const $ = window.$;

const OrderSummary = (props) => {

    const { cart, cartCurrencyCode, checkout, isPhone } = props;
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
                        <span className={defaultClasses['option-title']}>{optionObject.label}: </span>
                        <span className={defaultClasses['option-value']}>{optionObject.value}</span>
                    </div>
                );
            });

            optionElement = (
                <div className={defaultClasses['item-options']}>
                    <div className={defaultClasses['show-label']} onClick={(e) => handleToggleOption(e)}>
                        <span>{Identify.__('See details')}</span>
                        <Arrow className={'arrow-down'} />
                    </div>
                    <div className={'options-selected'} style={{ display: 'none' }}>
                        {itemsOption}
                    </div>
                </div>
            );
        }

        return (
            <li key={Identify.randomString()} className={defaultClasses['order-item']}>
                <div className={defaultClasses['item-image']} style={{ borderColor: configColor.image_border_color }}>
                    <img src={
                        o_item.image.file ?
                            resourceUrl(o_item.image.file, {
                                type: 'image-product',
                                width: 300
                            }) : logoUrl()
                    } alt={o_item.name} width={80} height={80}
                        style={{ objectFit: 'scale-down' }} />
                </div>
                <div className={defaultClasses['item-info']} style={{ width: '100%' }}>
                    <label className={defaultClasses['item-name']}>{o_item.name}</label>
                    <div className={defaultClasses['item-qty-price']}>
                        <span className={defaultClasses['qty']}>{Identify.__("Qty")}: {o_item.qty}</span>
                        <span className={defaultClasses['price']}><Price currencyCode={cartCurrencyCode} value={o_item.price} /></span>
                    </div>
                    {optionElement}
                </div>
            </li>
        );

    })

    const handleToggleItems = (e) => {
        const parent = $(e.currentTarget);
        parent.next('ul').slideToggle('fast');
        $(e.currentTarget).find('.expand_icon').toggleClass('rotate-180')
    }

    const handleToggleOption = (e) => {
        const parent = $(e.currentTarget);
        parent.next('.options-selected').slideToggle('fast');
        parent.children('.arrow-down').toggleClass('rotate-180');
    }

    const totalsSummary = (
        <Total classes={defaultClasses} data={cart.totals} currencyCode={cartCurrencyCode} />
    )

    const summaryItem = (
        <div className={defaultClasses['order-review-container']}>
            <div className={defaultClasses['order-review item-box']}>
                <div className={defaultClasses['order-items-header']} key={Identify.randomString()} id="order-items-header" onClick={(e) => handleToggleItems(e)}>
                    <div className={defaultClasses['item-count']}>
                        <span>{totalLabel} </span>
                        <Arrow className={'expand_icon'} />
                    </div>
                </div>
                <ul className={defaultClasses['items']}>
                    {orderItem}
                </ul>
            </div>
        </div>
    )

    const renderBlockShippingDetail = (
        <div className={defaultClasses['shipping-address-detail']}>
            <div className={defaultClasses['item-box']}>
                <div className={defaultClasses['block-header']}>
                    <span className={defaultClasses['title']}>{Identify.__('Ship To') + ":"}</span>
                </div>
                <AddressItem classes={defaultClasses} data={shippingAddress} />
            </div>
        </div>
    )

    const renderView = (
        <div className={defaultClasses['order-summary-content']}>
            {summaryItem}
            {shippingAddress && !isObjectEmpty(shippingAddress) && !is_virtual && renderBlockShippingDetail}
            {cart.totals && !isObjectEmpty(cart.totals) && totalsSummary}
        </div>
    )

    const containerSty = isPhone ? {marginTop: 35} : {};
    return <div className={defaultClasses['order-summary']} id="order-summary">
        <Panel title={<div className={defaultClasses['checkout-section-title']}>{Identify.__('Order Summary')}</div>}
            renderContent={renderView}
            isToggle={false}
            expanded={true}
            containerStyle={containerSty}
        />
    </div>
}

export default OrderSummary;
