import React, { Component, } from 'react';
import { updateGiftVoucher, deleteGiftCode } from 'src/simi/Model/Cart';
import { getGiftCodes } from 'src/simi/Model/Customer'
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import Identify from 'src/simi/Helper/Identify';
import { Colorbtn } from 'src/simi/BaseComponents/Button'
import ChevronDownIcon from 'react-feather/dist/icons/chevron-down';
import Icon from 'src/components/Icon';
require('./ApplyGiftcard.scss');

const arrow = <Icon src={ChevronDownIcon} size={18} />;

class ApplyGiftcard extends Component {
    constructor(props) {
        super(props)
        this.state = {savedCoupons: []}
        this.savedCouponSelect = false
        this.couponFieldInput = false
        this.applyingGiftCard = false
    }

    componentDidMount() {
        const { userSignedIn } = this.props
        if (userSignedIn)
            getGiftCodes((data) => this.gotVouchers(data), 1, 99999, 'id', 'desc')
    }

    gotVouchers(data) {
        if (data && data.length) {
            this.setState({savedCoupons: data})
        }
    }

    deleteVoucher (giftCode) {
        const { userSignedIn } = this.props
        this.clearVoucher = true
        let storeCode = 'default'
        const storeConfig = Identify.getStoreConfig() || {};
        if (storeConfig.storeConfig && storeConfig.storeConfig.code)
            storeCode = storeConfig.storeConfig.code
        showFogLoading()
        deleteGiftCode((data) => this.processData(data), giftCode, userSignedIn, storeCode)
    }

    handleVoucher() {
        const {toggleMessages} = this.props
        const voucher = this.couponFieldInput.value;
        if (!voucher) {
            toggleMessages([{ type: 'error', message: Identify.__('Please enter gift code'), auto_dismiss: true }]);
            return null;
        }
        this.applyVoucher(voucher)
    }

    applyVoucher(voucher) {
        if (this.applyingGiftCard)
            return
        this.applyingGiftCard = true
        let storeCode = 'default'
        const storeConfig = Identify.getStoreConfig() || {};
        if (storeConfig.storeConfig && storeConfig.storeConfig.code)
            storeCode = storeConfig.storeConfig.code
        const { userSignedIn} = this.props
        showFogLoading()
        updateGiftVoucher((data) => this.processData(data), voucher, userSignedIn, storeCode)
    }

    processData(data) {
        this.applyingGiftCard = false
        const {getCartDetails, toggleMessages} = this.props
        hideFogLoading();
        if(this.clearVoucher){
            if (!data || data.errors) {
                toggleMessages([{ type: 'error', message: Identify.__('Something went wrong'), auto_dismiss: true }]);
            } else {
                this.clearVoucher = false
                toggleMessages([{ type: 'success', message: Identify.__('Gift Card code has been removed'), auto_dismiss: true }]);
                this.couponFieldInput.value = ''
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
        const { giftCartValue} = this.props
        const {savedCoupons} = this.state

        if (giftCartValue) { //equal to cart.totals.total_segments.[giftcartsegment].extension_attributes
            const aw_giftcard_codes = giftCartValue
                .aw_giftcard_codes[0]
                ? giftCartValue.aw_giftcard_codes[0]
                : '';
            if (aw_giftcard_codes) {
                const value = JSON.parse(aw_giftcard_codes);
                giftCode = value.giftcard_code;
            }
        }
        
        const selections = [<option key="0" value="0">{Identify.__('Choose a Gift Voucher')}</option>]
        if (savedCoupons && Array.isArray(savedCoupons) && savedCoupons.length) {
            savedCoupons.map((savedCoupon) => {
                selections.push(
                    <option value={savedCoupon.code} key={savedCoupon.code}>
                        {savedCoupon.code}
                    </option>
                )
            })
        }

        return (
            <div className='gift-voucher-checkout'>
                <div className='gift-voucher-area'>
                    <input id="checkout_voucher_field" type="text" 
                        ref={(item)=> {this.couponFieldInput = item}}
                        placeholder={Identify.__('Enter Gift Code')} defaultValue={giftCode} />
                    {!giftCode 
                    ?   <Colorbtn 
                            id="submit-voucher"
                            className={`${Identify.isRtl() ? "submit-voucher-rtl" : 'submit-voucher'}`}
                            onClick={() => this.handleVoucher()}
                            text={Identify.__('Apply')} 
                        />
                    :   <Colorbtn 
                            id="remove-voucher"
                            className={`${Identify.isRtl() ? "remove-voucher-rtl" : 'remove-voucher'}`}
                            onClick={() => this.deleteVoucher(giftCode)}
                            text={Identify.__('Cancel')} 
                        />
                    }
                </div>
                {
                    (selections.length > 1) && 
                    (
                        <div className="gift-select">
                            <div className="gift-select-label">{Identify.__('Or choose from your existing Gift Voucher(s)')}</div>
                            <div className="gift-select-options">
                                <span className="selectSavedGiftcard">
                                    <span className="selectSavedGiftcardInput">
                                        <select 
                                            name="selected_giftcard_field"
                                            onChange={(e) => {if (this.savedCouponSelect) this.applyVoucher(this.savedCouponSelect.value)}}
                                            ref={(item)=> {this.savedCouponSelect = item}}
                                        >
                                            {selections}
                                        </select>
                                    </span>
                                    <span className="selectSavedGiftcardAfter">{arrow}</span>
                                </span>
                                
                            </div>
                        </div>
                    )
                }
            </div>
        )
    }
}

export default ApplyGiftcard