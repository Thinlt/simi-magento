import React, { Fragment, useCallback, useState } from 'react';
// import { useFormState } from 'informed';

import Select from './Select';
require('./fieldShippingMethod.scss')

const fieldShippingMethod = (props) => {
    const { initialValue, selectableShippingMethods, availableShippingMethods, cancel, submit } = props;

    const [ selectedValue, setSelectedValue ] = useState(initialValue);

    const handleSelectMethod = useCallback(
        (shippingMethod) => {
            if (props.handleSelect) {
                props.handleSelect(shippingMethod);
            }
        },
        [availableShippingMethods, cancel, submit]
    )

    const handleShippingMethod = (e) => {
        const shippingMethod = e.target.value;
        if (shippingMethod) handleSelectMethod(shippingMethod);
        setSelectedValue(shippingMethod);
    }

    return <Fragment>
        <div
            className="ship-method_field"
            id="shippingMethod"
        >
            <Select
                field="shippingMethod"
                value={selectedValue}
                items={selectableShippingMethods}
                onChange={handleShippingMethod}
            />
        </div>
    </Fragment>

}
export default fieldShippingMethod;
