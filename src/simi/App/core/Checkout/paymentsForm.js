import React, { useCallback, useState } from 'react';
import { Form } from 'informed';
import { array, bool, shape, string } from 'prop-types';

import { mergeClasses } from 'src/classify';
import defaultClasses from './paymentsForm.css';
import PaymentsFormItems from './components/paymentsFormItems';

/**
 * A wrapper around the payment form. This component's purpose is to maintain
 * the submission state as well as prepare/set initial values.
 */
const PaymentsForm = props => {
    const { initialValues, paymentMethods } = props;
    const classes = mergeClasses(defaultClasses, props.classes);

    const [isSubmitting, setIsSubmitting] = useState(false);

    const handleSubmit = useCallback(() => {
        setIsSubmitting(true);
    }, [setIsSubmitting]);

    const formChildrenProps = {
        ...props,
        classes,
        isSubmitting,
        setIsSubmitting
    };

    return (
        <Form
            className={classes.root}
            initialValues={initialValues}
            onSubmit={handleSubmit}
        >
            <PaymentsFormItems {...formChildrenProps} />
        </Form>
    );
};

PaymentsForm.propTypes = {
    classes: shape({
        root: string
    }),
    initialValues: shape({
        city: string,
        postcode: string,
        region_code: string,
        street0: array
    }),
    paymentMethods: array
};

PaymentsForm.defaultProps = {
    initialValues: {}
};

export default PaymentsForm;
