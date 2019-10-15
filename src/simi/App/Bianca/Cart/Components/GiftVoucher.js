import React, {useState} from 'react';
import { func, string } from 'prop-types';
import { showFogLoading, hideFogLoading } from 'src/simi/BaseComponents/Loading/GlobalLoading';
import { updateGiftVoucher, deleteGiftCode } from 'src/simi/Model/Cart';
import Identify from 'src/simi/Helper/Identify';
import { Whitebtn } from 'src/simi/BaseComponents/Button'
import Close from 'src/simi/BaseComponents/Icon/TapitaIcons/Close'
import ArrowDown from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown'
import ArrowUp from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp'
require ('./giftVoucher.scss')

const GiftVoucher = (props) => {
    const { giftCode, toggleMessages, getCartDetails, cart } = props;
    const [isOpen, setOpen] = useState(false)
    let clearVoucher = false;
    const handleVoucher = (type = '') => {
        const voucher = document.querySelector('#voucher_field').value;
        if (!voucher && type !== 'clear') {
            toggleMessages([{ type: 'error', message: 'Please enter gift code', auto_dismiss: true }]);
            return null;
        }
        showFogLoading()
        const params = {
            'aw-giftcard': voucher
        }
        updateGiftVoucher(processData, params)
    }

    const deleteVoucher = () => {
        const params = {
            'aw-giftcard': giftCode
        }
        deleteGiftCode(processData, params)
    }

    const processData = (data) => {
        const giftcard = cart.totals.total_segments[4] ? cart.totals.total_segments[4] : null;
        const textSuccess = 'Successful';
        const textFailed = giftcard ? 'Gift Cart has already added' : 'Gift Cart is invalid'
        if (data.errors || giftcard) {
            toggleMessages([{ type: 'error', message: textFailed, auto_dismiss: true }]);
        }
        if(clearVoucher){
            clearVoucher = false
            success = true
            document.querySelector('#voucher_field').value = ''
        }
        if (data === true) toggleMessages([{ type: 'success', message: textSuccess, auto_dismiss: true }]);
        getCartDetails();
        hideFogLoading();
    }

    return (
    <div className='gift-voucher'>
        <div 
            role="button" 
            className="gift-voucher-title" 
            tabIndex="0" 
            onClick={() => setOpen(!isOpen)} 
            onKeyDown={() => setOpen(!isOpen)}>
                {Identify.__('Add a Gift Voucher')}
                {isOpen
                ? <ArrowUp/>
                : <ArrowDown/>
                }
        </div>
        <div className={`gift-voucher-area-tablet ${isOpen ? 'voucher-open': 'voucher-close'}`}>
            <input id="voucher_field" type="text" placeholder={Identify.__('enter gift code')} defaultValue={giftCode} />
                    {giftCode && <button className='btn-clear-voucher' onClick={()=>deleteVoucher()}>
                        <Close style={{width:15,height:15}}/>
                    </button>   }
            {!giftCode 
            ?   <Whitebtn id="submit-voucher" className={`${Identify.isRtl() ? "submit-voucher-rtl" : 'submit-voucher'}`} onClick={() => handleVoucher()} text={Identify.__('Apply')} />
            :   null
            }
        </div>
    </div>
    )
}

GiftVoucher.propTypes = {
    value: string,
    toggleMessages: func,
    getCartDetails: func
}
export default GiftVoucher;
