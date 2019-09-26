import React, { Component } from 'react';
import { bool, func, object } from 'prop-types';
import { Form } from 'informed';
import TextInput from 'src/components/TextInput';
import { isRequired } from 'src/util/formValidators';
import classes from './signIn.css';
import Identify from 'src/simi/Helper/Identify'
import {configColor} from 'src/simi/Config'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import UserIcon from '../../../../../BaseComponents/Icon/User';
import Warning from '../../../../../BaseComponents/Icon/Warning';
import CheckBox from 'src/simi/BaseComponents/CheckBox';
import Facebook from 'src/simi/BaseComponents/Icon/Facebook';
import Instagram from 'src/simi/BaseComponents/Icon/Instagram';
import Twitter from 'src/simi/BaseComponents/Icon/Twitter';
import GooglePlus from 'src/simi/BaseComponents/Icon/TapitaIcons/GooglePlus';

class SignIn extends Component {
    static propTypes = {
        isGettingDetails: bool,
        onForgotPassword: func.isRequired,
        signIn: func
    };

    render() {
        return (
            <div className={classes.root}>
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Sign In')
                })}
                <Form
                    className={classes.form}
                    getApi={this.setFormApi}
                    onSubmit={() => this.onSignIn()}
                >
                <div className={classes.userInput}>
                    <UserIcon className={classes.userIcon} style={{width:'16px',height:'16px'}}/>
                    <TextInput
                        classes={classes}
                        style={{paddingLeft:"56px"}}
                        autoComplete="email"
                        field="email"
                        validate={isRequired}
                        validateOnBlur
                        placeholder="Email"
                    />
                </div>
                <div className={classes.passwordInput}>
                    <Warning className={classes.passwordIcon} style={{width:'16px',height:'16px'}}/>
                    <TextInput
                        classes={classes}
                        style={{paddingLeft:"56px"}}
                        autoComplete="current-password"
                        field="password"
                        type="password"
                        validate={isRequired}
                        validateOnBlur
                        placeholder="Password"
                    />
                </div>
                    <div className={classes['signInAction']}>
                        <CheckBox label={Identify.__("Remember me")}/>
                        <button
                            type="button"
                            className={classes.forgotPassword}
                            onClick={this.handleForgotPassword}
                        >
                            {Identify.__('Forgot password')}
                        </button>
                    </div>
                    <div className={classes.signInButtonCtn}>
                        <button 
                            priority="high" className={classes.signInButton} type="submit" 
                            style={{backgroundColor: '#101820', color: configColor.button_text_color}}>
                            {Identify.__('Sign In')}
                        </button>
                    </div>
                </Form>
                <div className={classes.signInDivider}>
                    <span className={classes.signInWith}>{Identify.__("or sign in with".toUpperCase())}</span>
                </div>
                <div className={classes.socialMedia}>
                    <Facebook/>
                    <Twitter/>
                    <GooglePlus/>
                    <Instagram/>
                    <Instagram/>
                </div>
                <div className={classes.showCreateAccountButtonCtn}>
                    <button priority="high" className={classes.showCreateAccountButton} onClick={this.showCreateAccountForm} type="submit">
                        {Identify.__('Create an Account')}
                    </button>
                </div>
            </div>
        );
    }

    handleForgotPassword = () => {
        this.props.onForgotPassword();
    };

    onSignIn() {
        const username = this.formApi.getValue('email');
        const password = this.formApi.getValue('password');
        this.props.onSignIn(username, password)
    }

    setFormApi = formApi => {
        this.formApi = formApi;
    };

    showCreateAccountForm = () => {
        this.props.showCreateAccountForm();
    };
}

export default SignIn;