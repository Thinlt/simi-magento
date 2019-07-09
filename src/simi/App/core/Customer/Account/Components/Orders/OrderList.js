import React, { useState, useEffect } from 'react';
import Loading from "src/simi/BaseComponents/Loading";
import Identify from 'src/simi/Helper/Identify'
import PaginationTable from './PaginationTable';
import { Link } from 'react-router-dom';

const OrderList = props => {
    const {classes, history, showForDashboard ,data} = props
    const cols = 
        [
            { title: Identify.__("Order #"), width: "14.02%" },
            { title: Identify.__("Date"), width: "15.67%" },
            // { title: Identify.__("Ship to"), width: "33.40%" },
            { title: Identify.__("Total"), width: "12.06%" },
            { title: Identify.__("Status"), width: "12.58%" },
            { title: Identify.__(""), width: "12.27%" }
        ];
    const limit = 15;
    const currentPage= 1;

    const renderOrderItem = (item, index) => {
        let date = Date.parse(item.created_at);
        date = new Date(date);
        let m = date.getMonth() + 1;
        m = m < 10 ? "0" + m : m;
        date = date.getDate() + "/" + m + "/" + date.getFullYear();
        const location = {
            pathname: "/orderdetails.html/" + item.increment_id,
            state: {orderData: item}
        };
        return (
            <tr key={index}>
                <td data-title={Identify.__("Order #")}>
                    {Identify.__(item.increment_id)}
                </td>
                <td
                    data-title={Identify.__("Date")}
                >
                    {date}
                </td>
                {/* <td data-title={Identify.__("Ship to")}>{`${
                    item.customer_firstname
                }  ${item.customer_lastname}`}</td> */}
                <td data-title={Identify.__("Total")}>
                    {Identify.formatPrice(item.grand_total)}
                </td>
                <td className="order-status" data-title={Identify.__("Status")}>
                    {item.status}
                </td>
                <td data-title="">
                    <Link className={classes["view-order"]} to={location}>{Identify.__('View order')}</Link>
                </td>
            </tr>
        )
    }

    return (
        <div className={classes['customer-recent-orders']}>
            {!data || !data.hasOwnProperty('customerOrders') || data.customerOrders.items.length === 0
                ? (
                    <div className="text-center">
                        {Identify.__("You have no items in your order")}
                    </div>
                ) : (
                    <PaginationTable 
                        renderItem={renderOrderItem}
                        data={showForDashboard ? data.customerOrders.items.slice(0, 3) : data.customerOrders.items}
                        cols={cols}
                        showPageNumber={!showForDashboard}
                        limit={limit}
                        currentPage={currentPage}
                        classes={classes}
                    />
                )
            } 
        </div>
    )
}

export default OrderList;