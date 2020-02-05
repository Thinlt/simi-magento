import React, { Component } from 'react'
import ReactPhoneInput from 'react-phone-input-2'
import Identify from 'src/simi/Helper/Identify';
require('./verifyFormReadonly.scss')

const $ = window.$;

class PhoneInputReadonly extends Component {

    constructor(props) {
        super(props)
    }

    componentDidMount() {
        $('.verify-opt-readonly .custom-input .react-tel-input input').attr('readonly', true);
    }

    render() {
        
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
            <div id="verify-opt-readonly" className={`verify-opt-readonly ${Identify.isRtl() ? 'verify-opt-readonly-rtl' : ''}`}>
                <div className="label">
                    {Identify.__('current Phone number *'.toUpperCase())}
                </div>
                <div className="custom-input">
                    <div className="element-1">
                        <div className="custom-arrow"></div>
                    </div>
                    <ReactPhoneInput
                        country={"vn"}
                        onlyCountries={listAllowedCountry}
                        countryCodeEditable={false}
                        disabled={true}
                    />
                    <div className="element-2">
                        <input
                            id="real-input-readonly"
                            onKeyUp={() => updateValue()}
                            name="real-input"
                            type="number"
                            pattern="[0-9]"
                            placeholder={Identify.__('Telephone')}
                            value={this.props.telephone.substring(2)}
                            readOnly
                        />
                    </div>
                </div>
                <div id="number_phone-required" className="error-message">{Identify.__("Mobile number is required.")}</div>
                <div id="number_phone-invalid" className="error-message">{Identify.__("Invalid Number.")}</div>
                <div id="number_phone-not-exist" className="error-message">{Identify.__("Mobile number don't exist")}</div>
            </div>
        )
    }
}

export default PhoneInputReadonly;
