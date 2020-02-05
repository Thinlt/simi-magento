import React, { useState, useLayoutEffect } from 'react';
import { shape, string } from 'prop-types';
import { Form, Option, asField, BasicSelect, useFieldState } from 'informed';

import Field from 'src/components/Field';
import TextInput from 'src/components/TextInput';
import { validators } from './validators';
import classes from './vendorRegister.css';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { vendorRegister } from 'src/simi/Model/Customer';
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { validateEmpty } from 'src/simi/Helper/Validation';
import { red } from '@material-ui/core/colors';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import VerifyForm from 'src/simi/App/Bianca/Components/Otp/VerifyForm';
import GetOtpModal from 'src/simi/App/Bianca/Components/Otp/GetOtpModal';
import { sendOTPForRegister, verifyOTPForRegister } from 'src/simi/Model/Otp';
import VerifyOtpModal from 'src/simi/App/Bianca/Components/Otp/VerifyOtpModal';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';

const VendorRegister = (props) => {
	const [firstName, setName] = useState('');
	const { history, createAccountError } = props;
	const errorMessage =
		createAccountError && Object.keys(createAccountError).length !== 0
			? Identify.__('An error occurred. Please try again.')
			: null;
	const [allowSubmit, setAllowSubmit] = useState(false)
	const [phoneRegister, setPhone] = useState("")
	const [showModalGet, setModalGet] = useState(false)
	const [showModalVerify, setModalVerify] = useState(false)
	let registeringEmail = null;
	let registeringPassword = null;

	const $ = window.$;
	$('#siminia-main-page').css('min-height', '100vh');

	const storeConfig = Identify.getStoreConfig();
	const countries = storeConfig.simiStoreConfig.config.allowed_countries;
	const [selectedCountry, setCountry] = useState('');
	const SimiSelect = asField(({ fieldState, ...props }) => (
		<React.Fragment>
			<BasicSelect
				fieldState={fieldState}
				{...props}
				style={fieldState.error ? { border: 'solid 1px red' } : null}
			/>
			{fieldState.error ? <small style={{ color: 'red' }}>{fieldState.error}</small> : null}
		</React.Fragment>
	));

	const initialValues = () => {
		const { initialValues } = props;
		const {
			vendorId,
			company,
			street,
			city,
			countryId,
			region,
			postcode,
			telephone,
			vendorAgreement,
			...rest
		} = initialValues;

		return {
			vendor: {
				vendor_id: vendorId,
				company,
				street,
				city,
				country_id: countryId,
				region,
				postcode,
				telephone,
				vendor_registration_agreement: vendorAgreement
			},
			...rest
		};
	};

	const validateOption = (value, opt) => {
		if (opt === 'req') {
			return !value || !validateEmpty(value) ? 'Please select an option.' : undefined;
		}
		return undefined;
	};

	const hideArrow = () => {
		// show arrow down
		const form = $('#root-designer');
		// region
		var arrowUp = form.find(`.${classes['arrow_up']}`);
		var arrowDown = form.find(`.${classes['arrow_down']}`);
		if (arrowUp.hasClass('show')) {
			arrowUp.removeClass('show');
			arrowUp.addClass('hidden');
			arrowDown.removeClass('hidden');
			arrowDown.addClass('show');
		}
	};

	const hideArrow1 = () => {
		// show arrow down
		const form = $('#root-designer');
		// country
		var arrowUp1 = form.find(`.${classes['arrow_up1']}`);
		var arrowDown1 = form.find(`.${classes['arrow_down1']}`);
		if (arrowUp1.hasClass('show')) {
			arrowUp1.removeClass('show');
			arrowUp1.addClass('hidden');
			arrowDown1.removeClass('hidden');
			arrowDown1.addClass('show');
		}
	};

	useLayoutEffect(function () {
		const form = $('#root-designer');
		var open = form.find(`.${classes['open']}`);
		// show arrow up
		// country
		var arrowUp1 = form.find(`.${classes['arrow_up1']}`);
		var arrowDown1 = form.find(`.${classes['arrow_down1']}`);
		$('#input-country').on({
			mousedown: function () {
				if (open) {
					if (arrowUp1.hasClass('hidden')) {
						arrowUp1.removeClass('hidden');
						arrowUp1.addClass('show');
						arrowDown1.removeClass('show');
						arrowDown1.addClass('hidden');
					}
				}
			},
			change: function () {
				effectArrowRegion();
			}
		});
	});
	const effectArrowRegion = () => {
		// show arrow up
		const form = $('#root-designer');
		$(document).ready(function () {
			// region
			var openR = form.find(`.${classes['openR']}`);
			var arrowUp = form.find(`.${classes['arrow_up']}`);
			var arrowDown = form.find(`.${classes['arrow_down']}`);
			var region = form.find(`${'#input-region'}`);
			if (region) {
				region.on({
					mousedown: function () {
						if (openR) {
							if (arrowUp.hasClass('hidden')) {
								arrowUp.removeClass('hidden');
								arrowUp.addClass('show');
								arrowDown.removeClass('show');
								arrowDown.addClass('hidden');
							}
						}
					}
				});
			}
		});
	};

	const Regions = () => {
		// get selected country
		var country;
		var selectedCountry = useFieldState('vendor.country_id');
		for (var i in countries) {
			if (countries[i].country_code === selectedCountry.value) {
				country = countries[i];
				break;
			}
		}
		if (country && country.states && country.states.length) {
			var regionValue = null;
			return (
				<div className={classes.form_row}>
					<label className={classes.select} htmlFor="input-region">{Identify.__('Region *')}</label>
					<label className={`${classes.arrow_down} show`} htmlFor="input-region" />
					<label className={`${classes.arrow_up} hidden`} htmlFor="input-region" />
					<SimiSelect
						id="input-region"
						field="vendor.region"
						initialValue={regionValue}
						validate={(value) => validateOption(value, 'req')}
						validateOnChange
						onValueChange={() => hideArrow()}
					>
						<Option value="" key={-1}>
							{Identify.__('Region')}
						</Option>
						{country.states.map((region, index) => {
							return region.state_id !== null ? (
								<Option className="openR" value={`${region.state_name}`} key={index}>
									{Identify.__(region.state_name)}
								</Option>
							) : null;
						})}
					</SimiSelect>
				</div>
			);
		} else {
			var regionValue = null;
			return (
				<Field label="Region *">
					<TextInput
						field="vendor.region"
						validate={validators.get('region')}
						placeholder="Region"
						validateOnBlur
					/>
				</Field>
			);
		}
	};

	const handleSubmit = (values) => {
		values.vendor.telephone = phoneRegister.substring(1)
		const merchant = Identify.getStoreConfig();
		if (merchant && merchant.hasOwnProperty('storeConfig') && merchant.storeConfig) {
			const { website_id } = merchant.storeConfig;
			if (website_id) {
				values.vendor.website_id = website_id;
			}
		}
		const params = {
			email: values.email,
			firstname: values.firstname,
			lastname: values.lastname,
			password: values.password,
			confirm_password: values.confirm,
			vendor_data: {
				...values.vendor
			},
			// vendor_id: values.vendorId,
			vendor_registration_agreement: values.vendor_registration_agreement ? 1 : 0
		};
		if (!allowSubmit) {
			$('#must-verify').css('display', 'block')
			$('#createAccount').css('backgroundColor', '#B91C1C')
			$('#verify-opt-area .wrap').css('float', 'unset')
			// do nothing
		} else {
			$('#must-verify').css('display', 'none')
			$('#createAccount').css('backgroundColor', '#101820')
			$('#verify-opt-area .wrap').css('float', 'right')
			showFogLoading()
			registeringEmail = values.email;
			registeringPassword = values.password;
			vendorRegister(registerDone, params);
		}
	};

	const registerDone = (data) => {
		hideFogLoading();
		if (data && data.status === 'error') {
			let message = Identify.__(data.message);
			showToastMessage(message);
		} else {
			let message = Identify.__(data.message);
			smoothScrollToView($('#id-message'));
			showToastMessage(message);
			// props.toggleMessages([{ type: 'success', message: message, auto_dismiss: true }]);
			// Reset form
			$('.form-create-account-vendor')[0].reset();
			// Set opacity button register = 1 after register done
			$('.form-create-account-vendor button').css('opacity', '1');
		}
		setAllowSubmit(false)
	};

	const handleBack = () => {
		history.push('/designer_login.html');
	};

	const handleSendOtp = () => {
		let phone = phoneRegister;
		// close get modal
		closeGetModal()
		$('#must-verify').css('display', 'none')
		$('#createAccount').css('backgroundColor', '#101820')
		$('#verify-opt-area .wrap').css('float', 'right')

		showFogLoading()
		phone = phone.replace(/[- )(]/g, '').replace(/\+/g, "").replace(/\,/g, "");
		var phoneNB = phone
		let params = {
			mobile: phone
		}
		const merchant = Identify.getStoreConfig();
		if (merchant && merchant.hasOwnProperty('storeConfig') && merchant.storeConfig) {
			const { website_id } = merchant.storeConfig;
			if (website_id) {
				params['website_id'] = website_id;
			}
		}
		sendOTPForRegister(params, handleCallBackSendOTP)
	}

	const handleCallBackSendOTP = (data) => {
		hideFogLoading();
		if (data && data.result && (data.result == "exist")) {
			hideFogLoading();
			showToastMessage(Identify.__('Already exist account with this phone number !'))
		} else {
			// Always run here, allow exist phone number, only check real number phone.
			hideFogLoading();
			localStorage.setItem("numberphone_register", phoneRegister);
			// Open modal verify otp
			openVModal();
			setTimeout(() => closeVModal(), 120000);
		}
	}

	const openVModal = () => {
		setModalVerify(true)
	}

	const closeVModal = () => {
		setModalVerify(false)
	}

	const handleVerifyRegister = () => {
		let logintotp = localStorage.getItem('login_otp');
		$('#login-input-otp-warning').css({ display: 'none' })
		showFogLoading();
		verifyOTPForRegister(phoneRegister.substring(1), logintotp, handleCallBackLVerifyRegister);
		localStorage.removeItem('login_otp')
	}

	const handleCallBackLVerifyRegister = (data) => {
		if (data && data.result && (data.result == "true")) {
			hideFogLoading();
			setAllowSubmit(true)
			showToastMessage(Identify.__('Phone number is Valid !'))
		} else {
			hideFogLoading();
			showToastMessage(Identify.__('Verify OTP fail !'))
		}
	}

	const onChange = (val1, val2) => {
		$('#verify-opt-area #number_phone-invalid').css({ display: 'none' })
		let value = val1 + val2
		setPhone(value)
		setAllowSubmit(false)
		localStorage.setItem("numberphone_register", value);
	}

	const openGetModal = () => {
		if (phoneRegister.length < 10) {

		} else {
			setModalGet(true)
		}
	}

	const closeGetModal = () => {
		localStorage.removeItem("numberphone_register");
		setModalGet(false)
	}

	return (
		<React.Fragment>
			{TitleHelper.renderMetaHeader({
				title: Identify.__('Create Designer Account')
			})}
			<GetOtpModal
				openGetModal={showModalGet}
				closeGetModal={closeGetModal}
				senOtpRegister={handleSendOtp}
			/>
			<VerifyOtpModal
				openVerifyModal={showModalVerify}
				closeVerifyModal={closeVModal}
				callApi={(phonenumber) => handleVerifyRegister(phonenumber)}
			/>
			<Form
				id="root-designer"
				className={`form-create-account-vendor ${classes.root} ${Identify.isRtl() ? classes['rtl-rootForm'] : null}`}
				onSubmit={handleSubmit}
			>
				<React.Fragment>
					<div className={classes.lead1}>{Identify.__('create an account'.toUpperCase())}</div>
					<div className={classes.lead2}>
						{Identify.__('Please enter the following information to create your account.')}
					</div>
					<Field label="First Name *" required={true}>
						<TextInput
							field="firstname"
							autoComplete="given-name"
							validate={validators.get('firstName')}
							validateOnBlur
							placeholder="First name"
						/>
					</Field>
					<Field label="Last Name *" required={true}>
						<TextInput
							field="lastname"
							autoComplete="family-name"
							validate={validators.get('lastName')}
							validateOnBlur
							placeholder="Last name"
						/>
					</Field>
					<Field label="Designer Id *">
						<TextInput
							field="vendor.vendor_id"
							validate={validators.get('vendorId')}
							validateOnBlur
							placeholder="Designer ID"
						/>
					</Field>
					<Field label="Email address *" required={true}>
						<TextInput
							field="email"
							autoComplete="email"
							validate={validators.get('email')}
							validateOnBlur
							placeholder="Email"
						/>
					</Field>
					<div className={classes.form_row}>
						<label className={classes.select} htmlFor="input-country">{Identify.__('Country *')}</label>
						<label className={`${classes.arrow_down1} show`} htmlFor="input-country" />
						<label className={`${classes.arrow_up1} hidden`} htmlFor="input-country" />
						<SimiSelect
							id="input-country"
							field="vendor.country_id"
							validate={(value) => validateOption(value, 'req')}
							validateOnChange
							onValueChange={() => hideArrow1()}
						>
							<Option value="" key={-1}>
								{Identify.__('Country')}
							</Option>
							{countries.map((country, index) => {
								return country.country_name !== null ? (
									<Option className="open" value={`${country.country_code}`} key={index}>
										{Identify.__(country.country_name)}
									</Option>
								) : null;
							})}
						</SimiSelect>
					</div>
					<Field label="City *">
						<TextInput
							field="vendor.city"
							validate={validators.get('city')}
							placeholder="City"
							validateOnBlur
						/>
					</Field>
					<Regions />
					{/* <Field label="Phone Number *">
						<TextInput
							field="vendor.telephone"
							validate={validators.get('telephone')}
							placeholder="Phone"
							validateOnBlur
						/>
					</Field> */}
					<VerifyForm
						openGetModal={openGetModal}
						handleVerify={handleVerifyRegister}
						handleChangePhone={(val1, val2) => onChange(val1, val2)}
						type={'login'}
					/>
					<Field label="Website *">
						<TextInput field="vendor.website" validate={validators.get('website')} validateOnBlur />
					</Field>
					<Field label="Facebook *">
						<TextInput field="vendor.facebook" validate={validators.get('facebook')} validateOnBlur />
					</Field>
					<Field label="Instagram *">
						<TextInput field="vendor.instagram" validate={validators.get('instagram')} validateOnBlur />
					</Field>
					<Field label="Password *">
						<TextInput
							field="password"
							type="password"
							autoComplete="new-password"
							validate={validators.get('password')}
							validateOnBlur
							placeholder="Password"
						/>
					</Field>
					<Field label="Password Confirmation *">
						<TextInput
							field="confirm"
							type="password"
							validate={validators.get('confirm')}
							validateOnBlur
							placeholder="Password Confirmation"
						/>
					</Field>
					<div className={classes.error}>{errorMessage}</div>
					<div className={classes.actions}>
						<button priority="high" className={classes.submitButton} type="submit">
							{Identify.__('Register')}
						</button>
					</div>
					<div className={`special-back ${classes['back']}`} onClick={handleBack} id="btn-back">
						<span>{Identify.__('back'.toUpperCase())}</span>
					</div>
				</React.Fragment>
			</Form>
		</React.Fragment>
	);
};

const mapDispatchToProps = {
	toggleMessages
};

VendorRegister.propTypes = {
	createAccountError: shape({
		message: string
	})
};

VendorRegister.defaultProps = {
	initialValues: {}
};

export default connect(null, mapDispatchToProps)(VendorRegister);
