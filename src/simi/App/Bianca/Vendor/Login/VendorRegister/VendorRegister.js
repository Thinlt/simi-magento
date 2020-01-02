import React from 'react';
import { shape, string } from 'prop-types';
import { Form, Option, asField, BasicSelect } from 'informed';

import Checkbox from 'src/components/Checkbox';
import Field from 'src/components/Field';
import TextInput from 'src/components/TextInput';
import { validators } from './validators';
import classes from './vendorRegister.css';
import { configColor } from 'src/simi/Config';
import Identify from 'src/simi/Helper/Identify';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import { vendorRegister } from 'src/simi/Model/Customer';
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';

const VendorRegister = (props) => {
	const { history, createAccountError } = props;
	const errorMessage =
		createAccountError && Object.keys(createAccountError).length !== 0
			? Identify.__('An error occurred. Please try again.')
			: null;
	let registeringEmail = null;
	let registeringPassword = null;

	const $ = window.$;
	$('#siminia-main-page').css('min-height', '100vh');

	const storeConfig = Identify.getStoreConfig();
	const countries = storeConfig.simiStoreConfig.config.allowed_countries;

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

	const handleSubmit = (values) => {
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

		// console.log(params.vendor_data[vendor_id])
		// console.log(values)
		showFogLoading();
		registeringEmail = values.email;
		registeringPassword = values.password;
		vendorRegister(registerDone, params);
	};

	const registerDone = (data) => {
		hideFogLoading();
		if (data && data.status === 'error') {
			let message = Identify.__(data.message);
			showToastMessage(message);
		} else {
			let message = Identify.__(data.message);
			showToastMessage(message);
		}
	};

	const handleBack = () => {
		history.push('/designer_login.html');
	};

	return (
		<React.Fragment>
			{TitleHelper.renderMetaHeader({
				title: Identify.__('Create Account')
			})}
			<Form className={classes.root} initialValues={initialValues} onSubmit={handleSubmit}>
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
						placeholder="First Name"
					/>
				</Field>
				<Field label="Last Name *" required={true}>
					<TextInput
						field="lastname"
						autoComplete="family-name"
						validate={validators.get('lastName')}
						validateOnBlur
						placeholder="Last Name"
					/>
				</Field>
				<Field label="Vendor Id *">
					<TextInput
						field="vendor.vendor_id"
						validate={validators.get('vendorId')}
						validateOnBlur
						placeholder="Vendor Id"
					/>
				</Field>
				<Field label="Email *" required={true}>
					<TextInput
						field="email"
						autoComplete="email"
						validate={validators.get('email')}
						validateOnBlur
						placeholder="Email"
					/>
				</Field>
				<div className={classes.form_row}>
					<label htmlFor="input-country">{Identify.__('Country *')}</label>
					<SimiSelect
						id="input-country"
						field="vendor.country_id"
						initialValue={'US'}
						// validate={(value) => validateOption(value, addressConfig && addressConfig.country_id_show || 'req')}
						validateOnChange
					>
						{countries.map((country, index) => {
							return country.country_name !== null ? (
								<Option value={`${country.country_code}`} key={index}>
									{country.country_name}
								</Option>
							) : null;
						})}
					</SimiSelect>
				</div>
				{/* <Field label="Zip/Postal Code *">
					<TextInput field="vendor.postcode" validate={validators.get('postcode')} placeholder="Zip Code" />
				</Field> */}
				<Field label="City *">
					<TextInput field="vendor.city" validate={validators.get('city')} placeholder="City" />
				</Field>
				<Field label="Region *">
					<TextInput field="vendor.region" validate={validators.get('region')} placeholder="Region" />
				</Field>
				<Field label="Phone Number *">
					<TextInput field="vendor.telephone" validate={validators.get('telephone')} placeholder="Phone" />
				</Field>
				<Field label="Website *">
					<TextInput field="vendor.website" validate={validators.get('website')} />
				</Field>
				<Field label="Facebook *">
					<TextInput field="vendor.facebook" validate={validators.get('facebook')} />
				</Field>
				<Field label="Instagram *">
					<TextInput field="vendor.instagram" validate={validators.get('instagram')} />
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
					<button
						priority="high"
						className={classes.submitButton}
						type="submit"
					>
						{Identify.__('Submit')}
					</button>
				</div>
				<div className={classes['back']} onClick={handleBack}>
					<span>{Identify.__('back'.toUpperCase())}</span>
				</div>
			</Form>
		</React.Fragment>
	);
};

VendorRegister.propTypes = {
	createAccountError: shape({
		message: string
	})
};

VendorRegister.defaultProps = {
	initialValues: {}
};

export default VendorRegister;
