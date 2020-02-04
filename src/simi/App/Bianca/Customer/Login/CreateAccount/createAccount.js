import React, { useState, useEffect } from 'react';
import { shape, string } from 'prop-types';
import { Form } from 'informed';

import Field from 'src/components/Field';
import TextInput from 'src/components/TextInput';
import { validators } from './validators';
import classes from './createAccount.css';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import { createAccount } from 'src/simi/Model/Customer'
import { showToastMessage } from 'src/simi/Helper/Message';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import VerifyForm from 'src/simi/App/Bianca/Components/Otp/VerifyForm';
import GetOtpModal from 'src/simi/App/Bianca/Components/Otp/GetOtpModal';
import { sendOTPForRegister, verifyOTPForRegister } from 'src/simi/Model/Otp';
import VerifyOtpModal from 'src/simi/App/Bianca/Components/Otp/VerifyOtpModal';
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { connect } from 'src/drivers';

const $ = window.$;

const CreateAccount = props => {
    const { history, createAccountError } = props;
    const errorMessage = createAccountError && (Object.keys(createAccountError).length !== 0) ? Identify.__('An error occurred. Please try again.') : null
    const [allowSubmit, setAllowSubmit] = useState(false)
    const [phoneRegister, setPhone] = useState("")
    const [showModalGet, setModalGet] = useState(false)
    const [showModalVerify, setModalVerify] = useState(false)
    var registeringEmail = null
    var registeringPassword = null

    const initialValues = () => {
        const { initialValues } = props;
        const { email, firstName, lastName, ...rest } = initialValues;

        return {
            customer: { email, firstname: firstName, lastname: lastName },
            ...rest
        };
    }

    const handleSubmit = values => {
        values.customer.telephone = phoneRegister.substring(1)
        const params = {
            password: values.password,
            confirm_password: values.confirm,
            ...values.customer,
            news_letter: values.subscribe ? 1 : 0
        }
        const merchant = Identify.getStoreConfig();
        if (merchant && merchant.hasOwnProperty('storeConfig') && merchant.storeConfig) {
            const { website_id } = merchant.storeConfig;
            if (website_id) {
                params['website_id'] = website_id;
            }
        }
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
            registeringEmail = values.customer.email
            registeringPassword = values.password
            createAccount(registerDone, params)
        }
    };

    const registerDone = (data) => {
        hideFogLoading()
        if (data.errors) {
            let errorMsg = ''
            if (data.errors.length) {
                data.errors.map(error => {
                    errorMsg += error.message
                })
                let message = Identify.__(errorMsg);
                if (errorMsg === 'Account confirmation is required. Please, check your email !') {
                    smoothScrollToView($('#id-message'));
                    showToastMessage(message)
                    // props.toggleMessages([{ type: 'success', message: message, auto_dismiss: true }]);
                    // Reset form
                    $('.form-create-account')[0].reset();
                    // Make button opacity = 1
                    $('.form-create-account button').css('opacity', '1');
                } else {
                    showToastMessage(message)
                }
            }
        } else {

        }
        setAllowSubmit(false)
    }

    const handleBack = () => {
        history.push('/login.html');
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
                title: Identify.__('Create Account')
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
                className={`form-create-account ${classes.root} ${Identify.isRtl() ? classes['rtl-rootForm'] : null}`}
                initialValues={initialValues}
                onSubmit={handleSubmit}
            >
                <div className={classes.lead1}>
                    {Identify.__('create an account'.toUpperCase())}
                </div>
                <div className={classes.lead2}>
                    {Identify.__('Please enter the following information to create your account.')}
                </div>
                <Field label="First Name *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.firstname"
                        autoComplete="given-name"
                        validate={validators.get('firstName')}
                        validateOnBlur
                        placeholder="First Name"
                    />
                </Field>
                <Field label="Last Name *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.lastname"
                        autoComplete="family-name"
                        validate={validators.get('lastName')}
                        validateOnBlur
                        placeholder="Last Name"
                    />
                </Field>
                <Field label="Email Address *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.email"
                        autoComplete="email"
                        validate={validators.get('email')}
                        validateOnBlur
                        placeholder="Email"
                    />
                </Field>
                {/* <Field label="Phone Number *" required={true}>
                    <TextInput
                        classes={classes}
                        field="customer.telephone"
                        validate={validators.get('telephone')}
                        validateOnBlur
                        placeholder="Phone"
                    />
                </Field> */}
                <VerifyForm
                    openGetModal={openGetModal}
                    handleVerify={handleVerifyRegister}
                    handleChangePhone={(val1, val2) => onChange(val1, val2)}
                    type={'login'}
                />
                <Field label="Password *">
                    <TextInput
                        classes={classes}
                        field="password"
                        type="password"
                        autoComplete="new-password"
                        validate={validators.get('password')}
                        validateOnBlur
                        placeholder="Password"
                    />
                </Field>
                <Field label="Password Confirmation*">
                    <TextInput
                        field="confirm"
                        type="password"
                        validate={validators.get('confirm')}
                        validateOnBlur
                        placeholder="Password confirmation"
                    />
                </Field>
                <div className={classes.error}>{errorMessage}</div>
                <div className={classes.actions}>
                    <button
                        priority="high" className={classes.submitButton} type="submit"
                    >
                        {Identify.__('Register')}
                    </button>
                </div>
                <div
                    className={`special-back ${classes['back']}`}
                    id="btn-back"
                    onClick={handleBack}
                >
                    <span>{Identify.__('back'.toUpperCase())}</span>
                </div>
            </Form>
        </React.Fragment>
    );
}

const mapDispatchToProps = {
    toggleMessages
};

CreateAccount.propTypes = {
    createAccountError: shape({
        message: string
    }),
    initialValues: shape({
        email: string,
        firstName: string,
        lastName: string
    })
}

CreateAccount.defaultProps = {
    initialValues: {}
};

export default connect(null, mapDispatchToProps)(CreateAccount);
