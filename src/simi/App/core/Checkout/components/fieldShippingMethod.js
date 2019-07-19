import React, { Fragment, useCallback } from 'react';
import {connect} from 'react-redux';
import { useFormState, useMemo } from 'informed';

import { mergeClasses } from 'src/classify';
import Identify from 'src/simi/Helper/Identify';
import defaultClasses from './fieldShippingMethod.css';
import Select from 'src/components/Select';

const fieldShippingMethod = (props) => {
    const { classes, initialValue, selectableShippingMethods, availableShippingMethods, cancel, submit } = props;

    const formState = useFormState();

    /* const value = useMemo(
        () =>( return initialValue , {}),
        [initialValue]
    ); */


    const handleSelectMethod = useCallback(
        (shippingMethod) => {
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
            // cancel();
        },
        [availableShippingMethods, cancel, submit]
    )

    const handleShippingMethod = () => {
        const shippingMethod = formState.values['shippingMethod'];
        if (shippingMethod) handleSelectMethod(shippingMethod);
    }

    return <Fragment>
        <div
            className={defaultClasses['ship-method_field']}
            id={classes.shippingMethod}
        >
            <Select
                field="shippingMethod"
                initialValue={initialValue}
                items={selectableShippingMethods}
                onChange={() => handleShippingMethod()}
            />
        </div>
    </Fragment>

}
export default fieldShippingMethod;
