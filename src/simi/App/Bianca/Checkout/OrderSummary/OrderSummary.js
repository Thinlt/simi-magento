import React, {useMemo} from 'react';
import Panel from 'src/simi/BaseComponents/Panel';
import Identify from 'src/simi/Helper/Identify';
import Arrow from 'src/simi/BaseComponents/Icon/Arrowup';
import Total from 'src/simi/BaseComponents/Total';
import isObjectEmpty from 'src/util/isObjectEmpty';
import OrderItems from './OrderItems';

require('./OrderSummary.scss')
const $ = window.$;

const OrderSummary = (props) => {

    const { cart, cartCurrencyCode, panelClassName, btnPlaceOrder } = props;
    const { details } = cart;

    const totalLabel = details && details.hasOwnProperty('items_count') && details.items_count + Identify.__(' items in cart');

    const orderItem = useMemo(() => details && details.items && <OrderItems items={details.items} cartCurrencyCode={cartCurrencyCode} />, [details.items]);

    const handleToggleItems = (e) => {
        const parent = $(e.currentTarget);
        parent.next('ul').slideToggle('fast');
        parent.find('.expand_icon').toggleClass('rotate-180')
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

    const renderView = (
        <div className='order-summary-content'>
            {summaryItem}
            {cart.totals && !isObjectEmpty(cart.totals) && totalsSummary}
            {btnPlaceOrder}
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
