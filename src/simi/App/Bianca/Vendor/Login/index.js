import React, { Component } from 'react';
import defaultClasses from './login.css';
import classify from 'src/classify';
import Identify from 'src/simi/Helper/Identify';
import PhoneLogin from '../../Customer/Login/SignIn/Phone/PhoneLogin';
import VendorLogInForm from './VendorLogin';
import ForgotPassword from './ForgotPassword';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import { withRouter } from 'react-router-dom';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import { vendorLogin } from 'src/simi/Model/Customer';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import * as Constants from 'src/simi/Config/Constants';
import { Util } from '@magento/peregrine';
import { simiSignedIn } from 'src/simi/Redux/actions/simiactions';
import { showToastMessage } from 'src/simi/Helper/Message';
import VendorRegister from './VendorRegister';
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
		isEmailLogin: true,
		isForgotPasswordOpen: false,
		isPhoneLogin: false,
		isVendorRegisterOpen: false,
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
				<VendorLogInForm
					classes={classes}
					showVendorRegisterForm={this.setVendorRegisterForm}
					onForgotPassword={this.setForgotPasswordForm}
					onSignIn={this.onVendorLogin.bind(this)}
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
				smoothScrollToView($('#root'));
				let message = Identify.__('You have succesfully logged in !');
				if (this.props.toggleMessages)
					this.props.toggleMessages([{ type: 'success', message: message, auto_dismiss: true }]);
				window.location.href = data.redirect_url;
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

	get createAccount() {
		const { setVendorRegisterForm } = this;
		const { isVendorRegisterOpen, isForgotPasswordOpen } = this.state;
		const { history, classes } = this.props;
		return (
			<React.Fragment>
				<div
					className={`${isVendorRegisterOpen || isForgotPasswordOpen
						? classes['inactive']
						: classes['active']}`}
				>
					<div className={`${classes['showCreateAccountButtonCtn']}`}>
						<button
							priority="high"
							className={`${classes['showCreateAccountButton']}`}
							onClick={setVendorRegisterForm}
							type="submit"
						>
							{Identify.__('Create an Account')}
						</button>
					</div>
				</div>
			</React.Fragment>
		);
	}

	vendorRegister = () => { };

	setVendorRegisterForm = () => {
		this.vendorRegister = (className, history) => {
			return (
				<div className={className}>
					<VendorRegister onSignIn={this.onVendorLogin.bind(this)} history={history} />
				</div>
			);
		};
		this.showVendorRegisterForm();
		$('#login-background').css('marginTop', '55px')
	};

	forgotPassword = () => { };

	setForgotPasswordForm = () => {
		this.forgotPassword = (className, history) => {
			return (
				<div className={className}>
					<ForgotPassword
						hideDesigner={this.hideDesigner}
						showDesigner={this.showDesigner}
						onClose={this.closeForgotPassword}
						history={history}
					/>
				</div>
			);
		};
		this.showForgotPasswordForm();
		$('#login-background').css('marginTop', '55px')
	};

	hideDesigner = () => {
		this.setState({ forgotPassSuccess: 'none' });
	};
	showDesigner = () => {
		this.setState({ forgotPassSuccess: 'block' });
	};

	closeForgotPassword = () => {
		this.hideForgotPasswordForm();
	};
	hideForgotPasswordForm = () => { };

	get vendorRegisterForm() {
		const { isVendorRegisterOpen } = this.state;
		const { history, classes } = this.props;
		const isOpen = isVendorRegisterOpen;
		const className = isOpen ? classes.form_open : classes.form_closed;

		return this.vendorRegister(className, history);
	}

	get forgotPasswordForm() {
		const { isForgotPasswordOpen } = this.state;
		const { history, classes } = this.props;
		const isOpen = isForgotPasswordOpen;
		const className = isOpen ? classes.form_open : classes.form_closed;
		return this.forgotPassword(className, history);
	}

	showVendorRegisterForm = () => {
		this.setState(() => ({
			isEmailLogin: false,
			isForgotPasswordOpen: false,
			isPhoneLogin: false,
			isVendorRegisterOpen: true
		}));
	};

	showForgotPasswordForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: true,
			isEmailLogin: false,
			isPhoneLogin: false,
			isVendorRegisterOpen: false
		}));
	};

	showEmailLoginForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: false,
			isEmailLogin: true,
			isPhoneLogin: false,
			isVendorRegisterOpen: false
		}));
	};

	showPhoneLoginForm = () => {
		this.setState(() => ({
			isForgotPasswordOpen: false,
			isEmailLogin: false,
			isPhoneLogin: true,
			isVendorRegisterOpen: false
		}));
	};

	onVendorLogin(username, password) {
		Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, null);
		vendorLogin(this.vendorLoginCallback.bind(this), { username, password });
		showFogLoading();
	}

	vendorLoginCallback = (data) => {
		hideFogLoading();
		if (data && data.status === 'error') {
			let message = Identify.__(data.message);
			showToastMessage(message);
		} else {
			smoothScrollToView($('#root'));
			let message = Identify.__('You have succesfully logged in !');
			if (this.props.toggleMessages)
				this.props.toggleMessages([{ type: 'success', message: message, auto_dismiss: true }]);
			window.location.href = data.redirect_url;
		}
	};

	render() {
		const {
			emailLoginForm,
			forgotPasswordForm,
			phoneLoginForm,
			createAccount,
			vendorRegisterForm,
			props,
			state
		} = this;
		const { isVendorRegisterOpen, isForgotPasswordOpen, isEmailLogin, isPhoneLogin } = state;

		const { classes, isSignedIn, firstname, history } = props;

		return (
			<React.Fragment>
				{TitleHelper.renderMetaHeader({
					title: Identify.__('Designer Login')
				})}
				<div id="login-background" className={`${classes['login-background']} ${Identify.isRtl() ? classes['rtl-login-background'] : null}`}>
					<div
						className={` ${this.state.forgotPassSuccess == 'none'
							? classes['smallSize']
							: classes['']} ${classes['login-container']}`}
					>
						<div
							className={`${classes['designer-login']}`}
							style={{ display: `${this.state.forgotPassSuccess}` }}
						>
							<span>{Identify.__('Designer'.toUpperCase())}</span>
						</div>
						<div
							className={`${isVendorRegisterOpen || isForgotPasswordOpen
								? classes['inactive']
								: classes['']} ${classes['select-type']}`}
						>
							<div
								onClick={this.showPhoneLoginForm}
								className={`${isPhoneLogin ? classes['active'] : null} ${classes['phone-type']}`}
							>
								<div className={classes['wrap']}>
									<span className={classes['icon-phone']} />
									<span className={classes['title-phone']}>{Identify.__('Phone')}</span>
								</div>
							</div>
							<div
								onClick={this.showEmailLoginForm}
								className={`${isEmailLogin ? classes['active'] : null} ${classes['email-type']}`}
							>
								<div className={classes['wrap']}>
									<span className={classes['icon-email']} />
									<span className={classes['title-email']}>{Identify.__('Email')}</span>
								</div>
							</div>
						</div>
						{emailLoginForm}
						{phoneLoginForm}
						{createAccount}
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

export default compose(classify(defaultClasses), withRouter, connect(mapStateToProps, mapDispatchToProps))(Login);

async function setToken(token) {
	// TODO: Get correct token expire time from API
	return storage.setItem('signin_token', token, 3600);
}
