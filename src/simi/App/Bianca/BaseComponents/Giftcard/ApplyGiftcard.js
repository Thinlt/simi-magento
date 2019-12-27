import React, { Component, } from 'react';
import { updateGiftVoucher, deleteGiftCode } from 'src/simi/Model/Cart';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';
import { Whitebtn } from 'src/simi/BaseComponents/Button'
import Close from 'src/simi/BaseComponents/Icon/TapitaIcons/Close'
require('./ApplyGiftcard.scss');

class ApplyGiftcard extends Component {
    deleteVoucher (giftCode) {
        const { userSignedIn } = this.props
        this.clearVoucher = true
        let storeCode = 'default'
        const storeConfig = Identify.getStoreConfig() || {};
        if (storeConfig.storeConfig && storeConfig.storeConfig.code)
            storeCode = storeConfig.storeConfig.code
        deleteGiftCode((data) => this.processData(data), giftCode, userSignedIn, storeCode)
    }

    handleVoucher(type = '') {
        let storeCode = 'default'
        const storeConfig = Identify.getStoreConfig() || {};
        if (storeConfig.storeConfig && storeConfig.storeConfig.code)
            storeCode = storeConfig.storeConfig.code
        const {toggleMessages, userSignedIn} = this.props
        const voucher = document.querySelector('#checkout_voucher_field').value;
        if (!voucher && type !== 'clear') {
            toggleMessages([{ type: 'error', message: Identify.__('Please enter gift code'), auto_dismiss: true }]);
            return null;
        }
        showFogLoading()
        updateGiftVoucher((data) => this.processData(data), voucher, userSignedIn, storeCode)
    }

    processData(data) {
        const {getCartDetails, toggleMessages} = this.props
        hideFogLoading();
        if(this.clearVoucher){
            if (!data || data.errors) {
                toggleMessages([{ type: 'error', message: Identify.__('Something went wrong'), auto_dismiss: true }]);
            } else {
                this.clearVoucher = false
                toggleMessages([{ type: 'success', message: Identify.__('Gift Card code has been removed'), auto_dismiss: true }]);
                document.querySelector('#checkout_voucher_field').value = ''
            }
        } else if (!data || data.errors) {
            toggleMessages([{ type: 'error', message: Identify.__('Gift Cart is invalid'), auto_dismiss: true }]);
        } else {
            toggleMessages([{ type: 'success', message: Identify.__('Gift Card code has been applied'), auto_dismiss: true }]);
        }
        getCartDetails();
    }

    render() {
        let giftCode = '';
        const {cart} = this.props

        if (cart && cart.totals && cart.totals.total_segments) {
            const segment = cart.totals.total_segments.find(item => {
                if (
                    item.extension_attributes &&
                    item.extension_attributes.aw_giftcard_codes
                )
                    return true;
                return false;
            });
            if (segment) {
                const aw_giftcard_codes = segment.extension_attributes
                    .aw_giftcard_codes[0]
                    ? segment.extension_attributes.aw_giftcard_codes[0]
                    : '';
                if (aw_giftcard_codes) {
                    const value = JSON.parse(aw_giftcard_codes);
                    giftCode = value.giftcard_code;
                }
            }
        }

        return (
            <div className='gift-voucher-checkout'>
                <div className={`gift-voucher-area`}>
                    <input id="checkout_voucher_field" type="text" placeholder={Identify.__('Enter Gift Code')} defaultValue={giftCode} />
                            {giftCode && <button className='btn-clear-voucher' onClick={()=>this.deleteVoucher(giftCode)}>
                                <Close style={{width:15,height:15}}/>
                            </button>   }
                    {!giftCode 
                    ?   <Whitebtn 
                            id="submit-voucher"
                            className={`${Identify.isRtl() ? "submit-voucher-rtl" : 'submit-voucher'}`}
                            onClick={() => this.handleVoucher()}
                            text={Identify.__('Apply')} 
                        />
                    :   null
                    }
                </div>
            </div>
        )
    }
}

export default ApplyGiftcard