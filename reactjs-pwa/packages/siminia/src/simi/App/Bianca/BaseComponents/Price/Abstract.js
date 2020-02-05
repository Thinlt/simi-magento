import React from 'react';
import {configColor} from 'src/simi/Config';
import {formatPrice as helperFormatPrice} from 'src/simi/Helper/Pricing';

const style = {
    price: {
        color: '#101820'
    },
    specialPrice: {
        color: '#727272'
    }
};
class Abstract extends React.Component{
    constructor(props) {
        super(props);
        this.type = this.props.type;
        this.prices = this.props.prices;
        this.parent = this.props.parent;
    }

    formatPrice(price, currency = null, special = true) {
        if (!price)
            return
        style.price = {...style.price,...this.props.stylePrice};
        style.specialPrice = {...style.specialPrice,...this.props.styleSpecialPrice};
        if (special) {
            return (
                <span className="price" style={style.price}>
                    {helperFormatPrice(price, currency)}
                </span>
            );
        } else {
            return (
                <span className="price old" style={style.specialPrice}>
                    {helperFormatPrice(price, currency)}
                </span>
            );

        }
    }
    render(){
        return this.renderView();
    }
}
export default Abstract;