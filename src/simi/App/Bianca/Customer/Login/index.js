import React, { Component } from 'react';
import defaultClasses from './login.css';
import classify from 'src/classify';
import Identify from 'src/simi/Helper/Identify';
import SignIn from './SignIn';
import PhoneLogin from './SignIn/Phone/PhoneLogin'
import VendorLogInForm from './VendorLogin';
import CreateAccount from './CreateAccount';
import ForgotPassword from './ForgotPassword';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import BackIcon from 'src/simi/BaseComponents/Icon/TapitaIcons/Back';
import { withRouter } from 'react-router-dom';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { simiSignIn as signinApi, vendorLogin } from 'src/simi/Model/Customer';
import {
    showFogLoading,
    hideFogLoading
} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import * as Constants from 'src/simi/Config/Constants';
import { Util } from '@magento/peregrine';
import { simiSignedIn } from 'src/simi/Redux/actions/simiactions';
import { showToastMessage } from 'src/simi/Helper/Message';
import VendorRegister from './VendorRegister';
import Facebook from 'src/simi/BaseComponents/Icon/Facebook';
import Instagram from 'src/simi/BaseComponents/Icon/Instagram';
import Twitter from 'src/simi/BaseComponents/Icon/Twitter';
import GooglePlus from 'src/simi/BaseComponents/Icon/TapitaIcons/GooglePlus';

const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();
class Login extends Component {
    state = {
        isCreateAccountOpen: false,
        isEmailLogin: true,
        isForgotPasswordOpen: false,
        isPhoneLogin: false,
        isVendorRegisterOpen: false
    };

    stateForgot = () => {
        const { history } = this.props;

        return (
            history.location &&
            history.location.state &&
            history.location.state.forgot
        );
    };

    componentDidMount() {
        if (this.stateForgot()) {
            this.setForgotPasswordForm();
        }
    }

    get emailLoginForm() {
        const { isEmailLogin } = this.state;
        const { classes } = this.props;
        const isOpen = isEmailLogin;
        const className = isOpen ? classes.signIn_open : classes.signIn_closed;

        return (
            <div className={className}>
                <SignIn
                    classes={classes}
                    onForgotPassword={this.setForgotPasswordForm}
                    onSignIn={this.onSignIn.bind(this)}
                />
            </div>
        );
    }

    get phoneLoginForm() {
        const { isPhoneLogin } = this.state;
        const { classes } = this.props;
        const isOpen = isPhoneLogin;
        const className = isOpen ? classes.signIn_open : classes.signIn_closed;

        return (
            <div className={className}>
                <PhoneLogin />
            </div>
        );
    }

    get socialAndCreateAccount (){
        const {setCreateAccountForm} = this
        const {isCreateAccountOpen, isForgotPasswordOpen} = this.state
        const { classes } = this.props;
        return(
            <React.Fragment>
                <div className={`${(isCreateAccountOpen||isForgotPasswordOpen) ? classes["inactive"] : classes["active"] }` }>
                    <div className={`${classes['signInDivider']}`} >
                        <hr className={`${classes['hr']} ${classes['left-hr']}`} />
                        <div className={`${classes['signInWidth']}`}>{Identify.__("or sign in with".toUpperCase())}</div>
                        <hr className={`${classes['hr']} ${classes['right-hr']}`} />
                    </div>
                    <div className={`${classes['socialMedia']}`}>
                        <span className={`${classes['social-icon']}`} >
                            <span className={`${classes['icon']} ${classes['facebook']}`}></span>
                        </span>
                        <span className={`${classes['social-icon']}`} >
                            <span className={`${classes['icon']} ${classes['twitter']}`}></span>
                        </span>
                        <span className={`${classes['social-icon']} ${classes['special']}`} >
                            <span className={`${classes['icon']} ${classes['google']}`}></span>
                        </span>
                        <span className={`${classes['social-icon']}`} >
                            <span className={`${classes['icon']} ${classes['linkedin']}`}></span>
                        </span>
                        <span className={`${classes['social-icon']}`} >
                            <span className={`${classes['icon']} ${classes['instagram']}`}></span>
                        </span>
                    </div>
                    <div className={`${classes['showCreateAccountButtonCtn']}`} >
                        <button priority="high" className={`${classes['showCreateAccountButton']}`} onClick={setCreateAccountForm} type="submit">
                            {Identify.__('Create an Account')}
                        </button>
                    </div>
                </div>
            </React.Fragment>
        )
    }
    // get vendorLogInForm() {
    //     const { isPhoneLogin } = this.state;
    //     const { classes } = this.props;
    //     const isOpen = isPhoneLogin;
    //     const className = isOpen ? classes.signIn_open : classes.signIn_closed;

    //     return (
    //         <div className={className}>
    //             <VendorLogInForm
    //                 classes={classes}
    //                 showVendorRegisterForm={this.setVendorRegisterForm}
    //                 onForgotPassword={this.setForgotPasswordForm}
    //                 onSignIn={this.onVendorLogin.bind(this)}
    //             />
    //         </div>
    //     );
    // }

    vendorRegister = () => {};
    createAccount = () => {};

    setVendorRegisterForm = () => {
        this.vendorRegister = className => {
            return (
                <div className={className}>
                    <VendorRegister onSignIn={this.onVendorLogin.bind(this)} />
                </div>
            );
        };
        this.showVendorRegisterForm();
    };

    setCreateAccountForm = () => {

        this.createAccount = className => {
            return (
                <div className={className}>
                    <CreateAccount onSignIn={this.onSignIn.bind(this)} />
                </div>
            );
        };
        this.showCreateAccountForm();
    };

    forgotPassword = () => {};

    setForgotPasswordForm = () => {
        this.forgotPassword = className => {
            return (
                <div className={className}>
                    <ForgotPassword onClose={this.closeForgotPassword} />
                </div>
            );
        };
        this.showForgotPasswordForm();
    };

    closeForgotPassword = () => {
        this.hideForgotPasswordForm();
    };

    get vendorRegisterForm() {
        const { isVendorRegisterOpen } = this.state;
        const { classes } = this.props;
        const isOpen = isVendorRegisterOpen;
        const className = isOpen ? classes.form_open : classes.form_closed;

        return this.vendorRegister(className);
    }

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

    showVendorRegisterForm = () => {
        this.setState(() => ({
            isCreateAccountOpen: false,
            isEmailLogin: false,
            isForgotPasswordOpen: false,
            isPhoneLogin: false,
            isVendorRegisterOpen: true
        }));
    };

    showCreateAccountForm = () => {
        this.setState(() => ({
            isCreateAccountOpen: true,
            isEmailLogin: false,
            isForgotPasswordOpen: false,
            isPhoneLogin: false,
            isVendorRegisterOpen: false
        }));
    };

    showForgotPasswordForm = () => {
        this.setState(() => ({
            isForgotPasswordOpen: true,
            isEmailLogin: false,
            isCreateAccountOpen: false,
            isPhoneLogin: false,
            isVendorRegisterOpen: false
        }));
    };

    showEmailLoginForm = () => {
        this.setState(() => ({
            isForgotPasswordOpen: false,
            isEmailLogin: true,
            isCreateAccountOpen: false,
            isPhoneLogin: false,
            isVendorRegisterOpen: false
        }));
    };

    showPhoneLoginForm = () => {
        this.setState(() => ({
            isForgotPasswordOpen: false,
            isEmailLogin: false,
            isCreateAccountOpen: false,
            isPhoneLogin: true,
            isVendorRegisterOpen: false
        }));
    };

    onSignIn(username, password) {
        Identify.storeDataToStoreage(
            Identify.LOCAL_STOREAGE,
            Constants.SIMI_SESS_ID,
            null
        );
        signinApi(this.signinCallback.bind(this), { username, password });
        showFogLoading();
    }

    onVendorLogin(username, password) {
        Identify.storeDataToStoreage(
            Identify.LOCAL_STOREAGE,
            Constants.SIMI_SESS_ID,
            null
        );
        vendorLogin(this.vendorLoginCallback.bind(this), {
            username,
            password
        });
        showFogLoading();
    }

    vendorLoginCallback = data => {
        hideFogLoading();
        console.log(data);
        window.location.href = data.redirect_url;
    };

    signinCallback = data => {
        hideFogLoading();
        if (this.props.simiSignedIn) {
            if (data && !data.errors) {
                storage.removeItem('cartId');
                storage.removeItem('signin_token');
                if (data.customer_access_token) {
                    Identify.storeDataToStoreage(
                        Identify.LOCAL_STOREAGE,
                        Constants.SIMI_SESS_ID,
                        data.customer_identity
                    );
                    setToken(data.customer_access_token);
                    this.props.simiSignedIn(data.customer_access_token);
                } else {
                    Identify.storeDataToStoreage(
                        Identify.LOCAL_STOREAGE,
                        Constants.SIMI_SESS_ID,
                        null
                    );
                    setToken(data);
                    this.props.simiSignedIn(data);
                }
            } else
                showToastMessage(
                    Identify.__(
                        'The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.'
                    )
                );
        }
    };

    render() {
        const {
            createAccountForm,
            emailLoginForm,
            forgotPasswordForm,
            phoneLoginForm,
            socialAndCreateAccount,
            vendorRegisterForm,
            props,
            state
        } = this;
        const {
            isCreateAccountOpen,
            isForgotPasswordOpen,
            isEmailLogin,
            isPhoneLogin
        } = state;

        const { classes, isSignedIn, firstname, history } = props;

        if (isSignedIn) {
            if (
                history.location.hasOwnProperty('pushTo') &&
                history.location.pushTo
            ) {
                const { pushTo } = history.location;
                history.push(pushTo);
            } else {
                history.push('/');
            }

            const message = firstname
                ? Identify.__('Welcome %s Start shopping now').replace(
                      '%s',
                      firstname
                  )
                : Identify.__(
                      'You have succesfully logged in, Start shopping now'
                  );
            if (this.props.toggleMessages)
                this.props.toggleMessages([
                    { type: 'success', message: message, auto_dismiss: true }
                ]);
        }
        const showBackBtn = isCreateAccountOpen || isForgotPasswordOpen;

        return (
            <React.Fragment>
                {TitleHelper.renderMetaHeader({
                    title: Identify.__('Customer Login')
                })}
                <div className={classes['login-background']}>
                    <div className={classes['login-container']}>
                        <div className={`${classes['buyer-login']}`}>
                            <span>{Identify.__('Buyer'.toUpperCase())}</span>
                        </div>
                        <div className={`${(isCreateAccountOpen||isForgotPasswordOpen) ? classes["inactive"]: classes[""]} ${classes['select-type']}` }>
                            <div onClick={this.showPhoneLoginForm} className={`${isPhoneLogin ? classes["active"]: null} ${classes['phone-type']}` }>
                                <span className={classes['icon-phone']} />
                                <span className={classes['title-phone']}>
                                    {Identify.__('Phone')}
                                </span>
                            </div>
                            <div onClick={this.showEmailLoginForm} className={`${isEmailLogin ? classes["active"]: null} ${classes['email-type']}` }>
                                <span className={classes['icon-email']} />
                                <span className={classes['title-email']}>
                                    {Identify.__('Email')}
                                </span>
                            </div>
                        </div>
                        {emailLoginForm}
                        {phoneLoginForm}
                        {socialAndCreateAccount}
                        {createAccountForm}
                        {vendorRegisterForm}
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
        lastname
    };
};

const mapDispatchToProps = {
    toggleMessages,
    simiSignedIn
};

export default compose(
    classify(defaultClasses),
    withRouter,
    connect(
        mapStateToProps,
        mapDispatchToProps
    )
)(Login);

async function setToken(token) {
    // TODO: Get correct token expire time from API
    return storage.setItem('signin_token', token, 3600);
}
