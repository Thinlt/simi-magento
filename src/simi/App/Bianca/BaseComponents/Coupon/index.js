import React, { useState } from 'react';
import { func, string } from 'prop-types';
import {
    showFogLoading,
    hideFogLoading
} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { updateCoupon } from 'src/simi/Model/Cart';
import Identify from 'src/simi/Helper/Identify';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
import ArrowDown from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown';
import ArrowUp from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp';
require('./style.scss');

const Coupon = props => {
    const { value, toggleMessages, getCartDetails } = props;
    const [isCouponOpen, setOpen] = useState(false);
    const [coupon, setCoupon] = useState('');
    const [clearCoupon, setClearCoupon] = useState(false)
    const handleCoupon = (type = '') => {
        if (!coupon && type !== 'clear') {
            toggleMessages([
                {
                    type: 'error',
                    message: 'Please enter coupon code',
                    auto_dismiss: true
                }
            ]);
            return null;
        }
        if (type === 'clear') {
            setClearCoupon(true)
        }
        showFogLoading();
        const params = {
            coupon_code: coupon
        };
        updateCoupon(processData, params);
    };

    const processData = data => {
        let text = '';
        let success = false;
        if (data.message) {
            const messages = data.message;
            for (let i in messages) {
                const msg = messages[i];
                text += msg + ' ';
            }
        }
        if (data.total && data.total.coupon_code) {
            success = true;
        }
        if (clearCoupon) {
            setClearCoupon(false)
            success = true;
            setCoupon('')
        }
        if (text)
            toggleMessages([
                {
                    type: success ? 'success' : 'error',
                    message: text,
                    auto_dismiss: true
                }
            ]);
        getCartDetails();
        hideFogLoading();
    };

    return (
        <div className="coupon-code">
            <div
                role="button"
                className="coupon-code-title"
                tabIndex="0"
                onClick={() => setOpen(!isCouponOpen)}
                onKeyUp={() => setOpen(!isCouponOpen)}
            >
                <div>{Identify.__('Add a Coupon Code')}</div>
                <div>{isCouponOpen ? <ArrowUp /> : <ArrowDown />}</div>
            </div>
            <div
                className={`coupon-code-area-tablet  ${isCouponOpen?'coupon-open':'coupon-close'}`}
            >
                <input
                    className="coupon_field"
                    type="text"
                    placeholder={Identify.__('Coupon Code')}
                    defaultValue={value}
                    onChange={e => setCoupon(e.target.value)}
                />
                {value ? (
                    <Colorbtn
                        className={`submit-coupon ${ Identify.isRtl() && 'submit-coupon-rtl' }`}
                        onClick={() => handleCoupon('clear')}
                        text={Identify.__('Cancel')}
                    />
                ) : (
                    <Colorbtn
                        className={`submit-coupon ${ Identify.isRtl() && 'submit-coupon-rtl' }`}
                        onClick={() => handleCoupon()}
                        text={Identify.__('Apply')}
                    />
                )}
            </div>
        </div>
    );
};

Coupon.propTypes = {
    value: string,
    toggleMessages: func,
    getCartDetails: func
};
export default Coupon;
