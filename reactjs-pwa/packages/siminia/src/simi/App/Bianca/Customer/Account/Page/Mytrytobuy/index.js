import React, { useState } from 'react';
import OrderHistory from 'src/simi/App/Bianca/Customer/Account/Components/Orders/OrderList';
import Identify from "src/simi/Helper/Identify";
import Loading from "src/simi/BaseComponents/Loading";
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getTryToBuyOrders } from 'src/simi/Model/Orders'

const Mytrytobuy = props => {
    const [data, setData] = useState(null)
    if (!data) {
        getTryToBuyOrders((data) => setData(data))
    }
    if (!data || !data.mytrytobuys) {
        return <Loading />
    }
    
    return (
        <div className='account-my-orders-history'>
            {TitleHelper.renderMetaHeader({
                title: Identify.__('My Try & Buy Products'),
                desc: Identify.__('My Try & Buy Products') 
            })}
            <div className="customer-page">
                <div className="customer-page-title">
                    {Identify.__("My Try & Buy Products")}
                </div>
                <div className='account-my-orders'>
                    <OrderHistory data={data.mytrytobuys} showForDashboard={false} isPhone={props.isPhone} />
                </div>
            </div>
        </div>
    )
}
export default Mytrytobuy