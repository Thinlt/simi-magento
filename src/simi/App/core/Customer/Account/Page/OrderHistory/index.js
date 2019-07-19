import React, { useEffect } from 'react';
import OrderHistory from 'src/simi/App/core/Customer/Account/Components/Orders/OrderList';
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import Identify from "src/simi/Helper/Identify";
import { Link } from 'react-router-dom'
import Loading from "src/simi/BaseComponents/Loading";
import { simiUseQuery } from 'src/simi/Network/Query'
import getCustomerInfoQuery from 'src/simi/queries/getCustomerInfo.graphql'

const MyOrder = props => {
    const { classes, isPhone } = props;
    const [queryResult, queryApi] = simiUseQuery(getCustomerInfoQuery, false);
    const { data } = queryResult
    const { runQuery } = queryApi;

    const getCustomerInfo = () => {
        runQuery({});
    }

    useEffect(() => {
        if (!data) {
            getCustomerInfo();
        }
    }, [data])

    if (!data) {
        return <Loading />
    }

    return (
        <div className={classes['account-my-orders-history']}>
            <div className={classes["customer-page-title"]}>
                <div className={classes["customer-page-title"]}>
                    {Identify.__("My Orders")}
                </div>
                <div className={classes['account-my-orders']}>
                    <OrderHistory classes={classes} data={data} showForDashboard={false} />
                </div>
            </div>
        </div>
    )
}

export default MyOrder;