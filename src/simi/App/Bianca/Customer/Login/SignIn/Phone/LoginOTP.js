import React, { Component } from 'react'
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading'
import OtpForm from 'src/simi/App/Bianca/Components/Otp/OtpForm';
import { sendOTPForLogin, verifyOTPForLogin } from 'src/simi/Model/Otp';
import { Util } from '@magento/peregrine';
import Identify from 'src/simi/Helper/Identify'
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();
import validator from 'validator'
const $ = window.$;

class LoginOTP extends Component {
    constructor(props) {
        super(props);
        this.config = window.SMCONFIGS;
        this.state = {
            phone: '',
            isButtonDisabled: false
        }
        this.merchant = Identify.getStoreConfig();
    }

    onChange = (val1, val2) => {
        let value = val1 + val2
        this.setState({ phone: value });
    }

    handleSendProfile = (data, token) => {
        if (data.customer) {
            this.props.onSignIn(token);
        }
    }

    handleSendOtp = () => {
        let phone = this.state.phone;
        console.log(phone)
        $('#login-opt-area #number_phone-not-exist').css({ display: 'none' });
        $('#login-opt-area #number_phone-invalid').css({ display: 'none' });    
        if (!phone && !phone.trim().length === 0) {
            $('#login-opt-area #number_phone-not-exist').css({ display: 'block' })
            return;
        }

        if (!validator.isMobilePhone(phone)) {
            $('#login-opt-area #number_phone-invalid').css({ display: 'block' });
            return;
        }

        // showFogLoading()
        phone = phone.replace(/[- )(]/g, '').replace(/\+/g, "").replace(/\,/g, "");
        this.phoneNB = phone
        let params = {
            mobile: phone
        }
        if (this.merchant && this.merchant.hasOwnProperty('storeConfig') && this.merchant.storeConfig) {
            const { website_id } = this.merchant.storeConfig;
            if (website_id) {
                params['website_id'] = website_id;
            }
        }
        sendOTPForLogin(params, this.handleCallBackSendOTP)
    }

    handleCallBackSendOTP = (data) => {
        hideFogLoading();

        if (data.result && data.result === 'true') {
            this.setState({ isButtonDisabled: true });
            setTimeout(() => this.setState({ isButtonDisabled: false }), 20000);

            $('#login-opt-area #number_phone-invalid').css({ display: 'none' });
            $('#login-opt-area #verify-phone-area').removeClass('hidden');
        } else {
            $('#login-opt-area #number_phone-invalid').css({ display: 'block' })
            $('#login-opt-area #verify-phone-area').addClass('hidden');
        }

    }

    handleVerifyLogin = () => {
        const logintotp = $('#login-input-otp').val();
        let isValid = true;
        if (typeof logintotp !== 'string' && !logintotp && !logintotp.trim().length === 0) {
            isValid = false;
            $('#login-input-otp-warning').css({ display: 'block' })
        } else {
            $('#login-input-otp-warning').css({ display: 'none' })
        }

        if (isValid) {

            showFogLoading();
            verifyOTPForLogin(this.phoneNB, logintotp, this.handleCallBackLVerifyLogin);
        }

    }

    handleCallBackLVerifyLogin = (data) => {
        if (data.result && data.result === 'true' && data.customer_access_token) {
            $('#login-opt-area #return-otp-warning').css({ display: 'none' });
            hideFogLoading();
            setToken(data.customer_access_token)
            // Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, null);
            this.props.onSignIn(data.customer_access_token);
            // getProfileAfterOtp(this.handleSendProfile.bind(this, data.customer_access_token));
        } else {
            hideFogLoading();
            $('#login-opt-area #return-otp-warning').css({ display: 'block' });
        }
    }

    render() {
        const { isButtonDisabled } = this.state;

        return (
            <OtpForm
                handleSendOtp={this.handleSendOtp}
                isButtonDisabled={isButtonDisabled}
                handleVerify={this.handleVerifyLogin}
                handleChangePhone={(val1, val2) => this.onChange(val1, val2)}
                phone={this.state.phone}
                type="login"
            />
        )
    }
}

export default LoginOTP

async function setToken(token) {
    // TODO: Get correct token expire time from API
    return storage.setItem('signin_token', token, 604800);
}

