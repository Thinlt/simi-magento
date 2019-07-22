import React, { Component } from 'react';
import { bool, func, object } from 'prop-types';
import { Form } from 'informed';
import Field from 'src/components/Field';
import {showFogLoading, hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
import TextInput from 'src/components/TextInput';
import { isRequired } from 'src/util/formValidators';
import classes from './signIn.css';
import { simiSignIn } from 'src/simi/Model/Customer'
import Identify from 'src/simi/Helper/Identify'
import  * as Constants from 'src/simi/Config/Constants'
import { Util } from '@magento/peregrine'
import {configColor} from 'src/simi/Config'
import TitleHelper from 'src/simi/Helper/TitleHelper'

const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

class SignIn extends Component {
    static propTypes = {
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
        hideFogLoading()
        const { onSignIn, errorMessage } = this;
        return (
            <div className={classes.root}>
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Sign In')
                })}
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
                    <div className={classes.signInButtonCtn}>
                        <button 
                            priority="high" className={classes.signInButton} type="submit" 
                            style={{backgroundColor: configColor.button_background, color: configColor.button_text_color}}>
                            {Identify.__('Sign In')}
                        </button>
                    </div>
                    <div className={classes.signInError}>
                        {errorMessage}
                    </div>
                    <button
                        type="button"
                        className={classes.forgotPassword}
                        onClick={this.handleForgotPassword}
                    >
                        {Identify.__('Forgot password?')}
                    </button>
                </Form>
                <div className={classes.signInDivider} />
                <div className={classes.showCreateAccountButtonCtn}>
                    <button priority="high" className={classes.showCreateAccountButton} onClick={this.showCreateAccountForm} type="submit">
                        {Identify.__('Create an Account')}
                    </button>
                </div>
            </div>
        );
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
        showFogLoading()
    };

    setData = (data) => {
        hideFogLoading()
        if (this.props.simiSignedIn) {
            if (data && !data.errors) {
                if (data.customer_access_token) {
                    Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, data.customer_identity)
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

export default SignIn;

async function setToken(token) {
    // TODO: Get correct token expire time from API
    return storage.setItem('signin_token', token, 3600);
}