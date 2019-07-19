import React, { useCallback, useMemo } from 'react';
import { Form} from 'informed';
import { array, bool, func, object, shape, string } from 'prop-types';

import { mergeClasses } from 'src/classify';
import defaultClasses from './addressForm.css';
import isObjectEmpty from 'src/util/isObjectEmpty';
import FormFields from './components/formFields';

const fields = [
    'city',
    'email',
    'firstname',
    'lastname',
    'postcode',
    'region_code',
    'street',
    'telephone'
];

const DEFAULT_FORM_VALUES = {
    addresses_same: true
};

const AddressForm = props => {
    const {
        cancel,
        countries,
        isAddressInvalid,
        invalidAddressMessage,
        initialValues,
        submit,
        submitting,
        billingForm,
        billingAddressSaved,
        submitBilling
    } = props;

    const classes = mergeClasses(defaultClasses, props.classes);
    const validationMessage = isAddressInvalid ? invalidAddressMessage : null;

    let initialFormValues = initialValues;

    if(billingForm){
        fields.push('addresses_same');
        if (isObjectEmpty(initialValues)) {
            initialFormValues = DEFAULT_FORM_VALUES;
        } else {
            if (initialValues.sameAsShippingAddress) {
                initialFormValues = {
                    addresses_same: true
                };
            } else {
                initialFormValues = {
                    addresses_same: false,
                    ...initialValues
                };
                delete initialFormValues.sameAsShippingAddress;
            }
        }
    }

    const values = useMemo(
        () =>
            fields.reduce((acc, key) => {
                acc[key] = initialFormValues[key];
                return acc;
            }, {}),
        [initialFormValues]
    );

    let initialCountry;
    let selectableCountries;
    const callGetCountries = {value: '', label: 'Please choose'}

    if (countries && countries.length) {
        selectableCountries = countries.map(
            ({ id, full_name_english }) => ({
                label: full_name_english,
                value: id
            })
        );
        initialCountry = values.country || countries[0].id;
    } else {
        selectableCountries = [];
        initialCountry = '';
    }
    selectableCountries.unshift(callGetCountries);

    const handleSubmitBillingSameFollowShipping = useCallback(
        () => {
            const billingAddress = {
                sameAsShippingAddress: true
            }
            submitBilling(billingAddress);
        },
        [submitBilling]
    )

    const handleSubmit = useCallback(
        values => {
            if (values.hasOwnProperty('addresses_same')) delete values.addresses_same
            if (values.hasOwnProperty('selected_shipping_address')) delete values.selected_shipping_address
            if (values.hasOwnProperty('password')) delete values.password
            if (values.save_in_address_book) {
                values.save_in_address_book = 1;
            } else {
                values.save_in_address_book = 0;
            }
            submit(values);
            if(!billingForm && !billingAddressSaved){
                handleSubmitBillingSameFollowShipping();
            }
        },
        [submit]
    );

    const formChildrenProps = {
        ...props,
        classes,
        submitting,
        submit,
        validationMessage,
        initialCountry,
        selectableCountries
    };

    return (
        <Form
            className={classes.root}
            initialValues={values}
            onSubmit={handleSubmit}
            style={{display: 'inline-block', width: '100%'}}
        >
            <FormFields {...formChildrenProps} />
        </Form>
    );
};

AddressForm.propTypes = {
    cancel: func.isRequired,
    classes: shape({
        body: string,
        button: string,
        city: string,
        email: string,
        firstname: string,
        footer: string,
        heading: string,
        lastname: string,
        postcode: string,
        root: string,
        region_code: string,
        street0: string,
        telephone: string,
        validation: string
    }),
    countries: array,
    invalidAddressMessage: string,
    initialValues: object,
    isAddressInvalid: bool,
    submit: func.isRequired,
    submitting: bool
};

AddressForm.defaultProps = {
    initialValues: {}
};

export default AddressForm;
