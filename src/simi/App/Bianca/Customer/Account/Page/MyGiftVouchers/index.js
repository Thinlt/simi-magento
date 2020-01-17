import React, { useState, useEffect } from 'react';
import classify from 'src/classify';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import PageTitle from 'src/simi/App/core/Customer/Account/Components/PageTitle';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { showToastMessage } from 'src/simi/Helper/Message';
import Vouchers from './Vouchers';
import defaultClasses from './style.scss';
import Loading from 'src/simi/BaseComponents/Loading';
import { getGiftCodes, getHistoryCodes } from 'src/simi/Model/Customer';
import { Colorbtn } from 'src/simi/BaseComponents/Button';
import { addGiftVoucher, removeCode } from 'src/simi/Model/Customer';
import TextBox from 'src/simi/BaseComponents/TextBox';
import Identify from 'src/simi/Helper/Identify';

$ = window.$;

const MyGiftVouchers = props => {
    // const [code, setCode] = useState('');
    const [giftCode, setGiftCode] = useState('');
    const [historyCodes, setHistoryCodes] = useState('');
    const [loading, setLoading] = useState(true);
    useEffect(() => {
        getGiftCodes(getCodeCallBack, 1, 5, 'id', 'asc');
        getHistoryCodes(getHistoryCodesCallBack, 1, 5, 'id', 'asc');
    }, []);

    const getCodeCallBack = data => {
        setGiftCode(data);
        setLoading(false)
    };

    const getHistoryCodesCallBack = data => {
        setHistoryCodes(data);
        setLoading(false)
    };

    const handleSubmitVoucher = (e) => {
        e.preventDefault();
        // const storeConfig = Identify.getStoreConfig();
        // const storeId = storeConfig.simiStoreConfig.store_id;
        const code = $('#voucher-input').val();
        setLoading(true)
        addGiftVoucher(giftVoucherCallBack, {code});
    }

    const giftVoucherCallBack = (data) => {
        if (data instanceof Array){
            setGiftCode(data);
        } else {
            showToastMessage(Identify.__('This code does not available'));
        }
        $('#voucher-input').val('');
        setLoading(false);
    }

    return (
        <div className="my-gift-vouchers-area">
            <PageTitle title={'GIFT VOUCHERS'} />
            <form className="form-add-voucher" onSubmit={handleSubmitVoucher}>
                <TextBox
                    label={Identify.__('ADD A GIFT VOUCHERS')}
                    name="voucher"
                    className="add-voucher"
                    placeholder="Specify Gift Code"
                    // onChange={(e)=>setCode(e.target.value)}
                    id={"voucher-input"}
                />
                <Colorbtn type="submit" 
                    className="add-voucher-btn" 
                    text={Identify.__("Add to my list")}
                />
            </form>
            {giftCode && historyCodes && !loading ? (
                <Vouchers
                    isPhone={props.isPhone}
                    giftCode={giftCode}
                    historyCodes={historyCodes}
                    setGiftCode={setGiftCode}
                    setHistoryCodes={setHistoryCodes}
                    setLoading={setLoading}
                />
            ) : (
                <Loading />
            )}
        </div>
    );
};

const mapDispatchToProps = {
    toggleMessages
};

export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
)(MyGiftVouchers);
