import React, { useCallback } from 'react';
import { array, bool, func, object, oneOf, shape, string } from 'prop-types';

import AddressForm from './addressForm';
import PaymentsForm from './paymentsForm';
import ShippingForm from './shippingForm';

/**
 * The EditableForm component renders the actual edit forms for the sections
 * within the form.
 */
const EditableForm = props => {
    const {
        editing,
        editOrder,
        submitShippingAddress,
        submitShippingMethod,
        submitting,
        isAddressInvalid,
        invalidAddressMessage,
        directory: { countries },
        paymentMethods,
        submitBillingAddress,
        submitPaymentMethod
    } = props;

    const handleCancel = useCallback(() => {
        editOrder(null);
    }, [editOrder]);

    const handleSubmitAddressForm = useCallback(
        formValues => {
            submitShippingAddress({
                formValues
            });
        },
        [submitShippingAddress]
    );

    /* const handleSubmitPaymentsForm = useCallback(
        formValues => {
            submitPaymentMethodAndBillingAddress({
                formValues
            });
        },
        [submitPaymentMethodAndBillingAddress]
    ); */

    const handleSubmitShippingForm = useCallback(
        formValues => {
            submitShippingMethod({
                formValues
            });
        },
        [submitShippingMethod]
    );

    const handleSubmitBillingForm = useCallback(
        formValues => {
            submitBillingAddress(formValues);
        },
        [submitBillingAddress]
    );

    const handleSubmitPaymentsForm = useCallback(
        formValues => {
            submitPaymentMethod(formValues);
        },
        [submitPaymentMethod]
    );

    switch (editing) {
        case 'address': {
            let { shippingAddress } = props;
            if (!shippingAddress) {
                shippingAddress = undefined;
            }

            return (
                <AddressForm
                    cancel={handleCancel}
                    countries={countries}
                    isAddressInvalid={isAddressInvalid}
                    invalidAddressMessage={invalidAddressMessage}
                    initialValues={shippingAddress}
                    submit={handleSubmitAddressForm}
                    submitting={submitting}
                />
            );
        }

        case 'billingAddress': {
            let { billingAddress } = props;
            if (!billingAddress) {
                billingAddress = undefined;
            }

            return (
                <AddressForm
                    cancel={handleCancel}
                    countries={countries}
                    isAddressInvalid={isAddressInvalid}
                    invalidAddressMessage={invalidAddressMessage}
                    initialValues={billingAddress}
                    submit={handleSubmitBillingForm}
                    submitting={submitting}
                    billingForm={true}
                />
            );
        }

        case 'paymentMethod': {
            let { paymentData } = props;
            if (!paymentData) {
                paymentData = undefined;
            }

            return (
                <PaymentsForm
                    cancel={handleCancel}
                    countries={countries}
                    initialValues={paymentData}
                    submit={handleSubmitPaymentsForm}
                    submitting={submitting}
                    paymentMethods={paymentMethods}
                />
            );
        }
        case 'shippingMethod': {
            const { availableShippingMethods, shippingMethod } = props;
            return (
                <ShippingForm
                    availableShippingMethods={availableShippingMethods}
                    cancel={handleCancel}
                    shippingMethod={shippingMethod}
                    submit={handleSubmitShippingForm}
                    submitting={submitting}
                />
            );
        }
        default: {
            return null;
        }
    }
};

EditableForm.propTypes = {
    availableShippingMethods: array,
    editing: oneOf(['address', 'billingAddress', 'paymentMethod', 'shippingMethod']),
    editOrder: func.isRequired,
    shippingAddress: object,
    shippingMethod: string,
    submitShippingAddress: func.isRequired,
    submitShippingMethod: func.isRequired,
    submitBillingAddress: func.isRequired,
    submitPaymentMethod: func.isRequired,
    submitting: bool,
    isAddressInvalid: bool,
    invalidAddressMessage: string,
    directory: shape({
        countries: array
    }),
    paymentMethods: array
};

export default EditableForm;
