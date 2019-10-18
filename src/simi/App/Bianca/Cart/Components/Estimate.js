import React, {useState, useCallback} from 'react';
import { func, string } from 'prop-types';
import {hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';
import ArrowDown from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown'
import ArrowUp from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp'
import ShippingForm from 'src/simi/App/Bianca/Checkout/ShippingForm';
import { Form, Option, asField, Select } from 'informed';

require ('./Estimate.scss')

const Estimate = (props) => {
    const {toggleMessages, getCartDetails, cart, submitShippingMethod, editOrder,availableShippingMethods } = props;
    const [isOpen, setOpen] = useState(false)
    const storeConfig = Identify.getStoreConfig();
    const countries = storeConfig.simiStoreConfig.config.allowed_countries;

    const SimiSelect = asField(({ fieldState, ...props }) => (
        <React.Fragment>
          <Select
            fieldState={fieldState}
            {...props}
            style={fieldState.error ? { border: 'solid 1px red' } : null}
            className='country-select'
          />
          {fieldState.error ? (<small style={{ color: 'red' }}>{fieldState.error}</small>) : null}
        </React.Fragment>
    ));

    let clearVoucher = false;
    // const handleVoucher = (type = '') => {
    //     let voucher = document.querySelector('#voucher_field').value;
    //     if (!voucher && type !== 'clear') {
    //         toggleMessages([{ type: 'error', message: 'Please enter gift code', auto_dismiss: true }]);
    //         return null;
    //     }
    //     if(type === 'clear'){
    //         clearVoucher = true
    //         voucher = ''
    //     }
    //     showFogLoading()
    //     const params = {
    //         'aw-giftcard': voucher
    //     }
    //     updateGiftVoucher(processData, params)
    // }

    const processData = (data) => {
        const giftcard = cart.totals.total_segments[4] ? cart.totals.total_segments[4] : null;
        const textSuccess = 'Added Gift Card';
        const textFailed = giftcard ? 'Gift Cart has already added' : 'Gift Cart is invalid'
        if (data.errors || giftcard) {
            toggleMessages([{ type: 'error', message: textFailed, auto_dismiss: true }]);
        }
        if(clearVoucher){
            clearVoucher = false
            success = true
            document.querySelector('#voucher_field').value = ''
        }
        if (data === true) toggleMessages([{ type: 'success', message: textSuccess, auto_dismiss: true }]);
        getCartDetails();
        hideFogLoading();
    }

    const handleSubmitShippingForm = useCallback(
        formValues => {
            submitShippingMethod({
                formValues
            });
        },
        [submitShippingMethod]
    );

    const handleCancel = useCallback(() => {
        editOrder(null);
    }, [editOrder]);

    const {estimateOpen, countryOpen} = isOpen

    return (
    <div className='estimate'>
        <div 
            role="button" 
            className="estimate-title" 
            tabIndex="0" 
            onClick={() => setOpen(!isOpen)} 
            onKeyDown={() => setOpen(!isOpen)}>
                {Identify.__('Estimate Shipping and Tax')}
                {isOpen
                ? <ArrowUp/>
                : <ArrowDown/>
                }
        </div>
        <div className={`estimate-area-tablet ${isOpen ? 'estimate-open': 'estimate-close'}`}>
            <div className="estimate-subtitle">Enter your destination to get a shipping estimate</div>
            <div className="country-field">
                <span>Countries</span>
                <div className="form-row" >
                    <SimiSelect id="input-country" field="country" initialValue={'US'} 
                        // validate={(value) => validateOption(value, addressConfig && addressConfig.country_id_show || 'req')} 
                        validateOnChange
                    >
                        { countries.map((country, index) => {
                            return country.country_name !== null ? 
                                <Option value={`${country.country_code}`} key={index} >{country.country_name}</Option> : null
                        })}
                    </SimiSelect>
                    <ArrowDown className='arrow-country'/>
                </div>
            </div>
            <div className="postcode-field">
                <div>ZIP/POSTCODE</div>
                <input id="estimate_field" type="text" placeholder={Identify.__('ZIP/POSTCODE')}  />
            </div>
            <ShippingForm cancel={handleCancel} submit={handleSubmitShippingForm} availableShippingMethods={availableShippingMethods} />
        </div>
    </div>
    )
}

Estimate.propTypes = {
    value: string,
    toggleMessages: func,
    getCartDetails: func
}
export default Estimate;
