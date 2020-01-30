import React, { Component } from 'react';
import { bool, func } from 'prop-types';
import { Form } from 'informed';
import TextInput from 'src/components/TextInput';
import { isRequired } from 'src/util/formValidators';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import Checkbox from 'src/simi/BaseComponents/Checkbox';

require('./vendorLogin.scss');

const $ = window.$;

class VendorLogin extends Component {
	state = {
		isSeleted: false
	};

	componentDidMount() {
		$('#siminia-main-page').css('min-height', 'unset');
	}

	handleCheckBox = () => {
		this.setState({ isSeleted: !this.state.isSeleted });
	};

	static propTypes = {
		isGettingDetails: bool,
		onForgotPassword: func.isRequired,
		signIn: func
	};

	componentDidMount() {
		var savedUser = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_email');
		var savedPassword = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_password');
		if (savedUser && savedPassword) {
			this.setState({ isSeleted: true })
			// Prepare decode password and fill username and password into form (remember me)
			var crypto = require('crypto-js');
			var bytes = crypto.AES.decrypt(savedPassword, '@_1_namronaldomessi_privatekey_$');
			// Decode password to plaintext
			var plaintextPassword = bytes.toString(crypto.enc.Utf8);
			this.formApi.setValue('email', savedUser);
			this.formApi.setValue('password', plaintextPassword);
		}
	}

	render() {
		const { isSeleted } = this.state;
		const { classes } = this.props;

		return (
			<div className={`root sign-in-form ${Identify.isRtl() ? 'rtl-signInForm' : null}`}>
				{TitleHelper.renderMetaHeader({
					title: Identify.__('Sign In')
				})}
				<Form className="form" getApi={this.setFormApi} onSubmit={() => this.onSignIn()}>
					<div className="userInput">
						<div className="title-input">{Identify.__('EMAIL *')}</div>
						<TextInput
							classes={classes}
							style={{ paddingLeft: '56px' }}
							autoComplete="email"
							field="email"
							validate={isRequired}
							validateOnBlur
							placeholder="Email"
						/>
					</div>
					<div className="passwordInput">
						<div className="title-input">{Identify.__('PASSWORD *')}</div>
						<TextInput
							classes={classes}
							style={{ paddingLeft: '56px' }}
							autoComplete="current-password"
							field="password"
							type="password"
							validate={isRequired}
							validateOnBlur
							placeholder="Password"
						/>
					</div>
					<div className={`${Identify.isRtl() ? 'rtl-signInAction' : null} signInAction`}>
						<Checkbox
							onClick={this.handleCheckBox}
							label={Identify.__('Remember me')}
							selected={isSeleted}
						/>
						<button type="button" className="forgotPassword" onClick={this.handleForgotPassword}>
							{Identify.__('Forgot password')}
						</button>
					</div>
					<div className="signInButtonCtn">
						<button
							priority="high"
							className="signInButton"
							type="submit"
							style={{ backgroundColor: '#101820', color: '#fff' }}
						>
							{Identify.__('Sign In'.toUpperCase())}
						</button>
					</div>
				</Form>
			</div>
		);
	}

	handleForgotPassword = () => {
		this.props.onForgotPassword();
	};

	onSignIn() {
		const username = this.formApi.getValue('email');
		const password = this.formApi.getValue('password');

		if (this.state.isSeleted === true) {
			// Import extension crypto to encode password
			var crypto = require('crypto-js');
			// Encode password
			var hashedPassword = crypto.AES.encrypt(password, '@_1_namronaldomessi_privatekey_$').toString();
			// Save username to local storage 
			Identify.storeDataToStoreage(
				Identify.LOCAL_STOREAGE,
				'user_email',
				username
			);
			// Save hashed password to local storage
			Identify.storeDataToStoreage(
				Identify.LOCAL_STOREAGE,
				'user_password',
				hashedPassword
			);
		} else {
			var savedUser = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_email');
			var savedPassword = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_password');
			if (savedUser && savedPassword) {
				localStorage.removeItem('user_email');
				localStorage.removeItem('user_password');
			}
		}

		this.props.onSignIn(username, password);
	}

	setFormApi = (formApi) => {
		this.formApi = formApi;
	};

	showVendorRegisterForm = () => {
		this.props.showVendorRegisterForm();
	};
}

export default VendorLogin;
