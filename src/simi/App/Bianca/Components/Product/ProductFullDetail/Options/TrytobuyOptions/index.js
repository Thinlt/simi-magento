import React from 'react';
import Identify from 'src/simi/Helper/Identify';

require('./style.scss');

const TrytobuyOptions = (props) => {
    return (
        <React.Fragment>
            <div className={props.className}>
                <input ref={props.cbRef} id="try-to-buy-checkbox" type="checkbox" name="try-to-buy" />
                <label htmlFor="try-to-buy-checkbox">{Identify.__('Try on this item before buying')}</label>
            </div>
        </React.Fragment>
    );
}
export default TrytobuyOptions;