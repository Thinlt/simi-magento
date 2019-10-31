import React, { useState, useCallback } from 'react';
import { func, string } from 'prop-types';
import { hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';
import ArrowDown from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown';
import ArrowUp from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp';
import ShippingForm from 'src/simi/App/Bianca/Checkout/ShippingForm';
import { Form, Option, asField, Select } from 'informed';
import { updateEstimateShipping } from 'src/simi/Model/Cart';

require('./Estimate.scss');

const Estimate = props => {
    const {
        countries,
        shippingAddress,
        submitShippingMethod,
        editOrder,
        availableShippingMethods,
        submitShippingAddress
    } = props;
    const [isOpen, setOpen] = useState(false);
    const SimiSelect = asField(({ fieldState, ...props }) => (
        <React.Fragment>
            <Select
                fieldState={fieldState}
                {...props}
                style={fieldState.error ? { border: 'solid 1px red' } : null}
                className="country-select"
                onBlur={() => {
                    handleSubmitShippingAddressForm({
                        country_id: fieldState.value
                    });
                }}
            />
            {fieldState.error ? (
                <small style={{ color: 'red' }}>{fieldState.error}</small>
            ) : null}
        </React.Fragment>
    ));

    const handleSubmitShippingAddressForm = useCallback(
        formValues => {
            submitShippingAddress({
                formValues
            });
        },
        [submitShippingAddress]
    );

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

    return (
        <div className="estimate">
            <div
                role="button"
                className="estimate-title"
                tabIndex="0"
                onClick={() => setOpen(!isOpen)}
                onKeyDown={() => setOpen(!isOpen)}
            >
                {Identify.__('Estimate Shipping and Tax')}
                {isOpen ? <ArrowUp /> : <ArrowDown />}
            </div>
            <div
                className={`estimate-area-tablet ${
                    isOpen ? 'estimate-open' : 'estimate-close'
                }`}
            >
                <div className="estimate-subtitle">
                    Enter your destination to get a shipping estimate
                </div>
                <div className="country-field">
                    <span>Countries</span>
                    <div className="form-row">
                        <SimiSelect
                            id="input-country"
                            field="country"
                            initialValue={
                                shippingAddress
                                    ? shippingAddress.country_id
                                    : 'US'
                            }
                            // validate={(value) => validateOption(value, addressConfig && addressConfig.country_id_show || 'req')}
                            validateOnChange
                        >
                            {countries &&
                                countries.map((country, index) => {
                                    return country.full_name_english !==
                                        null ? (
                                        <Option
                                            value={`${country.id}`}
                                            key={index}
                                        >
                                            {country.full_name_english}
                                        </Option>
                                    ) : null;
                                })}
                        </SimiSelect>
                        <ArrowDown className="arrow-country" />
                    </div>
                </div>
                <div className="postcode-field">
                    <div>ZIP/POSTCODE</div>
                    <input
                        id="estimate_field"
                        type="text"
                        placeholder={Identify.__('ZIP/POSTCODE')}
                        onBlur={e => {
                            if (!shippingAddress) {
                                handleSubmitShippingAddressForm({
                                    country_id: 'US',
                                    postcode: e.target.value
                                });
                            } else {
                                handleSubmitShippingAddressForm({
                                    country_id: shippingAddress.country_id,
                                    postcode: e.target.value
                                });
                            }
                        }}
                    />
                </div>
                <ShippingForm
                    cancel={handleCancel}
                    submit={handleSubmitShippingForm}
                    availableShippingMethods={availableShippingMethods}
                />
            </div>
        </div>
    );
};

Estimate.propTypes = {
    value: string,
    toggleMessages: func,
    getCartDetails: func
};
export default Estimate;
