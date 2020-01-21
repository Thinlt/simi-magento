import React, {useState} from 'react';
import { func, string } from 'prop-types';
import Identify from 'src/simi/Helper/Identify';
import ArrowDown from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowDown'
import ArrowUp from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowUp'
import ApplyGiftcard from 'src/simi/App/Bianca/BaseComponents/Giftcard/ApplyGiftcard';
require ('./giftVoucher.scss')

const GiftVoucher = (props) => {
    const { toggleMessages, getCartDetails, cart, isSignedIn } = props;
    const [isOpen, setOpen] = useState(false)

    let giftCartValue = "";
    if (cart && cart.totals && cart.totals.total_segments) {
        cart.totals.total_segments.map(total_segment => {
            if ((total_segment.code === 'aw_giftcard') && total_segment.value) {
                giftCartValue = total_segment.extension_attributes
            }
        })
    }

    return (
        <div className='cart-gift-voucher'>
            <div 
                role="button" 
                className="gift-voucher-title" 
                tabIndex="0" 
                onClick={() => setOpen(!isOpen)} 
                onKeyDown={() => setOpen(!isOpen)}>
                    <div>{Identify.__('Add a Gift Voucher')}</div>
                    <div>
                    {isOpen
                    ? <ArrowUp/>
                    : <ArrowDown/>
                    }
                    </div>
            </div>
            <div className={`gift-voucher-area-tablet ${isOpen ? 'voucher-open': 'voucher-close'}`}>
                <ApplyGiftcard getCartDetails={getCartDetails} cart={cart} toggleMessages={toggleMessages} userSignedIn={isSignedIn} giftCartValue={giftCartValue} />
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
