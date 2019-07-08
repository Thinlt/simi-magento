import React, { useEffect, useState } from 'react';
import Identify from "src/simi/Helper/Identify";
import {Link} from 'react-router-dom'
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import { Query } from 'src/drivers';
import { SimiMutation } from 'src/simi/Network/Query'
import Loading from "src/simi/BaseComponents/Loading";
import CUSTOMER_NEWSLETTER from 'src/simi/queries/customerNewsletter.graphql';

const Dashboard = props => {
    const {classes, history, isPhone, customer} = props;
    return (
        <div className={classes['my-dashboard']}>
            {!isPhone ? (
                    <div className={classes["dashboard-recent-orders"]}>
                        <div className={classes["customer-page-title"]}>
                            {Identify.__("Recent Orders")}
                            <Link className={classes["view-all"]} to='/orderhistory.html'>{Identify.__("View all")}</Link>
                        </div>
                        {/* <OrderHistory parent={this} showForDashboard={true} /> */}
                    </div>
                ) : (
                    <Link to="/orderhistory.html">
                        <Whitebtn
                            text={Identify.__("View recent orders")}
                            className={classes["view-recent-orders"]}
                        />
                    </Link>
                    
            )}
            <div className={classes['dashboard-acc-information']}>
                <div className={classes['customer-page-title']}>
                    {Identify.__("Account Information")}
                </div>
                <div className={classes["acc-information"]} >
                    <div className={classes["dash-column-box"]}>
                        <div className={classes["white-box-content"]}>
                            <div className={classes["box-title"]}>
                                {Identify.__("Contact information")}
                            </div>
                            <p className={classes["desc"]}>{customer.email}</p>
                            <Link className={classes["edit-link"]} to={{ pathname: '/profile.html', state: {profile_edit: 'password'} }}>{Identify.__("Change password")}</Link>
                        </div>
                        <Link to="/profile.html">
                            <Whitebtn
                                text={Identify.__("Edit")}
                                className={classes["edit-information"]}
                            />
                        </Link>
                        
                    </div>
                    <div className={classes["dash-column-box"]}>
                        {customer.extension_attributes && customer.extension_attributes.hasOwnProperty('is_subscribed') ? (
                            <div className={classes["white-box-content"]}>
                                <div className={classes["box-title"]}>
                                    {Identify.__("Newsletter")}
                                </div>
                                <p className={classes["desc"]}>
                                    {customer.extension_attributes.is_subscribed === true
                                        ? Identify.__(
                                            "You are subscribed to our newsletter"
                                        )
                                        : Identify.__(
                                            "You are not subscribed to our newsletter"
                                        )}
                                </p>
                            </div>
                        ) : <Loading /> }
                        <Link to="/newsletter.html">
                            <Whitebtn
                                text={Identify.__("Edit")}
                                className={classes["edit-information"]} 
                            />            
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    )
    
}

export default Dashboard;