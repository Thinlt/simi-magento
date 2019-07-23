import React, { useCallback, Fragment } from 'react';
import { useFormState, asField, BasicRadioGroup } from 'informed';
import { array, bool, func, shape, string } from 'prop-types';

import Button from 'src/components/Button';
import Radio from 'src/components/RadioGroup/radio';
import defaultClasses from './paymentsFormItems.css';
import isObjectEmpty from 'src/util/isObjectEmpty';
import Identify from 'src/simi/Helper/Identify';
import BraintreeDropin from './paymentMethods/braintreeDropin';
import CCType from './paymentMethods/ccType';

/**
 * This component is meant to be nested within an `informed` form. It utilizes
 * form state to do conditional rendering and submission.
 */
const CustomRadioPayment = asField(({ fieldState, ...props }) => (
    <BasicRadioGroup {...props} fieldState={fieldState} />
));

const PaymentsFormItems = props => {
    const {
        classes,
        setIsSubmitting,
        submit,
        isSubmitting,
        paymentMethods,
        initialValues,
        paymentCode
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
    } else {
        selectablePaymentMethods = []
    }

    const ccPayment = { value: 'cc_type', label: 'Credit card' }
    selectablePaymentMethods.push(ccPayment);

    let thisInitialValue = null;
    if (initialValues && !isObjectEmpty(initialValues)) {
        if (initialValues.value) {
            thisInitialValue = initialValues.value;
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
        if (p_method === 'checkmo'
            || p_method === 'free'
            || p_method === 'cashondelivery'
            || p_method === 'banktransfer') {
            // payment type 0

            parseData = selectablePaymentMethods.find(
                ({ value }) => value === p_method
            );

            handleSuccess(parseData)
        }
    }

    const handleSubmit = useCallback(() => {
        setIsSubmitting(true);
    }, [setIsSubmitting]);

    const renderMethod = () => {
        let mt = null;
        if (selectablePaymentMethods.length) {
            mt = selectablePaymentMethods.map(ite => {

                let frameCard = '';
                // label with option have card
                if (ite.value === 'braintree' && formState.values['payment_method'] === ite.value) {
                    frameCard = <Fragment>
                        <BraintreeDropin shouldRequestPaymentNonce={isSubmitting} onError={handleError} onSuccess={handleSuccess} />
                        <Button
                            className={classes.button}
                            style={{ marginTop: 10, marginBottom: 20 }}
                            type="button"
                            onClick={() => handleSubmit()}
                        >{Identify.__('Use Card')}</Button>
                    </Fragment>
                }

                if (ite.value === 'cc_type' && formState.values['payment_method'] === ite.value) {
                    frameCard = <CCType />
                }

                return <Fragment key={ite.value}>
                    <Radio label={ite.label} value={ite.value} />
                    {frameCard}
                </Fragment>
            });
        }
        return mt;
    }

    return (
        <Fragment>
            <div className={classes.body}>
                <div className={defaultClasses['payment-method-item']}>
                    <CustomRadioPayment initialValue={paymentCode} field="payment_method" key={thisInitialValue} onChange={() => selectPaymentMethod()}>
                        {renderMethod()}
                    </CustomRadioPayment>
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
