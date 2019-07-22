import React from 'react';
import { func, shape, string } from 'prop-types';
import { Form } from 'informed';

import Checkbox from 'src/components/Checkbox';
import Field from 'src/components/Field';
import TextInput from 'src/components/TextInput';
import { validators } from './validators';
import classes from './createAccount.css';
import {configColor} from 'src/simi/Config'
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'

const CreateAccount = props => {
    const { createAccountError } = props;
    const errorMessage = createAccountError && (Object.keys(createAccountError).length !== 0) ? Identify.__('An error occurred. Please try again.'):null

    const initialValues = () => {
        const { initialValues } = props;
        const { email, firstName, lastName, ...rest } = initialValues;

        return {
            customer: { email, firstname: firstName, lastname: lastName },
            ...rest
        };
    }

    const handleSubmit = values => {
        const { onSubmit } = props;
        if (typeof onSubmit === 'function') {
            onSubmit(values);
        }
    };

    
    return (
        <React.Fragment>
            {TitleHelper.renderMetaHeader({
                title:Identify.__('Create Account')
            })}
            <Form
                className={classes.root}
                initialValues={initialValues}
                onSubmit={handleSubmit}
            >
                <h3 className={classes.lead}>
                    {`Check out faster, use multiple addresses, track
                            orders and more by creating an account!`}
                </h3>
                <Field label="First Name" required={true}>
                    <TextInput
                        field="customer.firstname"
                        autoComplete="given-name"
                        validate={validators.get('firstName')}
                        validateOnBlur
                    />
                </Field>
                <Field label="Last Name" required={true}>
                    <TextInput
                        field="customer.lastname"
                        autoComplete="family-name"
                        validate={validators.get('lastName')}
                        validateOnBlur
                    />
                </Field>
                <Field label="Email" required={true}>
                    <TextInput
                        field="customer.email"
                        autoComplete="email"
                        validate={validators.get('email')}
                        validateOnBlur
                    />
                </Field>
                <Field label="Password">
                    <TextInput
                        field="password"
                        type="password"
                        autoComplete="new-password"
                        validate={validators.get('password')}
                        validateOnBlur
                    />
                </Field>
                <Field label="Confirm Password">
                    <TextInput
                        field="confirm"
                        type="password"
                        validate={validators.get('confirm')}
                        validateOnBlur
                    />
                </Field>
                <div className={classes.subscribe}>
                    <Checkbox
                        field="subscribe"
                        label="Subscribe to news and updates"
                    />
                </div>
                <div className={classes.error}>{errorMessage}</div>
                <div className={classes.actions}>
                    <button 
                        priority="high" className={classes.submitButton} type="submit" 
                        style={{backgroundColor: configColor.button_background, color: configColor.button_text_color}}>
                        {Identify.__('Submit')}
                    </button>
                </div>
            </Form>
        </React.Fragment>
    );
}

CreateAccount.propTypes = {
    createAccountError: shape({
        message: string
    }),
    initialValues: shape({
        email: string,
        firstName: string,
        lastName: string
    }),
    onSubmit: func
}

CreateAccount.defaultProps = {
    initialValues: {}
};

export default CreateAccount;
