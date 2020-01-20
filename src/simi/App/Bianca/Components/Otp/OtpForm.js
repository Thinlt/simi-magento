import React, { Component } from 'react'
import ReactPhoneInput from 'react-phone-input-2'
import Identify from 'src/simi/Helper/Identify';
import { configColor } from 'src/simi/Config'
import CountDown from './CountDown';
import { $CombinedState } from 'redux';
require('./style.scss')

const $ = window.$;

class OtpForm extends Component {

    constructor(props) {
        super(props)
    }

    componentDidMount() {
        $('.login-opt-area .custom-input .react-tel-input input').attr('readonly', true);
    }

    render() {
        const styleActice = {
            backgroundColor: configColor.button_background,
            color: configColor.button_text_color
        }

        const styleInActive = {
            backgroundColor: "#eee",
            color: "#fff",
            cursor: 'not-allowed'
        }

        const showOption = () => {
            $('.arrow').click()
        }

        const updateValue = () => {
            let country_code = $('#phone-form-control').val()
            let new_val = $('#real-input').val()
            this.props.handleChangePhone(country_code, new_val)
        }

        const storeConfig = Identify.getStoreConfig();
        const countries = storeConfig.simiStoreConfig.config.allowed_countries;
        const listAllowedCountry = [];
        countries.map((country, index) => {
            let code = country.country_code
            listAllowedCountry.push(code.toLowerCase())
        })

        const doNothing = () => {

        }

        return (
            <div id="login-opt-area" className={`login-opt-area ${Identify.isRtl() ? 'login-opt-area-rtl' : ''}`}>
                <div className="label">
                    {Identify.__('phone *'.toUpperCase())}
                </div>
                <div className="custom-input">
                    <div className="element-1" onClick={() => showOption()}>
                        <div className="custom-arrow"></div>
                    </div>
                    <ReactPhoneInput
                        country={"vn"}
                        onlyCountries={listAllowedCountry}
                        countryCodeEditable={false}
                        onChange={updateValue}
                    />
                    <div className="element-2">
                        <input
                            id="real-input"
                            onKeyUp={() => updateValue()}
                            name="real-input"
                            type="number"
                            pattern="[0-9]"
                            placeholder={Identify.__('Phone')}
                        />
                    </div>
                </div>
                <div id="number_phone-required" className="error-message">{Identify.__("Mobile number is required.")}</div>
                <div id="number_phone-invalid" className="error-message">{Identify.__("Invalid Number.")}</div>
                <div id="number_phone-not-exist" className="error-message">{Identify.__("Mobile number don't exist")}</div>
                <div className='phone-otp-desc'>
                    {/* {Identify.__('Mobile No. Without Country Code i.e 9898989898')} */}
                </div>
                <div role="presentation" style={this.props.isButtonDisabled ? styleInActive : styleActice} className='login-otp' onClick={!this.props.isButtonDisabled ? this.props.handleSendOtp : doNothing}>
                    {Identify.__('SEND VERIFICATION CODE').toUpperCase()}
                    {this.props.isButtonDisabled && <CountDown time={30} />}
                </div>

                {this.props.isButtonDisabled ?
                    <div id="verify-phone-area" className={`verify-phone-area hidden`}>
                        <div className="login-otp-form-field">
                            <div className="label">{Identify.__('Enter One Time Password')}</div>
                            <span className="description">{Identify.__("One Time Password (OTP) has been sent to your mobile,please enter the same here to login.")}</span>
                            <input type="password" name="logintotp" id="login-input-otp" className="form-control" ref="logintotp" />
                            <div id="login-input-otp-warning"
                                className="error-message">{Identify.__("Please enter OTP")}</div>
                            <div id="return-otp-warning" className="error-message">{Identify.__("OTP Not Verified.")}</div>
                        </div>
                        <div role="presentation" className="login-otp"
                            style={styleActice}
                            onClick={this.props.handleVerify}
                        >{this.props.type === 'login' ? Identify.__('Verify & Login').toUpperCase() : Identify.__('Verify Forgot Password')}</div>
                    </div>
                    : ''
                }
            </div>
        )
    }
}

export default OtpForm;
