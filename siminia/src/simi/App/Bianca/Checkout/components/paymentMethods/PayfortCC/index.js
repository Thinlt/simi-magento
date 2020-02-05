import React, { useRef, useState } from 'react';
import Identify from 'src/simi/Helper/Identify';
import CardHelper from 'src/simi/Helper/Card';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
require('./payfortcc.scss');
const $ = window.$;

const ListMonth = {
    "01": Identify.__("January"),
    '02': Identify.__("February"),
    '03': Identify.__("March"),
    "04": Identify.__("April"),
    "05": Identify.__("May"),
    "06": Identify.__("June"),
    "07": Identify.__("July"),
    "08": Identify.__("August"),
    "09": Identify.__("September"),
    "10": Identify.__("October"),
    "11": Identify.__("November"),
    "12": Identify.__("December")
}

const PayfortCC = (props) => {

    const numberRef = useRef();
    const monthRef = useRef();
    const yearRef = useRef();
    const cvcRef = useRef();
    const nameRef = useRef();

    const [errorMsg, setErrorMsg] = useState('');
    const [successMsg, setSuccessMsg] = useState('');
    const [hasError, setHasError] = useState('');
    const initialValues = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data') ? Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data') : '';

    const onCCNUmberInput = e => {
        $(e.currentTarget).val(formatCC(e.currentTarget.value));
    };

    const formatCC = (value) => {
        const v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let cardByNUmber = CardHelper.detectCardType(v);

        const pattern = /\d{4,16}/g;
        let cardType = 'OT';
        const regex = new RegExp(pattern, 'gi');
        const matches = v.match(regex);
        const match = (matches && matches[0]) ? matches[0] : '';
        const parts = [];

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4))
        }

        if (parts.length) {
            return parts.join(' - ')
        } else {
            return value
        }
    };


    const submitCC = () => {
        let card = {};
        setErrorMsg('');
        setHasError('');
        setSuccessMsg('');

        card["number"] = numberRef.current.value;
        card["exp_month"] = monthRef.current.value;
        card["exp_year"] = yearRef.current.value;
        card["cvc"] = cvcRef.current.value;
        card["name"] = nameRef.current.value;

        if (!numberRef.current.value || !monthRef.current.value || !yearRef.current.value || !cvcRef.current.value) {
            if (!numberRef.current.value) {
                setHasError('number');
                setErrorMsg(Identify.__('Your card\'s expiration number is invalid'));
                return;
            }
            if (!monthRef.current.value) {
                setHasError('exp_month');
                setErrorMsg(Identify.__('Your card\'s expiration month is invalid'));
                return;
            }
            if (!yearRef.current.value) {
                setHasError('exp_year');
                setErrorMsg(Identify.__('Your card\'s expiration year is invalid'));
                return;
            }
            if (!cvcRef.current.value) {
                setHasError('cvc');
                setErrorMsg(Identify.__('Your card\'s security code is invalid.'));
                return;
            }
        }

        Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'payfort_cc_card_data', card);
        setSuccessMsg(Identify.__('Saved card!'));
    }

    const renderMonths = () => {
        let months = [];
        for (var key in ListMonth) {
            months.push(
                <option key={Identify.randomString()} value={key}>{ListMonth[key] + " - " + key}</option>
            );
        }
        return <select name="cc_month" id="cc_month" ref={monthRef} key={Identify.randomString(5)} defaultValue={initialValues && initialValues.hasOwnProperty('exp_month') ? initialValues.exp_month : ''} className="form-control">
            {months}
        </select>
    }

    const renderYears = () => {
        const currentYear = (new Date()).getFullYear();
        let count = 0;
        let listYear = [];
        while (count < 50) {
            let year = currentYear + count;
            listYear.push(
                <option key={Identify.randomString()} value={year}>{year}</option>
            );
            count++
        }

        return (
            <select name="cc_year" id="cc_year" ref={yearRef} key={Identify.randomString(5)} defaultValue={initialValues && initialValues.hasOwnProperty('exp_year') ? initialValues.exp_year : ''} className="form-control">
                {listYear}
            </select>
        );
    }

    return (
        <div className={`container-cc_form ${Identify.isRtl() ? 'container-cc_form-rtl': ''}`}>
            <div className={`cc-field form-group ${hasError === 'number' ? 'has-error' : ''}`}>
                <label htmlFor="cc_number">
                    {Identify.__('Credit Card Number')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_number" id="cc_number" ref={numberRef} defaultValue={initialValues && initialValues.hasOwnProperty('number') ? initialValues.number : ''} className="form-control" type="text" onInput={(e) => onCCNUmberInput(e)} placeholder="xxxx - xxxx - xxxx - xxxx" />
            </div>
            <div className={`cc-field form-group`}>
                <label htmlFor="cc_holder_name">
                    {Identify.__('Card Name')}
                </label>
                <input name="cc_holder_name" id="cc_holder_name" ref={nameRef} defaultValue={initialValues && initialValues.hasOwnProperty('name') ? initialValues.name : ''} className="form-control" type="text" />
            </div>
            <div className={`cc-field form-group ${hasError === 'exp_month' ? 'has-error' : ''}`}>
                <label htmlFor="cc_month">
                    {Identify.__('Month')}
                    <span className="label-required">*</span>
                </label>
                {renderMonths()}
            </div>
            <div className={`cc-field form-group ${hasError === 'exp_year' ? 'has-error' : ''}`}>
                <label htmlFor="cc_year">
                    {Identify.__('Year')}
                    <span className="label-required">*</span>
                </label>
                {renderYears()}
            </div>
            <div className={`cc-field form-group ${hasError === 'cvc' ? 'has-error' : ''}`}>
                <label htmlFor="cc_cvc">
                    {Identify.__('CVV')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_cvc" id="cc_cvc" ref={cvcRef} defaultValue={initialValues && initialValues.hasOwnProperty('cvc') ? initialValues.cvc : ''} className="form-control" type="text" />
            </div>
            {errorMsg && <div className="cc-msg-error">{errorMsg}</div>}
            {successMsg && <div className="cc-msg-success">{successMsg}</div>}
            <Colorbtn
                className="submitCC"
                style={{ marginTop: 10, marginBottom: 20 }}
                type="button"
                onClick={() => submitCC()}
                text={Identify.__('Use Card')}
            />
        </div>
    );
}

export default PayfortCC;
