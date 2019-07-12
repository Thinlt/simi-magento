import React, { useCallback } from 'react';
import { Form, useFieldState } from 'informed';
import { array, bool, func, shape, string } from 'prop-types';

import { mergeClasses } from 'src/classify';
import defaultClasses from './shippingForm.css';
import Identify from 'src/simi/Helper/Identify';
import FieldShippingMethod from './components/fieldShippingMethod';

const ShippingForm = props => {
    const {
        availableShippingMethods,
        cancel,
        shippingMethod,
        submit,
        submitting
    } = props;
    const classes = mergeClasses(defaultClasses, props.classes);

    let initialValue;
    let selectableShippingMethods;

    const defaultMethod = { value: '', label: 'Please choose' }

    if (availableShippingMethods.length) {
        selectableShippingMethods = availableShippingMethods.map(
            ({ carrier_code, carrier_title }) => ({
                label: carrier_title,
                value: carrier_code
            })
        );
        initialValue =
            shippingMethod || availableShippingMethods[0].carrier_code;
    } else {
        selectableShippingMethods = [];
        initialValue = '';
    }

    selectableShippingMethods.unshift(defaultMethod);

    const handleSubmit = useCallback(
        ({ shippingMethod }) => {
            const selectedShippingMethod = availableShippingMethods.find(
                ({ carrier_code }) => carrier_code === shippingMethod
            );

            if (!selectedShippingMethod) {
                console.warn(
                    `Could not find the selected shipping method ${selectedShippingMethod} in the list of available shipping methods.`
                );
                cancel();
                return;
            }

            submit({ shippingMethod: selectedShippingMethod });
        },
        [availableShippingMethods, cancel, submit]
    );

    const childFieldProps = {
        classes,
        initialValue,
        selectableShippingMethods,
        availableShippingMethods,
        submit,
        cancel
    }

    return (
        <Form className={classes.root} onSubmit={handleSubmit}>
            <div className={classes.body}>
                {/* <h2 className={classes.heading}>{Identify.__("Shipping Information")}</h2> */}
                <FieldShippingMethod {...childFieldProps} />
            </div>
        </Form>
    );
};

ShippingForm.propTypes = {
    availableShippingMethods: array.isRequired,
    cancel: func.isRequired,
    classes: shape({
        body: string,
        button: string,
        footer: string,
        heading: string,
        shippingMethod: string
    }),
    shippingMethod: string,
    submit: func.isRequired,
    submitting: bool
};

ShippingForm.defaultProps = {
    availableShippingMethods: [{}]
};

export default ShippingForm;
