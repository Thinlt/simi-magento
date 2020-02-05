import React, { useState, useEffect } from 'react';
import OrderHistory from '../../Components/Orders/OrderList';
import Identify from "src/simi/Helper/Identify";
import Loading from "src/simi/BaseComponents/Loading";
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getAllOrders } from 'src/simi/Model/Orders'

const MyOrder = props => {
    const [data, setData] = useState(null)
    if (!data) {
        getAllOrders((data) => setData(data))
    }
    if (!data || !data.orders) {
        return <Loading />
    }
    

    return (
        <div className='account-my-orders-history'>
            {TitleHelper.renderMetaHeader({
                title: Identify.__('My Orders'),
                desc: Identify.__('My Orders') 
            })}
            <div className="customer-page">
                <div className="customer-page-title">
                    {Identify.__("My Orders")}
                </div>
                <div className='account-my-orders'>
                    <OrderHistory data={data.orders} showForDashboard={false} isPhone={props.isPhone}/>
                </div>
            </div>
        </div>
    )
}

export default MyOrder;