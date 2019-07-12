import React, { useCallback, useState, Fragment } from 'react';
import { array, bool, shape, string } from 'prop-types';

import { mergeClasses } from 'src/classify';
import defaultClasses from './billingForm.css';
import isObjectEmpty from 'src/util/isObjectEmpty';
import AddressForm from './addressForm';
import Checkbox from 'src/components/Checkbox';

const DEFAULT_FORM_VALUES = {
    addresses_same: true
};

/**
 * A wrapper around the payment form. This component's purpose is to maintain
 * the submission state as well as prepare/set initial values.
 */
const BillingForm = props => {
    const { initialValues } = props;
    const classes = mergeClasses(defaultClasses, props.classes);

    const billingForm = true;

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = useCallback(() => {
        setIsSubmitting(true);
    }, [setIsSubmitting]);

    let initialFormValues;
    if (isObjectEmpty(initialValues)) {
        initialFormValues = DEFAULT_FORM_VALUES;
    } else {
        if (initialValues.sameAsShippingAddress) {
            // If the addresses are the same, don't populate any fields
            // other than the checkbox with an initial value.
            initialFormValues = {
                addresses_same: true
            };
        } else {
            // The addresses are not the same, populate the other fields.
            initialFormValues = {
                addresses_same: false,
                ...initialValues
            };
            delete initialFormValues.sameAsShippingAddress;
        }
    }

    const formChildrenProps = {
        ...props,
        classes,
        isSubmitting,
        setIsSubmitting,
        billingForm
    };

    return (
        <AddressForm
                initialValues={initialFormValues}
                {...formChildrenProps}
            />

    );
};

BillingForm.propTypes = {
    classes: shape({
        root: string
    }),
    initialValues: shape({
        city: string,
        postcode: string,
        region_code: string,
        sameAsShippingAddress: bool,
        street0: array
    })
};

BillingForm.defaultProps = {
    initialValues: {}
};

export default BillingForm;
