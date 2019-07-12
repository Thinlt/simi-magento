import React, {useCallback, Fragment } from 'react';
import { useFormState } from 'informed';
import {
    validateEmail,
    isRequired,
    hasLengthExactly,
    validateRegionCode
} from 'src/util/formValidators';

import defaultClasses from './formFields.css';
import combine from 'src/util/combineValidators';
import TextInput from 'src/components/TextInput';
import Field from 'src/components/Field';
import Select from 'src/components/Select';
import Checkbox from 'src/components/Checkbox';
import Button from 'src/components/Button';
import Identify from 'src/simi/Helper/Identify';

const FormFields = (props) => {
    const { classes, billingForm, validationMessage, initialCountry, selectableCountries, submitting, submit } = props;

    let formState = useFormState();

    const viewFields = !formState.values.addresses_same ? (
        <Fragment>
            <div className={classes.email}>
                <Field label="Email">
                    <TextInput
                        id={classes.email}
                        field="email"
                        validate={combine([isRequired, validateEmail])}
                    />
                </Field>
            </div>
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
                    />
                </Field>
            </div>
            <div className={classes.region_code}>
                <Field label="State">
                    <TextInput
                        id={classes.region_code}
                        field="region_code"
                        validate={combine([
                            isRequired,
                            [hasLengthExactly, 2],
                            [validateRegionCode, props.countries]
                        ])}
                    />
                </Field>
            </div>
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
        </Fragment>
    ) : null;

    const viewSubmit = !formState.values.addresses_same ? (
        <div className={classes.footer}>
            <Button
                className={classes.button}
                style={{marginTop: 10, float: 'right'}}
                type="submit"
                priority="high"
                disabled={submitting}
            >{Identify.__('Save Address')}</Button>
        </div>
    ) : null;

    const handleCheckSame = useCallback(
        value => {

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
            {/* <h2 className={classes.heading}>{billingForm ? Identify.__('Billing Information') : Identify.__('Shipping Address')}</h2> */}
            {billingForm && <Checkbox field="addresses_same" label="Billing address same as shipping address" onChange={() => checkSameShippingAddress()} />}
            {viewFields}
        </div>
        {viewSubmit}
    </Fragment>
}

export default FormFields;
