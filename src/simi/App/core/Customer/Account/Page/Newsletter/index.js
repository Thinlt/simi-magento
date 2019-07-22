import React from 'react';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Loading from "src/simi/BaseComponents/Loading";
import { connect } from 'src/drivers';
import { Simiquery, SimiMutation } from 'src/simi/Network/Query'
import CUSTOMER_NEWSLETTER from 'src/simi/queries/customerNewsletter.graphql';
import CUSTOMER_NEWSLETTER_UPDATE from 'src/simi/queries/customerNewsletterUpdate.graphql';

class Newsletter extends React.Component {

    constructor(props) {
        super(props);
    }

    render() {
        const {user, classes} = this.props;
        TitleHelper.renderMetaHeader({title:Identify.__('Newsletter')})
        return (
            <Simiquery query={CUSTOMER_NEWSLETTER}>
                {({ loading, error, data }) => {
                    if (error) return <div>Data Fetch Error</div>;
                    if (loading) return <Loading />;
                    const { customer } = data;
                    const { is_subscribed } = customer;
                    let clicked = false;
                    return (
                        <SimiMutation mutation={CUSTOMER_NEWSLETTER_UPDATE}>
                            {(updateCustomer, { data }) => {
                                return (
                                <>
                                    <div className={classes["account-newsletter"]}>
                                        <input id="checkbox-subscribe" type="checkbox" onChange={(e)=> {
                                            if (!user.email) return false;
                                            clicked = true;
                                            let isSubscribed = e.target.checked ? true : false;
                                            updateCustomer({ variables: { email: user.email, isSubscribed: isSubscribed } });
                                        }}
                                        checked={is_subscribed} value={1} />
                                        <label htmlFor="checkbox-subscribe">&nbsp;{Identify.__('General Subscription')}</label>
                                    </div>
                                    {(data === undefined && clicked) && <Loading />}
                                </>
                                )
                            }}
                        </SimiMutation>
                    );
                }}
            </Simiquery>
        );
    }
}

const mapStateToProps = ({ user }) => {
    const { currentUser } = user
    return {
        user: currentUser
    };
}

export default connect(
    mapStateToProps
)(Newsletter);