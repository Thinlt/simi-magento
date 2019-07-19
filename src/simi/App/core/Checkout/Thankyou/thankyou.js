import React, { Fragment, useCallback, useEffect } from 'react';
import { bool, func, shape, string } from 'prop-types';
import classify, { mergeClasses } from 'src/classify';
import Button from 'src/components/Button';
import defaultClasses from './thankyou.css';
import { getOrderInformation } from 'src/selectors/checkoutReceipt';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import actions from 'src/actions/checkoutReceipt';
import { createAccount } from 'src/actions/checkout';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';

const Thankyou = props => {
    hideFogLoading()
    const { createAccount, history, reset, user } = props;

    const classes = mergeClasses(defaultClasses, props.classes);

    useEffect(() => reset, [reset]);

    const handleCreateAccount = useCallback(() => {
        createAccount(history);
    }, [createAccount, history]);

    /* const handleViewOrderDetails = useCallback(() => {
        // TODO: Implement/connect/redirect to order details page.

    }, []); */
    const handleViewOrderDetails = () => {
        let orderId = '/orderdetails.html/';
        history.push()
    }

    return (
        <div className="container" style={{marginTop: 40}}>
            <div className={classes.root}>
                <div className={classes.body}>
                    <h2 className={classes.header}>Thank you for your purchase!</h2>
                    <div className={classes.textBlock}>
                        You will receive an order confirmation email with order
                        status and other details.
                </div>
                    {user && user.isSignedIn ? (
                        <Fragment>
                            <div className={classes.textBlock}>
                                You can also visit your account page for more
                                information.
                        </div>
                            <Button onClick={handleViewOrderDetails}>
                                View Order Details
                        </Button>
                        </Fragment>
                    ) : (
                            <Fragment>
                                <hr />
                                <div className={classes.textBlock}>
                                    Track order status and earn rewards for your
                                    purchase by creating an account.
                        </div>
                                <Button priority="high" onClick={handleCreateAccount}>
                                    Create an Account
                        </Button>
                            </Fragment>
                        )}
                </div>
            </div>
        </div>
    );
};

Thankyou.propTypes = {
    classes: shape({
        body: string,
        footer: string,
        root: string
    }),
    order: shape({
        id: string
    }).isRequired,
    createAccount: func.isRequired,
    reset: func.isRequired,
    user: shape({
        isSignedIn: bool
    })
};

Thankyou.defaultProps = {
    order: {},
    reset: () => { },
    createAccount: () => { }
};

const { reset } = actions;

const mapStateToProps = state => ({
    order: getOrderInformation(state)
});

const mapDispatchToProps = {
    createAccount,
    reset
};

export default compose(
    classify(defaultClasses),
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(Thankyou);
