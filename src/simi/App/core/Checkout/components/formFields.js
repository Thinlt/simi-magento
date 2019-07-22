import React, { useCallback, Fragment, useState } from 'react';
import { useFormState } from 'informed';
import {
    validateEmail,
    isRequired,
    hasLengthExactly,
    /* validateRegionCode */
} from 'src/util/formValidators';


import defaultClasses from './formFields.css';
import combine from 'src/util/combineValidators';
import TextInput from 'src/components/TextInput';
import Field from 'src/components/Field';
import Select from 'src/components/Select';
import Checkbox from 'src/components/Checkbox';
import Button from 'src/components/Button';
import Identify from 'src/simi/Helper/Identify';
import { checkExistingCustomer, simiSignIn } from 'src/simi/Model/Customer';
import isObjectEmpty from 'src/util/isObjectEmpty';
import { Link } from 'react-router-dom';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import * as Constants from 'src/simi/Config/Constants'
import { Util } from '@magento/peregrine';
import {smoothScrollToView} from 'src/simi/Helper/Behavior'

const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

const listAddress = (addresses) => {
    let html = null;
    if (addresses && addresses.length) {
        html = addresses.map(address => {
            const labelA = address.firstname + ' ' + address.lastname + ', ' + address.city + ', ' + address.region.region;
            return { value: address.id, label: labelA };
        });
        const ctoSelect = { value: ' ', label: Identify.__('--- Please choose ---') };
        html.unshift(ctoSelect);

        const new_Address = { value: 'new_address', label: Identify.__('New Address') };
        html.push(new_Address);
    }
    return html;
}

const listState = (states) => {
    let html = null;
    if (states && states.length) {
        html = states.map(itemState => {
            return { value: itemState.code, label: itemState.name };
        });
        const ctoState = { value: ' ', label: Identify.__('--- Please choose ---') };
        html.unshift(ctoState);
    }
    return html;
}

let existCustomer = false;
let showState = null;

const FormFields = (props) => {
    const { classes,
        billingForm,
        validationMessage,
        initialCountry,
        selectableCountries,
        submitting,
        submit,
        user,
        billingAddressSaved,
        submitBilling,
        simiSignedIn,
        countries } = props;

    const { isSignedIn, currentUser } = user;

    const { addresses, default_billing, default_shipping } = currentUser;

    const [shippingNewForm, setShippingNewForm] = useState(false);
    // const [existCustomer, setExistCustomer] = useState(false);

    existCustomer = isSignedIn ? false : existCustomer;

    let formState = useFormState();

    const handleSubmitBillingSameFollowShipping = useCallback(
        () => {
            const billingAddress = {
                sameAsShippingAddress: true
            }
            submitBilling(billingAddress);
        },
        [submitBilling]
    )

    const handleChooseShipping = () => {
        if (formState.values.selected_shipping_address !== 'new_address') {
            const { selected_shipping_address } = formState.values;
            setShippingNewForm(false);
            const shippingFilter = addresses.find(
                ({ id }) => id === parseInt(selected_shipping_address, 10)
            );

            if (shippingFilter) {
                if (!shippingFilter.email) shippingFilter.email = currentUser.email;

                handleSubmit(shippingFilter);
                if (!billingForm && !billingAddressSaved) {
                    handleSubmitBillingSameFollowShipping();
                }
            }
        } else {
            setShippingNewForm(true);
        }
    }

    const handleSubmit = useCallback(
        values => {
            if (values.hasOwnProperty('selected_shipping_address')) delete values.selected_shipping_address
            submit(values);
        },
        [submit]
    );

    const processData = (data) => {
        if (data.hasOwnProperty('customer') && !isObjectEmpty(data.customer) && data.customer.email) {
            // setExistCustomer(true)
            existCustomer = true;
        } else {
            existCustomer = false
            // setExistCustomer(false);
        }
    }

    const checkMailExist = () => {
        const { email } = formState.values;
        if (!email) return;
        checkExistingCustomer(processData, email)
    }

    const handleActionSignIn = useCallback(
        (value) => {
            simiSignedIn(value);
        },
        [simiSignedIn]
    )

    const setDataLogin = (data) => {
        hideFogLoading();
        if (data && !data.errors) {
            if (data.customer_access_token) {
                Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, data.customer_identity)
                setToken(data.customer_access_token)
                handleActionSignIn(data.customer_access_token)
            } else {
                setToken(data)
                handleActionSignIn(data)
            }
        } else {
            smoothScrollToView($("#id-message"));
            toggleMessages([{ type: 'error', message: Identify.__('The account sign-in was incorrect or your account is disabled temporarily. Please wait and try again later.'), auto_dismiss: true }])
        }
    }

    const handleSignIn = () => {
        const { email, password } = formState.values;
        if (!email || !password) {
            smoothScrollToView($("#id-message"));
            toggleMessages([{ type: 'error', message: Identify.__('Email and password is required to login!'), auto_dismiss: true }])
            return;
        }
        const username = email;
        simiSignIn(setDataLogin, { username, password })
        showFogLoading()
    }

    const onHandleSelectCountry = () => {
        const { country_id } = formState.values;
        if (!country_id) {
            showState = null;
            return;
        }
        const country = countries.find(({ id }) => id === country_id);
        const { available_regions: regions } = country;
        if (country.available_regions && Array.isArray(regions) && regions.length) {
            showState = <div className={classes.region_code}>
                <Field label="State">
                    <Select field="region_code" items={listState(regions)} validate={isRequired} />
                </Field>
            </div>
        } else {
            showState = null;
        }
    }

    const viewFields = !formState.values.addresses_same ? (

        <Fragment>
            {isSignedIn && default_shipping && <div className={classes.shipping_address}>
                <Field label="Select Shipping">
                    <Select
                        field="selected_shipping_address"
                        initialValue={default_shipping}
                        items={listAddress(addresses)}
                        onChange={() => handleChooseShipping()}
                    />
                </Field>
            </div>}
            {!isSignedIn || !default_billing || shippingNewForm ?
                <Fragment>
                    <div className={classes.email}>
                        <Field label="Email">
                            <TextInput
                                id={classes.email}
                                field="email"
                                validate={combine([isRequired, validateEmail])}
                                onBlur={() => !billingForm && !user.isSignedIn && checkMailExist()}
                            />
                        </Field>
                    </div>
                    {existCustomer && <Fragment>
                        <div className={classes.password}>
                            <Field label="Password">
                                <TextInput
                                    id={classes.password}
                                    field="password"
                                    type="password"
                                />
                            </Field>
                            <span>{Identify.__('You already have an account with us. Sign in or continue as guest')}</span>
                        </div>
                        <div className={classes.btn_login}>
                            <Button
                                className={classes.button}
                                style={{ marginTop: 10 }}
                                type="button"
                                onClick={() => handleSignIn()}
                            >{Identify.__('Login')}</Button>
                            <Link style={{ marginLeft: 5 }} to='/login.html'>{Identify.__('Forgot password?')}</Link>
                        </div>
                    </Fragment>
                    }
                    <div className={classes.firstname}>
                        <Field label="First Name">
                            <TextInput
                                id={classes.firstname}
                                field="firstname"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.lastname}>
                        <Field label="Last Name">
                            <TextInput
                                id={classes.lastname}
                                field="lastname"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.street0}>
                        <Field label="Street">
                            <TextInput
                                id={classes.street0}
                                field="street[0]"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.city}>
                        <Field label="City">
                            <TextInput
                                id={classes.city}
                                field="city"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.postcode}>
                        <Field label="ZIP">
                            <TextInput
                                id={classes.postcode}
                                field="postcode"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.country}>
                        <Field label="Country">
                            <Select
                                field="country_id"
                                initialValue={initialCountry}
                                items={selectableCountries}
                                onChange={() => onHandleSelectCountry()}
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    {showState}
                    <div className={classes.telephone}>
                        <Field label="Phone">
                            <TextInput
                                id={classes.telephone}
                                field="telephone"
                                validate={isRequired}
                            />
                        </Field>
                    </div>
                    <div className={classes.validation}>{validationMessage}</div>
                </Fragment> : null}
        </Fragment>
    ) : null;

    const viewSubmit = !formState.values.addresses_same && (!isSignedIn || !default_billing || shippingNewForm) ? (
        <div className={classes.footer}>
            <Button
                className={classes.button}
                style={{ marginTop: 10, float: 'right' }}
                type="submit"
                priority="high"
                disabled={submitting}
            >{Identify.__('Save Address')}</Button>
        </div>
    ) : null;

    const handleCheckSame = useCallback(
        () => {

            const sameAsShippingAddress = formState.values['addresses_same'];
            let billingAddress;
            if (!sameAsShippingAddress) {
                return;
            } else {
                billingAddress = {
                    sameAsShippingAddress
                };
            }
            submit(billingAddress);
        },
        [submit]
    );

    const checkSameShippingAddress = () => {
        handleCheckSame();
    }

    return <Fragment>
        <div className={classes.body}>
            {billingForm && <Checkbox field="addresses_same" label="Billing address same as shipping address" onChange={() => checkSameShippingAddress()} />}
            {viewFields}
        </div>
        {viewSubmit}
    </Fragment>
}

export default FormFields;

async function setToken(token) {
    // TODO: Get correct token expire time from API
    return storage.setItem('signin_token', token, 3600);
}
