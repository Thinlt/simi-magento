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
		console.log(data);
		if (data.errors) {
			console.log('nooo');
			let errorMsg = '';
			if (data.errors.length) {
				data.errors.map((error) => {
					errorMsg += error.message;
				});
				showToastMessage(errorMsg);
			}
		} else {
			// props.onSignIn(registeringEmail, registeringPassword)
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
				{/* <h3 className={classes.lead}>
                    {`Check out faster, use multiple addresses, track
                            orders and more by creating an account!`}
                </h3> */}
				<Field label="First Name" required={true}>
					<TextInput
						field="firstname"
						autoComplete="given-name"
						validate={validators.get('firstName')}
						validateOnBlur
					/>
				</Field>
				<Field label="Last Name" required={true}>
					<TextInput
						field="lastname"
						autoComplete="family-name"
						validate={validators.get('lastName')}
						validateOnBlur
					/>
				</Field>
				<Field label="Vendor Id">
					<TextInput field="vendor.vendor_id" validate={validators.get('vendorId')} validateOnBlur />
				</Field>
				<Field label="Email" required={true}>
					<TextInput field="email" autoComplete="email" validate={validators.get('email')} validateOnBlur />
				</Field>
				<Field label="Password">
					<TextInput
						field="password"
						type="password"
						autoComplete="new-password"
						validate={validators.get('password')}
						validateOnBlur
					/>
				</Field>
				<Field label="Confirm Password">
					<TextInput field="confirm" type="password" validate={validators.get('confirm')} validateOnBlur />
				</Field>
				<Field label="Company">
					<TextInput field="vendor.company" />
				</Field>
				<Field label="Street Address">
					<TextInput field="vendor.street" />
				</Field>
				<Field label="City">
					<TextInput field="vendor.city" />
				</Field>
				<div className="form-row">
					<label htmlFor="input-country">{Identify.__('Country')}</label>
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
				<Field label="State/Province">
					<TextInput field="vendor.region" />
				</Field>
				<Field label="Zip/Postal Code">
					<TextInput field="vendor.postcode" />
				</Field>
				<Field label="Phone Number">
					<TextInput field="vendor.telephone" />
				</Field>
				<div className={classes.subscribe}>
					<Checkbox field="vendor.vendor_registration_agreement" label="I agree with" />
				</div>
				<div className={classes.error}>{errorMessage}</div>
				<div className={classes.actions}>
					<button
						priority="high"
						className={classes.submitButton}
						type="submit"
						style={{ backgroundColor: configColor.button_background, color: configColor.button_text_color }}
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
