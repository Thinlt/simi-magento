import React, { useEffect, useState, useCallback } from 'react';
import defaultClass from './ccType.css';
import Identify from 'src/simi/Helper/Identify';
const $ = window.$;

const ccType = (props) => {
    const onCCNUmberInput = e => {
        $(e.currentTarget).val(formatCC(e.currentTarget.value));
    };

    const formatCC = (value) => {
        let v = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
        let cardByNUmber = CardHelper.detectCardType(v);

        let pattern = /\d{4,16}/g;
        let cardType = 'OT';
        if (cardByNUmber !== null) {
            pattern = cardByNUmber.cartdFormatPattern;
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
        }
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

    return <form className={defaultClass['cc-form-fields']}>
        Coming soon!
        {/* <div className="container-cc_form">
            <div className="cc-field">
                <label htmlFor="cc_number">
                    {Identify.__('Credit Card Number')}
                    <span className="label-required">*</span>
                </label>
                <input name="cc_number" id="cc_number" className='required' type="text" onInput={(e) => onCCNUmberInput(e)} placeholder="xxxx - xxxx - xxxx - xxxx" />
            </div>
        </div> */}
    </form>
}

export default ccType;
