import React from 'react';
import { shape, string } from 'prop-types';
import { Form } from 'informed';

import Field from 'src/components/Field';
import TextInput from 'src/components/TextInput';
import { validators } from './validators';
import classes from './createAccount.css';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import { createAccount } from 'src/simi/Model/Customer'
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';

const $ = window.$;

const CreateAccount = props => {
    const { history, createAccountError } = props;
    const errorMessage = createAccountError && (Object.keys(createAccountError).length !== 0) ? Identify.__('An error occurred. Please try again.') : null
    var registeringEmail = null
    var registeringPassword = null

    const initialValues = () => {
        const { initialValues } = props;
        const { email, firstName, lastName, ...rest } = initialValues;

        return {
            customer: { email, firstname: firstName, lastname: lastName },
            ...rest
        };
    }

    const handleSubmit = values => {
        const params = {
            password: values.password,
            confirm_password: values.confirm,
            ...values.customer,
            news_letter: values.subscribe ? 1 : 0
        }
        showFogLoading()
        registeringEmail = values.customer.email
        registeringPassword = values.password
        createAccount(registerDone, params)
    };

    const registerDone = (data) => {
        hideFogLoading()
        // Reset form
        $('.form-create-account')[0].reset();
        if (data.errors) {
            let errorMsg = ''
            if (data.errors.length) {
                data.errors.map(error => {
                    errorMsg += error.message
                })
                showToastMessage(errorMsg)
            }
        } else {

        }
    }

    const handleBack = () => {
        history.push('/login.html');
    };

    return (
        <React.Fragment>
            {TitleHelper.renderMetaHeader({
                title: Identify.__('Create Account')
            })}
            <Form
                className={`form-create-account ${classes.root} ${Identify.isRtl() ? classes['rtl-rootForm'] : null}`}
                initialValues={initialValues}
                onSubmit={handleSubmit}
            >
                <div className={classes.lead1}>
                    {Identify.__('create an account'.toUpperCase())}
                </div>
                <div className={classes.lead2}>
                    {Identify.__('Please enter the following information to create your account.')}
                </div>
                <Field label="First Name *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.firstname"
                        autoComplete="given-name"
                        validate={validators.get('firstName')}
                        validateOnBlur
                        placeholder="First Name"
                    />
                </Field>
                <Field label="Last Name *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.lastname"
                        autoComplete="family-name"
                        validate={validators.get('lastName')}
                        validateOnBlur
                        placeholder="Last Name"
                    />
                </Field>
                <Field label="Email Address *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.email"
                        autoComplete="email"
                        validate={validators.get('email')}
                        validateOnBlur
                        placeholder="Email"
                    />
                </Field>
                <Field label="Phone Number *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.telephone"
                        validate={validators.get('telephone')}
                        validateOnBlur
                        placeholder="Phone"
                    />
                </Field>
                <Field label="Password *">
                    <TextInput
                        classes={classes}
                        field="password"
                        type="password"
                        autoComplete="new-password"
                        validate={validators.get('password')}
                        validateOnBlur
                        placeholder="Password"
                    />
                </Field>
                <Field label="Password Confirmation*">
                    <TextInput
                        field="confirm"
                        type="password"
                        validate={validators.get('confirm')}
                        validateOnBlur
                        placeholder="Password confirmation"
                    />
                </Field>
                <div className={classes.error}>{errorMessage}</div>
                <div className={classes.actions}>
                    <button
                        priority="high" className={classes.submitButton} type="submit"
                    >
                        {Identify.__('Register')}
                    </button>
                </div>
                <div
                    className={`special-back ${classes['back']}`}
                    id="btn-back"
                    onClick={handleBack}
                >
                    <span>{Identify.__('back'.toUpperCase())}</span>
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
    })
}

CreateAccount.defaultProps = {
    initialValues: {}
};

export default CreateAccount;
