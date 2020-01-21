/* eslint-disable prefer-const */
import React, { useState, useEffect } from "react";
import Identify from "src/simi/Helper/Identify";
import { formatPrice } from "src/simi/Helper/Pricing";
import Loading from "src/simi/BaseComponents/Loading";
import ReactHTMLParse from "react-html-parser";
import { getOrderDetail } from 'src/simi/Model/Orders';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';

require('./orderDetails.scss')

const Detail = (props) => {
    const [data, setData] = useState(null)
    console.log(props)
    console.log(data)
    const [loaded, setLoaded] = useState(false)
    const { history } = props
    const isPhone = window.innerWidth < 1024 
    const id = history.location.state.orderData.increment_id || null;
    const storeConfig = Identify.getStoreConfig();

    console.log(storeConfig)
    useEffect(() => {
        const api = Identify.ApiDataStorage('quoteOrder') || {}
        if (api.hasOwnProperty(id)) {
            const data = api[id]
            setData(data)
            setLoaded(true)
        }
        if (!data && !loaded && id) {
            getOrderDetail(id, processData)
        }
    }, [])

    const processData = (data) => {
        let dataArr = {}
        const key = id;
        let dataOrder = data.order;
        setData(dataOrder)
        dataArr[key] = dataOrder;
        Identify.ApiDataStorage("quoteOrder", 'update', dataArr);
    }

    const handleLink = (url) => {
        history.push(url)
    }

    const getDateFormat = dateData => {
        const date = new Date(dateData);
        const day = date.getDate();
        const month =
            date.getMonth() + 1 < 10
                ? "0" + (date.getMonth() + 1)
                : date.getMonth() + 1;
        const year = date.getFullYear();

        return day + "/" + month + "/" + year;
    };

    const getFormatPrice = value => {
        if (data && data.order_currency_code) {
            return formatPrice(Number(value), data.order_currency_code)
        }
    }

    const renderAddress = (address, label) => {
        return (
            <div className="detail-col">
                <div className="line-num">
                    <div className="checkout-address-label">{label}</div>
                    <div className="address green">
                        <span style={{display: 'block'}}>
                            {address.firstname}  {address.lastname}
                        </span>
                        {address.street && (
                            <span style={{ display: "block" }}>
                                {ReactHTMLParse(
                                    address.street
                                )}
                            </span>
                        )}
                        {address.postcode && (
                            <span style={{ display: "block" }}>
                                {address.postcode}
                            </span>
                        )}
                        {address.city && (
                            <span style={{ display: "block" }}>
                                {address.city}
                            </span>
                        )}
                        {address.telephone && (
                            <span style={{ display: "block" }}>
                                {address.telephone}
                            </span>
                        )}
                    </div>
                </div>
            </div>
        )
    }
    
    const renderMethod = (label, value) => {
        return <div className="checkout-method">
            <div className="checkout-method-label">{label}</div>
            <div className="checkout-method-value">{value}</div>
        </div>

    }

    const renderSummary = () => {
        let html = null;
        if (data) {
            const colSize = isPhone?'col-xs-6':'col-md-3'
            html = (
                <div className="order-detail__summary">
                    <div className="summary-title">
                        {Identify.__('Order Information')}
                    </div>
                    <div className="summary-row rows">
                        {(data.shipping_address && Object.keys(data.shipping_address).length > 0) && <div className={colSize}>{renderAddress(data.shipping_address, Identify.__("Delivery Address"))}</div>}
                        {data.shipping_method && <div className={colSize}>{renderMethod(Identify.__('Shipping Method'), data.shipping_method)}</div>}
                        {(data.billing_address && Object.keys(data.billing_address).length > 0) &&  <div className={colSize}>{renderAddress(data.billing_address, Identify.__("Billing Address"))}</div>}
                        {data.payment_method && <div className={colSize}>{renderMethod(Identify.__('Payment Method'), data.payment_method)}</div>}
                    </div>
                </div>
            );
        }
        return html;
    };

    const renderItem = items => {
        let html = null;
        const totalPrice = data.total;

        if (items.length > 0) {
            html = items.map((item, index) => {
                let optionText = [];
                if (item.product_options && item.product_options.attributes_info) {
                    let options = item.product_options.attributes_info;
                    for (let i in options) {
                        let option = options[i];
                        optionText.push(
                            <div key={i}>
                                <div className="orderhisoptionlabel">{option.label}:</div> <div className="orderhisoptionvalue">{ReactHTMLParse(option.value)}</div>
                            </div>
                        );
                    }
                }

                const location = `/product.html?sku=${item.simi_sku?item.simi_sku:item.sku}`
                let vendorName = '';
                
                if (item.vendor_id !== 'default' && storeConfig) {
                    try {
                        const vendorList = storeConfig.simiStoreConfig.config.vendor_list;
                        const vendor = vendorList.find(vendor => {
                            return vendor.entity_id === item.vendor_id
                        })
                        if (vendor && vendor.firstname) vendorName = `${vendor.firstname}`;
                        if (vendor && vendor.lastname) vendorName = `${vendorName} ${vendor.lastname}`;
                        const {profile} = vendor || {}
                        vendorName = profile && profile.store_name || vendorName;
                    } catch (err) { }
                }
                

                return (
                    <div className="order-detail-line" key={index}>
                        <div className="detail-order__col img-item">
                            <div
                                to={location}
                                className="img-name-col"
                            >
                                <div className="order-item-info">
                                    <div
                                        className="des-order"
                                        style={{}}
                                    >
                                        <div className="item-name" role="presentation" onClick={()=>handleLink(location)}>
                                            {isPhone && <b>{Identify.__('Product name')}</b>}
                                            {ReactHTMLParse(item.name)}
                                        </div>
                                        {vendorName && <div className="vendorName">{isPhone && <b>{Identify.__('Designer name')}</b>} {vendorName}</div>}
                                        <div className="item-options">
                                            {(optionText.length > 0) && optionText}
                                            <div>
                                                <div className="orderhisoptionlabel">{Identify.__('Service Support:')}</div>  <div className="orderhisoptionvalue">{(item && parseInt(item.is_buy_service)) ? Identify.__('Yes') : Identify.__('No')}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="detail-order__col product-code">
                            {isPhone && <b>{Identify.__('SKU')}</b>}
                            <div className="cart-item-value">{item.sku}</div>
                        </div>
                        <div className="detail-order__col">
                            {isPhone && <b>{Identify.__('Unit Price')}</b>}
                            <div
                                className="cart-item-value price"
                                style={{}}
                            >
                                {
                                    totalPrice.tax ? getFormatPrice(item.price_incl_tax) : getFormatPrice(item.price)
                                }
                            </div>
                        </div>
                        <div className="detail-order__col item-qty">
                            {isPhone && <b>{Identify.__('Quantity')}</b>}
                            <div className="cart-item-value item-quantity-val">{parseInt(item.qty_ordered, 10)}</div>
                        </div>
                        <div className="detail-order__col">
                            {isPhone && <b>{Identify.__('Total Price')}</b>}
                            <div
                                className="cart-item-value price"
                                style={{}}
                            >
                                {
                                    totalPrice.tax
                                        ? getFormatPrice(item.row_total_incl_tax)
                                        : getFormatPrice(item.row_total)
                                }
                            </div>
                        </div>
                    </div>
                );
            });
        }
        return html;
    };

    const renderTableItems = () => {
        let html = null;
        if (data) {
            html = (
                <div className="order-detail-table">
                    {!isPhone && (
                        <div className="order-header">
                            <div className="detail-order__col detail-name-col">
                                {Identify.__("Product Name")}
                            </div>
                            <div className="detail-order__col detail-sku-col">
                                {Identify.__("SKU")}
                            </div>
                            <div className="detail-order__col">
                                {Identify.__("Price")}
                            </div>
                            <div className="detail-order__col">
                                {Identify.__("Qty")}
                            </div>
                            <div className="detail-order__col">
                                {Identify.__("Subtotal")}
                            </div>
                        </div>
                    )}
                    <div className="order-body">
                        {data.order_items.length > 0
                            ? renderItem(data.order_items)
                            : Identify.__("No product found!")}
                    </div>
                    {renderTotal()}
                </div>
            );
        }
        return (
            <div className="orderTableContainer">
                {html}
            </div>
        )
    };

    const renderTotal = () => {
        const totalPrice = data.total;

        return (
            <div className="detail-order-footer">
                <div className="box-total-price">
                    {totalPrice && <div className="total-sub-price-container">
                        <div className="summary-price-line">
                            <span className="bold">{Identify.__('Subtotal')}</span>
                            <span className="price">{totalPrice.tax ? getFormatPrice(totalPrice.subtotal_incl_tax) : getFormatPrice(totalPrice.subtotal_excl_tax)}</span>
                        </div>
                        <div className="summary-price-line">
                            <span className="bold">{Identify.__('Delivery')}</span>
                            <span className="price">{totalPrice.tax ? getFormatPrice(totalPrice.shipping_hand_incl_tax) : getFormatPrice(totalPrice.shipping_hand_excl_tax)}</span>
                        </div>
                        <div className="summary-price-line">
                            <span className="bold">{Identify.__('VAT')}</span>
                            <span className="price">{getFormatPrice(totalPrice.tax)}</span>
                        </div>
                        {
                                parseInt(data.service_support_fee) ? 
                                <div className="summary-price-line">
                                    <span className="bold">{Identify.__('Service Support Fee')}</span>
                                    <span className="price">{getFormatPrice(data.service_support_fee)}</span>
                                </div> : ''
                        }
                        {
                                parseInt(data.preorder_deposit_discount) ? 
                                <div className="summary-price-line">
                                    <span className="bold">{Identify.__('Pre-order Deposit Discount')}</span>
                                    <span className="price">{getFormatPrice(data.preorder_deposit_discount)}</span>
                                </div> : ''
                        }
                        <div className="summary-price-line total">
                            <span className="bold">{Identify.__('Grand Total')}</span>
                            <span className="price">{totalPrice.tax ? getFormatPrice(totalPrice.grand_total_incl_tax) : getFormatPrice(totalPrice.shipping_hand_excl_tax)}</span>
                        </div>
                    </div>}
                </div>
            </div>
        )
    }

    if (!data) {
        return <Loading />;
    }

    return (
        <div className="dashboard-acc-order-detail">
            <div className="customer-page-title">
                <div className="order-id">{Identify.__("Order #")} {data.increment_id}</div>
                <div className="created-at">{data.status} {getDateFormat(data.created_at)}</div>
                
            </div>
            {renderTableItems()}
            {renderSummary()}
        </div>
    );
}

const mapDispatchToProps = {
    toggleMessages,
}

export default connect(
    null,
    mapDispatchToProps
)(Detail);
