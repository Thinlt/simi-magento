import React, { Component } from 'react';
import defaultClasses from './login.css';
import classify from 'src/classify';
import Identify from 'src/simi/Helper/Identify';
import SignIn from './SignIn';
import PhoneLogin from './SignIn/Phone/PhoneLogin';
import CreateAccount from './CreateAccount';
import ForgotPassword from './ForgotPassword';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import { withRouter } from 'react-router-dom';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { simiSignIn as signinApi } from 'src/simi/Model/Customer';
import { socialLogin as socialLoginApi } from 'src/simi/Model/Customer';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import * as Constants from 'src/simi/Config/Constants';
import { Util } from '@magento/peregrine';
import { simiSignedIn } from 'src/simi/Redux/actions/simiactions';
import { showToastMessage } from 'src/simi/Helper/Message';
import firebase, { auth } from 'firebase';
import firebaseApp from './SocialLogin/base';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import VerifyOtpModal from 'src/simi/App/Bianca/Components/Otp/VerifyOtpModal';
import { sendOTPForLogin, verifyOTPForLogin } from 'src/simi/Model/Otp';

const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();
const $ = window.$;

class Login extends Component {
	constructor(props) {
		super(props);
	}

	state = {
		isCreateAccountOpen: false,
		isEmailLogin: true,
		isForgotPasswordOpen: false,
		isPhoneLogin: false,
		forgotPassSuccess: 'block',
		openVerifyModal: false
	};

	stateForgot = () => {
		const { history } = this.props;

		return history.location && history.location.state && history.location.state.forgot;
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

	openVerifyOtpModal = () => {
		this.setState({
			openVerifyModal: true
		})
	}

	closeVerifyModal = () => {
		this.setState({
			openVerifyModal: false
		})
		localStorage.removeItem("numberphone_otp")
	}

	handleVerifyLogin = (phoneNumber) => {
		let logintotp = localStorage.getItem('login_otp');
		$('#login-input-otp-warning').css({ display: 'none' })
		showFogLoading();
		var typeLogin = null;
		if (window.location.pathname === "/login.html") {
			typeLogin = 'customer';
		} else {
			typeLogin = 'vendor';
		}
		verifyOTPForLogin(typeLogin, phoneNumber.substring(1), logintotp, this.handleCallBackLVerifyLogin);
		localStorage.removeItem('login_otp')

	}

	handleCallBackLVerifyLogin = (data) => {
		if (data && data[0] && data[0].status && data[0].status === "error") {
			hideFogLoading();
			showToastMessage(Identify.__(data[0].message))
		} else {
			if (data.status && data.status === 'success' && data.customer_access_token) {
				hideFogLoading();
				Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, data.customer_identity);
				setToken(data.customer_access_token)
				this.props.simiSignedIn(data.customer_access_token);
				// getProfileAfterOtp(this.handleSendProfile.bind(this, data.customer_access_token));
			} else {
				hideFogLoading();
				showToastMessage('Invalid login !')
			}
		}
	}

	get phoneLoginForm() {
		const { isPhoneLogin } = this.state;
		const { classes } = this.props;
		const isOpen = isPhoneLogin;
		const className = isOpen ? classes.signIn_open : classes.signIn_closed;

		return (
			<div className={className}>
				<VerifyOtpModal
					openVerifyModal={this.state.openVerifyModal}
					closeVerifyModal={this.closeVerifyModal}
					callApi={(phonenumber) => this.handleVerifyLogin(phonenumber)}
				/>
				<PhoneLogin
					simiSignedIn={this.props.simiSignIn}
					openVModal={this.openVerifyOtpModal}
					closeVerifyModal={this.closeVerifyModal}
				// getUserDetails={}
				/>
			</div>
		);
	}

	authHandler = async (authData) => {
		var user = authData.user;
		var providerId = authData.additionalUserInfo.providerId;
		var profile = authData.additionalUserInfo.profile;
		var email = profile.email ? profile.email : null;
		var password = null;
		var firstname = null;
		var lastname = null;
		var telephone = null;
		var accessToken = authData.credential.accessToken;
		var accessTokenSecret = authData.credential.secret;
		var userSocialId = null;
		if (providerId === 'facebook.com') {
			userSocialId = profile.id;
			password = user.uid;
			firstname = profile.first_name;
			lastname = profile.last_name;
			telephone = user.phoneNumber ? user.phoneNumber : '';
		}

		if (providerId === 'google.com') {
			if (!email) {
				email = authData.user.email ? authData.user.email : null;
			}
			userSocialId = profile.id;
			password = user.uid;
			firstname = profile.given_name;
			lastname = profile.family_name;
			telephone = user.phoneNumber ? user.phoneNumber : '';
		}
		if (providerId === 'twitter.com') {
			userSocialId = profile.id_str;
			password = user.uid;
			firstname = profile.name;
			lastname = '';
			telephone = user.phoneNumber ? user.phoneNumber : '';
		}

		const accountInfo = {
			uid: user.uid,
			providerId: providerId,
			email: email,
			password: password,
			firstname: firstname,
			lastname: lastname,
			telephone: telephone,
			accessToken: accessToken,
			accessTokenSecret: accessTokenSecret,
			userSocialId: userSocialId
		};

		if (accountInfo) {
			Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, null);
			socialLoginApi(this.verifyDone, accountInfo);
			showFogLoading();
		}
	};

	verifyDone = (data) => {
		hideFogLoading();
		if (data.errors) {
			let errorMsg = '';
			if (data.errors.length) {
				data.errors.map((error) => {
					errorMsg += error.message;
				});
				showToastMessage(errorMsg);
			}
		} else {
			storage.removeItem('cartId');
			storage.removeItem('signin_token');
			if (data.customer_access_token) {
				Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, data.customer_identity);
				setToken(data.customer_access_token);
				this.props.simiSignedIn(data.customer_access_token);
			}
		}
	};

	authenticate = (provider) => {
		const authProvider = new firebase.auth[`${provider}AuthProvider`]();
		firebaseApp.auth().signInWithPopup(authProvider).then(this.authHandler);
	};

	get socialAndCreateAccount() {
		const { setCreateAccountForm } = this;
		const { isCreateAccountOpen, isForgotPasswordOpen } = this.state;
		const { classes } = this.props;
		return (
			<React.Fragment>
				<div
					className={`${isCreateAccountOpen || isForgotPasswordOpen
						? classes['inactive']
						: classes['active']}`}
				>
					<div className={`${classes['signInDivider']} ${Identify.isRtl() ? classes['rtl-divider'] : null}`}>
						<hr className={`${classes['hr']} ${classes['left-hr']}`} />
						<div className={`${classes['signInWidth']}`}>
							{Identify.__('or sign in with'.toUpperCase())}
						</div>
						<hr className={`${classes['hr']} ${classes['right-hr']}`} />
					</div>
					<div className={`${classes['socialMedia']}`}>
						<span className={`${classes['social-icon']}`} onClick={() => this.authenticate('Facebook')}>
							<span className={`${classes['icon']} ${classes['facebook']}`} />
						</span>
						<span className={`${classes['social-icon']}`} onClick={() => this.authenticate('Twitter')}>
							<span className={`${classes['icon']} ${classes['twitter']}`} />
						</span>
						<span
							className={`${classes['social-icon']} ${classes['special']}`}
							onClick={() => this.authenticate('Google')}
						>
							<span className={`${classes['icon']} ${classes['google']}`} />
						</span>
						{/* <span className={`${classes['social-icon']}`} onClick={() => this.authenticate('LinkedIn')}>
							<span className={`${classes['icon']} ${classes['linkedin']}`} />
						</span>
						<span className={`${classes['social-icon']}`} onClick={() => this.authenticate('Instagram')}>
							<span className={`${classes['icon']} ${classes['instagram']}`} />
						</span> */}
					</div>
					<div className={`${classes['showCreateAccountButtonCtn']}`}>
						<button
							priority="high"
							className={`${classes['showCreateAccountButton']}`}
							onClick={setCreateAccountForm}
							type="submit"
						>
							{Identify.__('Create an Account')}
						</button>
					</div>
				</div>
			</React.Fragment>
		);
	}

	createAccount = () => { };

	setCreateAccountForm = () => {
		this.createAccount = (className, history) => {
			return (
				<div className={className}>
					<CreateAccount onSignIn={this.onSignIn.bind(this)} history={history} />
				</div>
			);
		};
		this.showCreateAccountForm();
		$('#login-background').css('marginTop', '55px')
	};

	forgotPassword = () => { };

	setForgotPasswordForm = () => {
		this.forgotPassword = (className, history) => {
			return (
				<div className={className}>
					<ForgotPassword
						hideBuyer={this.hideBuyer}
						showBuyer={this.showBuyer}
						onClose={this.closeForgotPassword}
						history={history}
					/>
				</div>
			);
		};
		this.showForgotPasswordForm();
		$('#login-background').css('marginTop', '55px')
	};

	hideBuyer = () => {
		this.setState({ forgotPassSuccess: 'none' });
	};
	showBuyer = () => {
		this.setState({ forgotPassSuccess: 'block' });
	};

	closeForgotPassword = () => {
		this.hideForgotPasswordForm();
	};
	hideForgotPasswordForm = () => { };

	get createAccountForm() {
		const { isCreateAccountOpen } = this.state;
		const { history, classes } = this.props;
		const isOpen = isCreateAccountOpen;
		const className = isOpen ? classes.form_open : classes.form_closed;

		return this.createAccount(className, history);
	}

	get forgotPasswordForm() {
		const { isForgotPasswordOpen } = this.state;
		const { history, classes } = this.props;
		const isOpen = isForgotPasswordOpen;
		const className = isOpen ? classes.form_open : classes.form_closed;
		return this.forgotPassword(className, history);
	}

	showCreateAccountForm = () => {
		this.setState(() => ({
			isCreateAccountOpen: true,
			isEmailLogin: false,
			isForgotPasswordOpen: false,
			isPhoneLogin: false
		}));
	};

	showForgotPasswordForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: true,
			isEmailLogin: false,
			isCreateAccountOpen: false,
			isPhoneLogin: false
		}));
	};

	showEmailLoginForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: false,
			isEmailLogin: true,
			isCreateAccountOpen: false,
			isPhoneLogin: false
		}));
	};

	showPhoneLoginForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: false,
			isEmailLogin: false,
			isCreateAccountOpen: false,
			isPhoneLogin: true
		}));
	};

	onSignIn(username, password) {
		Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, null);
		signinApi(this.signinCallback.bind(this), { username, password });
		showFogLoading();
	}

	signinCallback = (data) => {
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
				}
			} else {
				let errorMsg = '';
				if (data.errors.length) {
					data.errors.map((error) => {
						if (error.endpoint == 'rest/V1/integration/customer/token') {
							errorMsg = 'Wrong password, does not exist account or account is not actived !';
						} else {
							errorMsg += error.message;
						}
					});
					showToastMessage(errorMsg);
				}
			}
		}
	};

	render() {
		const {
			createAccountForm,
			emailLoginForm,
			forgotPasswordForm,
			phoneLoginForm,
			socialAndCreateAccount,
			props,
			state
		} = this;
		const { isCreateAccountOpen, isForgotPasswordOpen, isEmailLogin, isPhoneLogin } = state;

		const { classes, isSignedIn, firstname, history } = props;

		if (isSignedIn) {
			if (history.location.hasOwnProperty('pushTo') && history.location.pushTo) {
				const { pushTo } = history.location;
				history.push(pushTo);
			} else {
				history.push('/');
			}
			smoothScrollToView($('#root'));
			const message = firstname
				? Identify.__('Welcome %s Start shopping now').replace('%s', firstname)
				: Identify.__('You have succesfully logged in, Start shopping now');
			if (this.props.toggleMessages)
				this.props.toggleMessages([{ type: 'success', message: message, auto_dismiss: true }]);
		}
		const showBackBtn = isCreateAccountOpen || isForgotPasswordOpen;

		return (
			<React.Fragment>
				{TitleHelper.renderMetaHeader({
					title: Identify.__('Customer Login')
				})}
				<div id="login-background" className={classes['login-background']}>
					<div
						className={` ${this.state.forgotPassSuccess == 'none'
							? classes['smallSize']
							: classes['']} ${classes['login-container']}`}
					>
						<div
							className={`${classes['buyer-login']}`}
							style={{ display: `${this.state.forgotPassSuccess}` }}
						>
							<span>{Identify.__('Buyer'.toUpperCase())}</span>
						</div>
						<div
							className={`${isCreateAccountOpen || isForgotPasswordOpen
								? classes['inactive']
								: classes['']} ${classes['select-type']}`}
						>
							<div
								onClick={this.showPhoneLoginForm}
								className={`${isPhoneLogin ? classes['active'] : null} ${classes['phone-type']}`}
							>
								<div className={`${classes['wrap']} ${Identify.isRtl() ? classes['rtl-wrap'] : null}`} >
									<span className={classes['icon-phone']} />
									<span className={classes['title-phone']}>{Identify.__('Phone')}</span>
								</div>
							</div>
							<div
								onClick={this.showEmailLoginForm}
								className={`${isEmailLogin ? classes['active'] : null} ${classes['email-type']}`}
							>
								<div className={`${classes['wrap']} ${Identify.isRtl() ? classes['rtl-wrap'] : null}`} >
									<span className={classes['icon-email']} />
									<span className={classes['title-email']}>{Identify.__('Email')}</span>
								</div>
							</div>
						</div>
						{emailLoginForm}
						{phoneLoginForm}
						{socialAndCreateAccount}
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
		lastname
	};
};

const mapDispatchToProps = {
	toggleMessages,
	simiSignedIn
};

export default compose(classify(defaultClasses), withRouter, connect(mapStateToProps, mapDispatchToProps))(Login);

async function setToken(token) {
	// TODO: Get correct token expire time from API
	return storage.setItem('signin_token', token, 3600);
}
