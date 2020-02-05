import React, { useEffect, useState } from 'react';

import TextBox from 'src/simi/BaseComponents/TextBox';
import Identify from 'src/simi/Helper/Identify';
import Checkbox from 'src/simi/BaseComponents/Checkbox';
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import { editCustomer } from 'src/simi/Model/Customer';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading'
import validator from 'validator'
import { smoothScrollToView } from 'src/simi/Helper/Behavior';
import PhoneInputReadonly from './PhoneInputReadonly';
import PhoneInputVerify from './PhoneInputVerify';
import { sendOTPForRegister, verifyOTPForRegister } from 'src/simi/Model/Otp';
import GetOtpModal from 'src/simi/App/Bianca/Components/Otp/GetOtpModal';
import VerifyOtpModal from 'src/simi/App/Bianca/Components/Otp/VerifyOtpModal';
import { showToastMessage } from 'src/simi/Helper/Message';
const $ = window.$;

const ProfileForm = props => {
    const { history, isPhone, data } = props;
    const { custom_attributes } = data
    var telephone = null
    if (custom_attributes && custom_attributes["0"] && custom_attributes["0"].attribute_code == "mobilenumber") {
        telephone = custom_attributes["0"].value
    }
    // const [data, setData] = useState(data);
    const [changeForm, handleChangeForm] = useState(false);
    const [isChangePass, setChangePass] = useState(false);
    const [allowSubmit, setAllowSubmit] = useState(true)
    const [phoneChange, setPhone] = useState("")
    const [showModalGet, setModalGet] = useState(false)
    const [showModalVerify, setModalVerify] = useState(false)

    useEffect(() => {
        if (
            history.location.state
            && history.location.state.hasOwnProperty('profile_edit')
            && history.location.state.profile_edit
        ) {
            const { profile_edit } = history.location.state;
            handleChangeForm(profile_edit);
        }
    }, [0])

    const scorePassword = pass => {
        let score = 0;
        if (!pass)
            return score;

        // award every unique letter until 5 repetitions
        let letters = {};
        for (let i = 0; i < pass.length; i++) {
            letters[pass[i]] = (letters[pass[i]] || 0) + 1;
            score += 5.0 / letters[pass[i]];
        }

        // bonus points for mixing it up
        let variations = {
            digits: /\d/.test(pass),
            lower: /[a-z]/.test(pass),
            upper: /[A-Z]/.test(pass),
            nonWords: /\W/.test(pass),
        }

        let variationCount = 0;
        for (let check in variations) {
            variationCount += (variations[check] === true) ? 1 : 0;
        }
        score += (variationCount - 1) * 10;

        return parseInt(score, 10);
    }

    const checkPassStrength = pass => {
        let score = scorePassword(pass);
        switch (true) {
            case score > 70:
                return "Strong";
            case score > 50:
                return "Good";
            case (score >= 30):
                return "Weak";
            default:
                return "no password"
        }
    }

    const handleOnChange = (e) => {
        if (e.target.name === 'new_password') {
            let str = checkPassStrength(e.target.value);
            $('#strength-value').html(Identify.__(str))
        }
        if (e.target.value !== "" || e.target.value !== null) {
            $(e.target).removeClass("is-invalid");
        }
    }

    const validateForm = () => {
        let formCheck = true;
        let msg = "";
        $("#harlows-edit-profile")
            .find(".required")
            .each(function () {
                if ($(this).val() === "" || $(this).val().length === 0) {
                    formCheck = false;
                    $(this).addClass("is-invalid");
                    msg = Identify.__("Please check some required fields");
                } else {
                    $(this).removeClass("is-invalid");
                    let new_pass_val = $("#harlows-edit-profile").find('input[name="new_password"]').val();
                    if ($(this).attr("name") === "email" || $(this).attr("name") === "new_email") {
                        if (!Identify.validateEmail($(this).val())) {
                            formCheck = false;
                            $(this).addClass("is-invalid");
                            msg = Identify.__("Email field is invalid");
                        }
                    }
                    if ($(this).attr("name") === "telephone") {
                        if (!validator.isMobilePhone($(this).val())) {
                            formCheck = false;
                            $(this).addClass("is-invalid");
                            msg = Identify.__("Phone number is invalid");
                        }
                    }
                    if ($(this).attr("name") === "new_password" && new_pass_val && new_pass_val.length < 6) {
                        formCheck = false;
                        $(this).addClass("is-invalid");
                        msg = Identify.__("Password need least 6 characters!");
                    }
                    if ($(this).attr("name") === "com_password") {
                        if (
                            $(this).val() !== new_pass_val) {
                            formCheck = false;
                            $(this).addClass("is-invalid");
                            msg = Identify.__("Confirm password is not match");
                        }
                    }
                }
            });

        if (!formCheck) {
            smoothScrollToView($("#id-message"));
            props.toggleMessages([{ type: 'error', message: msg, auto_dismiss: true }]);
        }

        return formCheck;
    };

    const processData = (data) => {
        smoothScrollToView($("#id-message"));
        if (data.hasOwnProperty('errors') && data.errors) {
            const messages = data.errors.map(value => {
                return { type: 'error', message: value.message, auto_dismiss: true }
            })
            props.toggleMessages(messages)
        } else if (data.message && data.hasOwnProperty('customer')) {
            if (isChangePass) {
                // Remove saved user email and password at localStorage
                let savedUser = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_email');
                let savedPassword = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'user_password');
                if (savedUser && savedPassword) {
                    localStorage.removeItem('user_email');
                    localStorage.removeItem('user_password');
                }
            }
            props.getUserDetails();
            $('#readOnlyInput').val(phoneChange)
            $('#real-input-register').val('')
            localStorage.removeItem('numberphone_register')
            setPhone('')
            props.toggleMessages([{ type: 'success', message: data.message, auto_dismiss: true }])
        }
        hideFogLoading()
    }

    const handleSaveProfile = (e) => {
        e.preventDefault();
        if (allowSubmit) {
            const formValue = $("#harlows-edit-profile").serializeArray();
            const isValidForm = validateForm(formValue);
            if (isValidForm) {
                let params = {
                    email: data.email
                }
                if (changeForm === 'password') {
                    params['change_password'] = 1;
                    setChangePass(true)
                }
                if (changeForm === 'email') {
                    params['change_email'] = 1;
                }
                for (let index in formValue) {
                    let field = formValue[index];
                    params[field.name] = field.value;
                }
                if (changeForm === 'phone') {
                    if (phoneChange !== '') {
                        params.telephone = phoneChange.substring(1)
                    } else {
                        showToastMessage(Identify.__('Invalid phone number !'))
                        return
                    }
                }
                showFogLoading()
                editCustomer(processData, params);
            }
        } else {
            showToastMessage(Identify.__('You must verify your phone number before change !'))
        }
    }

    const openGetModal = () => {
        let countryCode = $('.verify-opt-area #phone-form-control').val()
        let realPhone = $('#real-input-register').val()
        let fullPhoneNumber = (countryCode + realPhone).substring(1)
        if (fullPhoneNumber === telephone) {
            showToastMessage(Identify.__('New phone number must be difference the old one !'))
        } else if (phoneChange.length < 10 || fullPhoneNumber < 10 || !validator.isMobilePhone(fullPhoneNumber)) {
            showToastMessage(Identify.__('Invalid phone number !'))
        } else {
            setModalGet(true)
        }
    }

    const closeGetModal = () => {
        localStorage.removeItem("numberphone_register");
        setModalGet(false)
    }

    const handleSendOtp = () => {
        let phone = phoneChange;
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
            localStorage.setItem("numberphone_register", phoneChange);
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

    const handleVerifyChangePhone = () => {
        let changePhoneOtp = localStorage.getItem('login_otp');
        $('#login-input-otp-warning').css({ display: 'none' })
        showFogLoading();
        verifyOTPForRegister(phoneChange.substring(1), changePhoneOtp, handleCallBackLVerifyChangePhone);
        localStorage.removeItem('login_otp')
    }

    const handleCallBackLVerifyChangePhone = (data) => {
        if (data && data.result && (data.result == "true")) {
            hideFogLoading();
            setAllowSubmit(true)
            showToastMessage(Identify.__('Phone number is Valid !'))
        } else {
            hideFogLoading();
            showToastMessage(Identify.__('Verify OTP fail !'))
            localStorage.removeItem('numberphone_register')
        }
    }

    const onChange = (val1, val2) => {
        $('#verify-opt-area #number_phone-invalid').css({ display: 'none' })
        let value = val1 + val2
        setPhone(value)
        setAllowSubmit(false)
        localStorage.setItem("numberphone_register", value);
    }

    const renderAlternativeForm = () => {
        switch (changeForm) {
            case 'email':
                return (
                    <React.Fragment>
                        <h4 className="title">{Identify.__("Change Email")}</h4>
                        <TextBox
                            label={Identify.__("Email")}
                            name="new_email"
                            type="email"
                            className="required"
                            defaultValue={data.email}
                            onChange={e => handleOnChange(e)}
                        />
                        <TextBox
                            label={Identify.__("Current Password")}
                            name="old_password"
                            type="password"
                            className='required'
                            onChange={e => handleOnChange(e)}
                            required
                        />
                        {/* <div className='email-not-edit'>{Identify.__('Email cannot be edit')}</div> */}
                    </React.Fragment>
                );
                break;
            case 'password':
                return (
                    <React.Fragment>
                        <h4 className="title">{Identify.__("Change Password")}</h4>
                        <TextBox
                            label={Identify.__("Current Password")}
                            name="old_password"
                            type="password"
                            className="required"
                            required
                            onChange={e => handleOnChange(e)}
                        />
                        <div className="group-password-strong">
                            <TextBox
                                label={Identify.__("New password")}
                                name="new_password"
                                type="password"
                                className="required"
                                required
                                onChange={e => handleOnChange(e)}
                            />
                            <div className="password-strength"><span>{Identify.__('Password strength:')}</span><span id="strength-value" style={{ marginLeft: 3 }}>{Identify.__('no password')}</span></div>
                        </div>
                        <TextBox
                            label={Identify.__("Confirm new password")}
                            name="com_password"
                            type="password"
                            className="required"
                            required
                            onChange={e => handleOnChange(e)}
                        />
                    </React.Fragment>
                )
                break;
            case 'phone':
                return (
                    <React.Fragment>
                        <GetOtpModal
                            openGetModal={showModalGet}
                            closeGetModal={closeGetModal}
                            senOtpRegister={handleSendOtp}
                        />
                        <VerifyOtpModal
                            openVerifyModal={showModalVerify}
                            closeVerifyModal={closeVModal}
                            callApi={(phonenumber) => handleVerifyChangePhone(phonenumber)}
                        />
                        <h4 className="title">{Identify.__("Change Phone Number")}</h4>
                        <TextBox
                            id={'readOnlyInput'}
                            label={Identify.__("Current phone number*")}
                            defaultValue={'+' + telephone}
                            readOnly
                        />
                        {/* <PhoneInputReadonly
                            telephone={telephone}
                        /> */}
                        <PhoneInputVerify
                            openGetModal={openGetModal}
                            handleVerify={handleVerifyChangePhone}
                            handleChangePhone={(val1, val2) => onChange(val1, val2)}
                            type={'login'}
                        />
                    </React.Fragment>
                )
                break;
        }
    }

    const changeFormEmail = () => {
        handleChangeForm(changeForm === 'email' ? false : 'email')
        setAllowSubmit(true)
    }

    const changeFormPhone = () => {
        handleChangeForm(changeForm === 'phone' ? false : 'phone')
        if (changeForm === 'phone') {
            setAllowSubmit(true)
        }
        if (changeForm === false) {
            setAllowSubmit(false)
        }
    }

    const changeFormPassword = () => {
        handleChangeForm(changeForm === 'password' ? false : 'password')
        setAllowSubmit(true)
    }

    return (
        <form onSubmit={handleSaveProfile} id="harlows-edit-profile">
            <div className='row-edit-profile'>
                <div className="main__edit-column">
                    <h4 className="title">
                        {Identify.__("Account information")}
                    </h4>
                    <TextBox
                        defaultValue={data.firstname}
                        label={Identify.__("First name")}
                        name="firstname"
                        className="required"
                        required={true}
                        onChange={handleOnChange}
                        placeholder={Identify.__("First Name")}
                    />
                    <TextBox
                        defaultValue={data.lastname}
                        label={Identify.__("Last name")}
                        name="lastname"
                        className="required"
                        required={true}
                        onChange={handleOnChange}
                        placeholder={Identify.__("Last Name")}
                    />
                    <Checkbox
                        className="first"
                        label={Identify.__("Change email")}
                        onClick={() => changeFormEmail()}
                        selected={changeForm === 'email'}
                    />
                    <Checkbox
                        className=""
                        label={Identify.__("Change phone number")}
                        onClick={() => changeFormPhone()}
                        selected={changeForm === 'phone'}
                    />
                    <Checkbox
                        className=""
                        label={Identify.__("Change password")}
                        onClick={() => changeFormPassword()}
                        selected={changeForm === 'password'}
                    />
                    {!isPhone && <Whitebtn
                        text={Identify.__("Save")}
                        className="save-profile"
                        type="submit"
                    />}
                </div>
                <div className='alternative__edit-column'>
                    {renderAlternativeForm()}
                </div>
                {isPhone && <Whitebtn
                    text={Identify.__("Save")}
                    className="save-profile"
                    type="submit"
                />}
            </div>

        </form>
    )
}

export default ProfileForm