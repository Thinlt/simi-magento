import React, { useEffect, useState, useCallback, useRef } from 'react';

import defaultClass from './ccType.css';
import Identify from 'src/simi/Helper/Identify';
import CardHelper from 'src/simi/Helper/Card';
import Button from 'src/components/Button';
const $ = window.$;

const ccType = (props) => {
    const { onSuccess } = props;

    const numberRef = useRef();
    const monthRef = useRef();
    const yearRef = useRef();
    const cvcRef = useRef();

    const [errorMsg, setErrorMsg] = useState('');
    const [hasError, setHasError] = useState('');

    const onCCNUmberInput = e => {
        $(e.currentTarget).val(formatCC(e.currentTarget.value));
    };

    const formatCC = (value) => {
        let v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let cardByNUmber = CardHelper.detectCardType(v);

        let pattern = /\d{4,16}/g;
        let cardType = 'OT';
        /* if (cardByNUmber !== null) {
            pattern = cardByNUmber.cardFormatPattern;
            cardType = cardByNUmber.type;
            // auto pick Card Type By Number
            let paymentMethod = this.props.payment_item.payment_method;
            $('#' + paymentMethod).find('li.cc-item').each(function () {
                $(this).find('.check').hide();
                $(this).find('.uncheck').show();
            });
            let currentTarget = $('#' + cardByNUmber.type);
            currentTarget.find('.check').show();
            currentTarget.find('.uncheck').hide();
            currentTarget.parents('.lists-cc').find('input[name="cc_type"]').val(cardType);
            $('#cc_cid').attr('maxlength', cardByNUmber.cvcLength[0]).attr('placeholder', "*".repeat(cardByNUmber.cvcLength[0]));
        } */
        let regex = new RegExp(pattern, 'gi');
        let matches = v.match(regex);
        let match = (matches && matches[0]) ? matches[0] : '';
        let parts = [];

        for (let i = 0, len = match.length; i < len; i += 4) {
            parts.push(match.substring(i, i + 4))
        }

        if (parts.length) {
            return parts.join(' - ')
        } else {
            return value
        }
    };


    const submitCC = async () => {
        let card = {};
        setErrorMsg('');
        setHasError('');

        card["number"] = numberRef.current.value;
        card["exp_month"] = monthRef.current.value;
        card["exp_year"] = yearRef.current.value;
        card["cvc"] = cvcRef.current.value;

        const secKey = "pk_test_3DZuRfpyIAzQn1C5lGsgnKkj";
        const url = "https://api.stripe.com/v1/tokens";

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

        $.ajax({
            url: url, // Url to which the request is send
            headers: {
                Authorization: `Bearer ${secKey}`,
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            type: "POST",
            data: { card },
            success: function (data) {
                /* self.newCard = true
                self.processData(data) */
                console.log(data);
                processData(data);

            },
            error: function (xhr, status, error) {
                const respondText = JSON.parse(xhr.responseText);
                if (respondText.error.message) {
                    setErrorMsg(respondText.error.message);
                }
                if (respondText.error.param) {
                    setHasError(respondText.error.param);
                }
            }
        });
    }

    const processData = (data) => {
        const { card } = data;
        const paymentData = {
            cc_cid: "",
            cc_exp_month: card.exp_month,
            cc_exp_year: card.exp_year,
            cc_last4: card.last4,
            cc_number: "",
            cc_ss_issue: "",
            cc_ss_start_month: "",
            cc_ss_start_year: "",
            cc_token: data.id,
            cc_type: card.brand,
        };
        onSuccess(paymentData);
    }

    return (
        <div className="container-cc_form">
            <div className={`cc-field form-group ${hasError === 'number' ? 'has-error' : ''}`}>
                <label htmlFor="cc_number">
                    {Identify.__('Credit Card Number')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_number" id="cc_number" ref={numberRef} className="form-control" type="text" onInput={(e) => onCCNUmberInput(e)} placeholder="xxxx - xxxx - xxxx - xxxx" />
            </div>
            <div className={`cc-field form-group ${hasError === 'exp_month' ? 'has-error' : ''}`}>
                <label htmlFor="cc_month">
                    {Identify.__('Month')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_month" id="cc_month" ref={monthRef} className="form-control" type="text" />
            </div>
            <div className={`cc-field form-group ${hasError === 'exp_year' ? 'has-error' : ''}`}>
                <label htmlFor="cc_year">
                    {Identify.__('Year')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_year" id="cc_year" ref={yearRef} className="form-control" type="text" />
            </div>
            <div className={`cc-field form-group ${hasError === 'cvc' ? 'has-error' : ''}`}>
                <label htmlFor="cc_cvc">
                    {Identify.__('CVV')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_cvc" id="cc_cvc" ref={cvcRef} className="form-control" type="text" />
            </div>
            {errorMsg && <div className={defaultClass["cc-msg-error"]}>{errorMsg}</div>}
            <Button
                className={defaultClass['submitCC']}
                style={{ marginTop: 10, marginBottom: 20 }}
                type="button"
                onClick={() => submitCC()}
            >{Identify.__('Use Card')}</Button>
        </div>
    );
}

export default ccType;
