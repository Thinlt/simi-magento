import React, { Component } from 'react';
import { bool, func, object, shape, string } from 'prop-types';
import { Form } from 'informed';

import Button from 'src/components/Button';
import Field from 'src/components/Field';
import LoadingIndicator from 'src/components/LoadingIndicator';
import TextInput from 'src/components/TextInput';

import { isRequired } from 'src/util/formValidators';

import defaultClasses from './signIn.css';
import classify from 'src/classify';
import { simiSignIn } from 'src/simi/Model/Customer'
import Identify from 'src/simi/Helper/Identify'
import  * as Constants from 'src/simi/Config/Constants'
import { Util } from '@magento/peregrine';
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

class SignIn extends Component {
    static propTypes = {
        classes: shape({
            forgotPassword: string,
            form: string,
            modal: string,
            modal_active: string,
            root: string,
            showCreateAccountButton: string,
            signInDivider: string,
            signInError: string,
            signInSection: string
        }),
        isGettingDetails: bool,
        isSigningIn: bool,
        onForgotPassword: func.isRequired,
        setDefaultUsername: func,
        signIn: func,
        signInError: object
    };

    constructor(props) {
        super(props)
        this.state = {
            signInError: null
        }
    }

    get errorMessage() {
        const { signInError } = this.state
        if (signInError) {
            return Identify.__('The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.')
        }
    }

    render() {
        const { classes, isGettingDetails, isSigningIn } = this.props;
        const { onSignIn, errorMessage } = this;

        if (isGettingDetails || isSigningIn) {
            return (
                <div className={classes.modal_active}>
                    <LoadingIndicator>Signing In</LoadingIndicator>
                </div>
            );
        } else {
            return (
                <div className={classes.root}>
                    <Form
                        className={classes.form}
                        getApi={this.setFormApi}
                        onSubmit={onSignIn}
                    >
                        <Field label="Email" required={true}>
                            <TextInput
                                autoComplete="email"
                                field="email"
                                validate={isRequired}
                                validateOnBlur
                            />
                        </Field>
                        <Field label="Password" required={true}>
                            <TextInput
                                autoComplete="current-password"
                                field="password"
                                type="password"
                                validate={isRequired}
                                validateOnBlur
                            />
                        </Field>
                        <div className={classes.signInButton}>
                            <Button priority="high" type="submit">
                                Sign In
                            </Button>
                        </div>
                        <div className={classes.signInError}>
                            {errorMessage}
                        </div>
                        <button
                            type="button"
                            className={classes.forgotPassword}
                            onClick={this.handleForgotPassword}
                        >
                            Forgot password?
                        </button>
                    </Form>
                    <div className={classes.signInDivider} />
                    <div className={classes.showCreateAccountButton}>
                        <Button
                            priority="high"
                            onClick={this.showCreateAccountForm}
                        >
                            Create an Account
                        </Button>
                    </div>
                </div>
            );
        }
    }

    handleForgotPassword = () => {
        const username = this.formApi.getValue('email');

        if (this.props.setDefaultUsername) {
            this.props.setDefaultUsername(username);
        }

        this.props.onForgotPassword();
    };

    onSignIn = () => {
        const username = this.formApi.getValue('email');
        const password = this.formApi.getValue('password');
        
        simiSignIn(this.setData.bind(this), { username, password })
    };

    setData = (data) => {
        if (this.props.simiSignedIn) {
            if (data && !data.error) {
                if (data.customer_access_token) {
                    Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, data.customer_access_token)
                    setToken(data.customer_access_token)
                    this.props.simiSignedIn(data.customer_access_token)
                } else {
                    setToken(data)
                    this.props.simiSignedIn(data)
                }
            }
            else
                this.setState({ signInError: true})
        }
    }

    setFormApi = formApi => {
        this.formApi = formApi;
    };

    showCreateAccountForm = () => {
        const username = this.formApi.getValue('email');

        if (this.props.setDefaultUsername) {
            this.props.setDefaultUsername(username);
        }

        this.props.showCreateAccountForm();
    };
}

export default classify(defaultClasses)(SignIn);

async function setToken(token) {
    // TODO: Get correct token expire time from API
    return storage.setItem('signin_token', token, 3600);
}