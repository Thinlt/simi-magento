import React, { Component } from 'react';
import defaultClasses from './login.css';
import classify from 'src/classify';
import Identify from 'src/simi/Helper/Identify';
import SignIn from './SignIn';
import CreateAccount from 'src/components/CreateAccount';
import ForgotPassword from 'src/components/ForgotPassword';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import BackIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/Back';
import { withRouter } from 'src/drivers';
import TitleHelper from 'src/simi/Helper/TitleHelper'
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';

import {
    completePasswordReset,
    createAccount,
    getUserDetails,
    resetPassword
} from 'src/actions/user';

class Login extends Component {

    state = {
        isCreateAccountOpen: false,
        isSignInOpen: true,
        isForgotPasswordOpen: false,
    };

    get signInForm() {
        const { isSignInOpen } = this.state;
        const { classes } = this.props;
        const isOpen = isSignInOpen;
        const className = isOpen ? classes.signIn_open : classes.signIn_closed;

        return (
            <div className={className}>
                <SignIn
                    classes={classes}
                    showCreateAccountForm={this.setCreateAccountForm}
                    setDefaultUsername={this.setDefaultUsername}
                    onForgotPassword={this.setForgotPasswordForm}
                />
            </div>
        );
    }

    createAccount = () => {};

    setCreateAccountForm = () => {
        /*
        When the CreateAccount component mounts, its email input will be set to
        the value of the SignIn component's email input.
        Inform's initialValue is set on component mount.
        Once the create account button is dirtied, always render the CreateAccount
        Component to show animation.
        */
        this.createAccount = className => {
            return (
                <div className={className}>
                    <CreateAccount
                        onSubmit={this.props.createAccount}
                        initialValues={{ email: this.state.defaultUsername }}
                    />
                </div>
            );
        };
        this.showCreateAccountForm();
    };

    forgotPassword = () => {};

    /*
     * When the ForgotPassword component is mounted, its email input will be set to
     * the value of the SignIn component's email input.
     * Our common Input component handles initialValue only when component is mounted.
     */
    setForgotPasswordForm = () => {
        this.forgotPassword = className => {
            const {
                completePasswordReset,
                forgotPassword,
                resetPassword
            } = this.props;
            const { email, isInProgress } = forgotPassword;

            return (
                <div className={className}>
                    <ForgotPassword
                        completePasswordReset={completePasswordReset}
                        email={email}
                        initialValues={{ email: this.state.defaultUsername }}
                        isInProgress={isInProgress}
                        onClose={this.closeForgotPassword}
                        resetPassword={resetPassword}
                    />
                </div>
            );
        };
        this.showForgotPasswordForm();
    };

    closeForgotPassword = () => {
        this.hideForgotPasswordForm();
    };

    get createAccountForm() {
        const { isCreateAccountOpen } = this.state;
        const { classes } = this.props;
        const isOpen = isCreateAccountOpen;
        const className = isOpen ? classes.form_open : classes.form_closed;

        return this.createAccount(className);
    }

    get forgotPasswordForm() {
        const { isForgotPasswordOpen } = this.state;
        const { classes } = this.props;
        const isOpen = isForgotPasswordOpen;
        const className = isOpen ? classes.form_open : classes.form_closed;

        return this.forgotPassword(className);
    }

    setDefaultUsername = nextDefaultUsername => {
        this.setState(() => ({ defaultUsername: nextDefaultUsername }));
    };

    showCreateAccountForm = () => {
        this.setState(() => ({
            isCreateAccountOpen: true,
            isSignInOpen: false,
            isForgotPasswordOpen: false
        }));
    };

    showForgotPasswordForm = () => {
        this.setState(() => ({
            isForgotPasswordOpen: true,
            isSignInOpen: false,
            isCreateAccountOpen: false
        }));
    };

    hideCreateAccountForm = () => {
        this.setState(() => ({
            isCreateAccountOpen: false,
            isSignInOpen: true,
            isForgotPasswordOpen: false
        }));
    };

    hideForgotPasswordForm = () => {
        this.setState(() => ({
            isForgotPasswordOpen: false,
            isSignInOpen: true,
            isCreateAccountOpen: false
        }));
    };


    render() {
        const {
            createAccountForm,
            hideCreateAccountForm,
            signInForm,
            forgotPasswordForm,
            hideForgotPasswordForm,
            props,
            state
        } = this;

        const {
            isCreateAccountOpen,
            isForgotPasswordOpen
        } = state;

        const {
            classes,
            isSignedIn,
            firstname,
            history
        } = props;

        if (isSignedIn) {
            history.push('/account.html')
            const message = firstname?
                Identify.__("Welcome %s Start shopping now").replace('%s', firstname):
                Identify.__("You have succesfully logged in, Start shopping now")
            if (this.props.toggleMessages)
                this.props.toggleMessages([{type: 'success', message: message, auto_dismiss: true}])
        }

        const handleBack =
            isCreateAccountOpen
                ? hideCreateAccountForm
                : isForgotPasswordOpen
                ? hideForgotPasswordForm
                : null;

        const title =
            isCreateAccountOpen
                ? Identify.__('Create Account')
                : isForgotPasswordOpen
                ? Identify.__('Forgot password')
                : Identify.__('Sign In')

        return (
            <React.Fragment>
                {TitleHelper.renderMetaHeader({
                    title:Identify.__('Customer Login')
                })}
                <div className={classes['login-background']} >
                    <div className={classes['login-container']} >
                        <div className={`${classes['login-header']} ${handleBack&&classes['has-back-btn']}`}>
                            {
                                handleBack &&
                                <div role="presentation" 
                                    className={classes['login-header-back']}
                                    onClick={handleBack}
                                    >
                                    <BackIcon style={{width: 20, height: 20}}/>
                                </div>
                            }
                            <div className={classes['login-header-title']}>
                                {title}
                            </div>
                        </div>
                        {signInForm}
                        {createAccountForm}
                        {forgotPasswordForm}
                    </div>
                </div>
            </React.Fragment>
        );
    }
}

const mapStateToProps = ({ user }) => {
    const { currentUser, isSignedIn, forgotPassword } = user;
    const { firstname, email, lastname } = currentUser;

    return {
        email,
        firstname,
        forgotPassword,
        isSignedIn,
        lastname,
    };
};

const mapDispatchToProps = {
    completePasswordReset,
    createAccount,
    getUserDetails,
    resetPassword,
    toggleMessages
};

export default compose(
    classify(defaultClasses),
    withRouter,
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(Login);
