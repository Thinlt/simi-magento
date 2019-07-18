import React, { useCallback, Fragment } from 'react';
import { useFormState } from 'informed';
import { array, bool, func, shape, string } from 'prop-types';

import BraintreeDropin from './paymentMethods/braintreeDropin';
import Button from 'src/components/Button';
import RadioGroup from 'src/components/RadioGroup';
import defaultClasses from './paymentsFormItems.css';
import isObjectEmpty from 'src/util/isObjectEmpty';

/**
 * This component is meant to be nested within an `informed` form. It utilizes
 * form state to do conditional rendering and submission.
 */
const PaymentsFormItems = props => {
    const {
        classes,
        setIsSubmitting,
        submit,
        submitting,
        paymentMethods,
        initialValues
    } = props;

    // Currently form state toggles dirty from false to true because of how
    // informed is implemented. This effectively causes this child components
    // to re-render multiple times. Keep tabs on the following issue:
    //   https://github.com/joepuzzo/informed/issues/138
    // If they resolve it or we move away from informed we can probably get some
    // extra performance.
    const formState = useFormState();

    const handleError = useCallback(() => {
        setIsSubmitting(false);
    }, [setIsSubmitting]);


    let selectablePaymentMethods;

    if (paymentMethods && paymentMethods.length) {
        selectablePaymentMethods = paymentMethods.map(
            ({ code, title }) => ({
                label: title,
                value: code
            })
        );
    }else{
        selectablePaymentMethods =[]
    }

    let thisInitialValue = null;
    if(initialValues && !isObjectEmpty(initialValues)){
        if (initialValues.value){
            thisInitialValue = initialValues.value;
            // thisInitialValue.title = initialValues.label;
        }
    }

    // The success callback. Unfortunately since form state is created first and
    // then modified when using initialValues any component who uses this
    // callback will be rendered multiple times on first render. See above
    // comments for more info.
    const handleSuccess = useCallback(
        value => {
            setIsSubmitting(false);
            submit({
                code: formState.values['payment_method'],
                data: value
            });
        },
        [setIsSubmitting, submit]
    );

    const selectPaymentMethod = () => {

        const p_method = formState.values['payment_method'];
        let parseData = {};
        if (p_method === 'checkmo' || p_method === 'free'){
            // save data to reducer with standard payment method

            parseData = selectablePaymentMethods.find(
                ({ value }) => value === p_method
            );

        }
        handleSuccess(parseData)
    }

    return (
        <Fragment>
            <div className={classes.body}>
                {/* <h2 className={classes.heading}>{Identify.__('Payment Method')}</h2> */}
                {/* <div className={classes.braintree}>
                    <BraintreeDropin
                        shouldRequestPaymentNonce={isSubmitting}
                        onError={handleError}
                        onSuccess={handleSuccess}
                    />
                </div> */}
                <div className={defaultClasses['payment-method-item']}>
                    <RadioGroup field="payment_method" initialValue={thisInitialValue} items={selectablePaymentMethods} onChange={()=> selectPaymentMethod()} />
                </div>

            </div>

        </Fragment>
    );
};

PaymentsFormItems.propTypes = {
    cancel: func.isRequired,
    classes: shape({
        address_check: string,
        body: string,
        button: string,
        braintree: string,
        city: string,
        footer: string,
        heading: string,
        postcode: string,
        region_code: string,
        street0: string
    }),
    countries: array,
    isSubmitting: bool,
    setIsSubmitting: func.isRequired,
    submit: func.isRequired,
    submitting: bool
};

export default PaymentsFormItems;
