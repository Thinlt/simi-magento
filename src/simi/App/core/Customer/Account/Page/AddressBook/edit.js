import React from 'react';
import { connect } from 'src/drivers';
import { Form, Text, BasicText, BasicSelect, Checkbox, Option, useFieldState, asField } from 'informed';
import Identify from 'src/simi/Helper/Identify';
import {validateEmpty} from 'src/simi/Helper/Validation';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import Loading from "src/simi/BaseComponents/Loading";
import { SimiMutation } from 'src/simi/Network/Query';
import CUSTOMER_ADDRESS_UPDATE from 'src/simi/queries/customerAddressUpdate.graphql';
import CUSTOMER_ADDRESS_CREATE from 'src/simi/queries/customerAddressCreate.graphql';

const SimiText = asField(({ fieldState, ...props }) => (
    <React.Fragment>
      <BasicText
        fieldState={fieldState}
        {...props}
        style={fieldState.error ? { border: 'solid 1px red', color: 'red' } : null}
      />
      {fieldState.error ? (<small style={{ color: 'red' }}>{fieldState.error}</small>) : null}
    </React.Fragment>
));

const SimiSelect = asField(({ fieldState, ...props }) => (
    <React.Fragment>
      <BasicSelect
        fieldState={fieldState}
        {...props}
        style={fieldState.error ? { border: 'solid 1px red' } : null}
      />
      {fieldState.error ? (<small style={{ color: 'red' }}>{fieldState.error}</small>) : null}
    </React.Fragment>
));

const Edit = props => {

    const { addressData, countries, classes, address_fields_config } = props;

    var CUSTOMER_MUTATION = CUSTOMER_ADDRESS_CREATE;
    if (addressData.id) {
        CUSTOMER_MUTATION = CUSTOMER_ADDRESS_UPDATE;
    }

    const getFormApi = (formApi) => {
        // formApi.setValue('firstname', addressData.firstname)
    }

    const validate = (value) => {
        return !validateEmpty(value) ? 'This is a required field.' : undefined;
    }

    const validateStreet = (value) => {
        if (typeof value === 'array') {
            for(var i in value){
                if (!validateEmpty(value[i])) {
                    return 'This is a required field.';
                }
            }
        } else {
            return !validateEmpty(value) ? 'This is a required field.' : undefined;
        }
    }

    const validateOption = (value) => {
        var valid = !value || !validateEmpty(value) ? 'Please select an option.' : undefined;
        return valid;
    }

    const formSubmit = (values) => {
        // console.log('form submited:', values)
        
    }
    
    const formChange = (formState) => {
        // console.log('form change:', formState)
    }

    const getRegionObject = (country_id, region_id) => {
        var country;
        for(var i in countries) {
            if (countries[i].id === country_id){
                country = countries[i];
                break;
            }
        }
        if (country && country.available_regions && country.available_regions.length) {
            for (var i in country.available_regions) {
                if (country.available_regions[i].id === parseInt(region_id)) {
                    return country.available_regions[i];
                }
            }
        }
        return null
    }

    var loading = false;

    const buttonSubmitHandle = (mutaionCallback, formApi) => {
        loading = true;
        var values = formApi.getValues();
        formApi.submitForm();
        if (formApi.getState().invalid) {
            loading = false;
            return null; // not submit until form has no error
        }
        if (values.region) {
            var oldRegionValue = values.region;
            var region;
            if (values.region) region = getRegionObject(values.country_id, values.region.region_id);
            if (region) {
                values.region.region = region.name;
                values.region.region_id = region.id;
                values.region.region_code = region.code;
            } else {
                values.region.region = oldRegionValue.region ? oldRegionValue.region : null;
                values.region.region_id = null;
                values.region.region_code = null;
            }
        }
        // required values
        var config = address_fields_config;
        if (!values.telephone) values.telephone     = config.telephone_default || 'NA';
        if (!values.street) values.street           = [config.street_default || 'NA'];
        if (!values.country_id) values.country_id   = config.country_id_default || 'US';
        if (!values.city) values.city               = config.city_default || 'NA';
        if (!values.postcode) values.postcode       = config.zipcode_default || 'NA';

        values.id = addressData.id; //address id
        mutaionCallback({ variables: values });
    }

    const StateProvince = () => {
        const { value } = useFieldState('country_id');
        var stateShow = address_fields_config.region_id_show;

        // get country
        var country;
        for(var i in countries) {
            if (countries[i].id === value){
                country = countries[i];
                break;
            }
        }
        if (country && country.available_regions && country.available_regions.length) {
            var regionValue = addressData.region.region_id
            if (addressData.country_id !== value) {
                regionValue = null;
            }
            return (
                <>
                { stateShow === 'req' ? 
                    <>
                    <label htmlFor="input-state">{Identify.__('State/Province')}<span>*</span></label>
                    <SimiSelect id="input-state" field="region[region_id]" initialValue={regionValue} 
                        key={regionValue} validate={validateOption} validateOnChange >
                        <Option value="" key={-1}>{Identify.__('Please select a region, state or province.')}</Option>
                        {country.available_regions.map((region, index) => {
                            return <Option value={region.id} key={index}>{region.name}</Option>
                        })}
                    </SimiSelect>
                    </>
                :
                    <>
                    <label htmlFor="input-state">{Identify.__('State/Province')}</label>
                    <SimiSelect id="input-state" field="region[region_id]" initialValue={regionValue} 
                        key={regionValue} >
                        <Option value="" key={-1}>{Identify.__('Please select a region, state or province.')}</Option>
                        {country.available_regions.map((region, index) => {
                            return <Option value={region.id} key={index}>{region.name}</Option>
                        })}
                    </SimiSelect>
                    </>
                }
                </>
            );
        } else {
            var regionValue = addressData.region.region
            if (addressData.country_id !== value) {
                regionValue = null;
            }
            return (
                <>
                { stateShow === 'req' ? 
                    <>
                        <label htmlFor="input-state">{Identify.__('State/Province')}<span>*</span></label>
                        <SimiText id="input-state" field="region[region]" initialValue={regionValue} validate={validateOption} validateOnChange/>
                    </>
                :
                    <>
                        <label htmlFor="input-state">{Identify.__('State/Province')}</label>
                        <SimiText id="input-state" field="region[region]" initialValue={regionValue} />
                    </>
                }
                </>
            )
        }
    }

    
    return (
        <div className={classes['edit-address']}>
            {TitleHelper.renderMetaHeader({title: Identify.__('Edit Address')})}
            <Form id="address-form" getApi={getFormApi} onSubmit={formSubmit} onChange={formChange}>
                {({ formApi }) => (
                    <>
                    <div className={classes["col-left"]}>
                        <div className={classes["form-row"]}>
                            <div className={classes["col-label"]}>{Identify.__('Contact Information')}</div>
                        </div>
                        <div className={classes["form-row"]}>
                            <label htmlFor="input-firstname">{Identify.__('First Name')}<span>*</span></label>
                            <SimiText id="input-firstname" field="firstname" initialValue={addressData.firstname} validate={validate} validateOnBlur validateOnChange />
                        </div>
                        <div className={classes["form-row"]}>
                            <label htmlFor="input-lastname">{Identify.__('Last Name')}<span>*</span></label>
                            <SimiText id="input-lastname" field="lastname" initialValue={addressData.lastname} validate={validate} validateOnBlur validateOnChange />
                        </div>

                        { address_fields_config.company_show && 
                            <div className={classes["form-row"]}>
                            { address_fields_config.company_show === 'req' ? 
                            <>
                                <label htmlFor="input-company">{Identify.__('Company')}<span>*</span></label>
                                <SimiText id="input-company" field="company" initialValue={addressData.company} validate={validate} validateOnBlur validateOnChange />
                            </>
                            :
                            <>
                                <label htmlFor="input-company">{Identify.__('Company')}</label>
                                <SimiText id="input-company" field="company" initialValue={addressData.company} />
                            </>
                            }
                            </div>
                        }

                        {
                            address_fields_config.telephone_show && 
                            <div className={classes["form-row"]}>
                                { address_fields_config.telephone_show === 'req' ?
                                <>
                                    <label htmlFor="input-telephone">{Identify.__('Phone Number')}<span>*</span></label>
                                    <SimiText id="input-telephone" field="telephone" initialValue={addressData.telephone} validate={validate}  validateOnBlur validateOnChange />
                                </>
                                :
                                <>
                                    <label htmlFor="input-telephone">{Identify.__('Phone Number')}</label>
                                    <SimiText id="input-telephone" field="telephone" initialValue={addressData.telephone} />
                                </>
                                }
                            </div>
                        }
                    </div>
                    <div className={classes["col-right"]}>
                        {
                            address_fields_config.street_show || 
                            address_fields_config.city_show || 
                            address_fields_config.region_id_show || 
                            address_fields_config.zipcode_show || 
                            address_fields_config.country_id_show ? 

                            <div className={classes["form-row"]}>
                                <div className={classes["col-label"]}>{Identify.__('Address')}</div>
                            </div>
                        :  
                            <></>
                        }

                        { address_fields_config.street_show && 
                            <div className={classes["form-row"]}>
                                {
                                    address_fields_config.street_show === 'req' ? 
                                    <>
                                    <label htmlFor="input-street1">{Identify.__('Street Address')}<span>*</span></label>
                                    <SimiText id="input-street1" field="street[0]" initialValue={addressData.street[0]} validate={validateStreet} validateOnBlur validateOnChange />
                                    </>
                                :
                                    <>
                                    <label htmlFor="input-street1">{Identify.__('Street Address')}</label>
                                    <SimiText id="input-street1" field="street[0]" initialValue={addressData.street[0]} />
                                    </>
                                }
                                <SimiText id="input-street2" field="street[1]" initialValue={addressData.street[1]}/>
                                <SimiText id="input-street3" field="street[2]" initialValue={addressData.street[2]}/>
                            </div>
                        }

                        { address_fields_config.city_show && 
                            <div className={classes["form-row"]}>
                                {
                                    address_fields_config.city_show === 'req' ? 
                                    <>
                                    <label htmlFor="input-city">{Identify.__('City')}<span>*</span></label>
                                    <SimiText id="input-city" field="city" initialValue={addressData.city} validate={validate}  validateOnBlur validateOnChange />
                                    </>
                                    :
                                    <>
                                    <label htmlFor="input-city">{Identify.__('City')}</label>
                                    <SimiText id="input-city" field="city" initialValue={addressData.city} />
                                    </>
                                }
                            </div>
                        }

                        { address_fields_config.region_id_show && 
                            <div className={classes["form-row"]} id="state-province">
                                <StateProvince />
                            </div>
                        }

                        { address_fields_config.zipcode_show && 
                            <div className={classes["form-row"]}>
                                { address_fields_config.zipcode_show === 'req' ?
                                <>
                                    <label htmlFor="input-postcode">{Identify.__('Zip/Postal Code')}<span>*</span></label>
                                    <SimiText id="input-postcode" field="postcode" initialValue={addressData.postcode} validate={validate} validateOnBlur validateOnChange />
                                </>
                                :
                                <>
                                    <label htmlFor="input-postcode">{Identify.__('Zip/Postal Code')}</label>
                                    <SimiText id="input-postcode" field="postcode" initialValue={addressData.postcode} />
                                </>
                                }
                            </div>
                        }

                        { address_fields_config.country_id_show && 
                            <div className={classes["form-row"]}>
                                { address_fields_config.country_id_show === 'req'?
                                <>
                                    <label htmlFor="input-country">{Identify.__('Country')}<span>*</span></label>
                                    <SimiSelect id="input-country" field="country_id" initialValue={addressData.country_id || 'US'} validate={validateOption} validateOnChange>
                                        { countries.map((country, index) => {
                                            return country.full_name_locale !== null ? 
                                                <Option value={country.id} key={index} >{country.full_name_locale}</Option> : null
                                        })}
                                    </SimiSelect>
                                </>
                                :
                                <>
                                    <label htmlFor="input-country">{Identify.__('Country')}</label>
                                    <SimiSelect id="input-country" field="country_id" initialValue={addressData.country_id || 'US'} validate={validateOption} validateOnChange>
                                        { countries.map((country, index) => {
                                            return country.full_name_locale !== null ? 
                                                <Option value={country.id} key={index} >{country.full_name_locale}</Option> : null
                                        })}
                                    </SimiSelect>
                                </>
                                }
                            </div>
                        }


                        <div className={classes["form-row"]}>
                            <div className={classes["checkbox"]}>
                                <Checkbox id="checkbox-billing" field="default_billing" initialValue={addressData.default_billing} />
                                <label htmlFor="checkbox-billing">{Identify.__('Use as my default billing address')}</label>
                            </div>
                            <div className={classes["checkbox"]}>
                                <Checkbox id="checkbox-shipping" field="default_shipping" initialValue={addressData.default_shipping} />
                                <label htmlFor="checkbox-shipping">{Identify.__('Use as my default shipping address')}</label>
                            </div>
                        </div>
                    </div>
                    <div className={classes["form-button"]}>
                        <SimiMutation mutation={CUSTOMER_MUTATION}>
                            {(mutaionCallback, { data }) => {
                                if (data) {
                                    if (addressData.id) {
                                        var addressResult = data.updateCustomerAddress;
                                    } else {
                                        var addressResult = data.createCustomerAddress;
                                    }
                                    props.dispatchEdit({changeType: addressData.addressType, changeData: addressResult});
                                }
                                return (
                                    <>
                                        <div className={'btn '+classes["btn"]+' '+classes["save-address"]}>
                                            <button onClick={() => buttonSubmitHandle(mutaionCallback, formApi)}>
                                                <span>{Identify.__('Save Address')}</span>
                                            </button>
                                        </div>
                                        {(data === undefined && loading) && <Loading />}
                                    </>
                                );
                            }}
                        </SimiMutation>
                    </div>
                    </>
                )}
            </Form>
        </div>
    );
}

const mapStateToProps = ({ user }) => {
    const { currentUser } = user
    return {
        user: currentUser
    };
}

export default connect(
    mapStateToProps
)(Edit);
