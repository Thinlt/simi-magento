import React from 'react';
import Identify from 'src/simi/Helper/Identify';
// import SelectField from '@material-ui/core/Select';
// import MenuItem from '@material-ui/core/MenuItem';
// import { compose } from 'redux';
// import { connect } from 'src/drivers';
require('./style.scss');

// const TrytobuyOptions = React.forwardRef((props, ref) => {
//     const cbRef = React.useRef(ref);
//     return (
//         <div className={props.className}>
//             <input ref={cbRef} id="try-to-buy-checkbox" type="checkbox" name="try-to-buy" />
//             <label htmlFor="try-to-buy-checkbox">{Identify.__('Try on this item before buying')}</label>
//         </div>
//     );
// });

const TrytobuyOptions = (props) => {
    return (
        <React.Fragment>
            <div className={props.className}>
                <input ref={props.cbRef} id="try-to-buy-checkbox" type="checkbox" name="try-to-buy" />
                <label htmlFor="try-to-buy-checkbox">{Identify.__('Try on this item before buying')}</label>
            </div>
            <div className={props.className}>
                <input ref={props.cbRef2} id="reservable" type="checkbox" name="reservable" />
                <label htmlFor="reservable">{Identify.__('Reserve')}</label>
            </div>
            <div className={props.className}>
                <input ref={props.cbRef3} id="pre-order" type="checkbox" name="pre-order" />
                <label htmlFor="pre-order">{Identify.__('Pre order')}</label>
            </div>
        </React.Fragment>
    );
}
export default TrytobuyOptions;