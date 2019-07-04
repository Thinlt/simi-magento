import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import {configColor} from 'src/simi/Config';
import {formatPrice} from 'src/simi/Helper/Pricing';
import { Price } from '@magento/peregrine'

const style = {
    pirce: {
        color: configColor.price_color,
    },
    specialPrice: {
        color: configColor.special_price_color
    }
};
class Abstract extends React.Component{
    constructor(props) {
        super(props);
        this.type = this.props.type;
        this.configure = null;
        this.configurePrice = this.props.configure_price ? this.props.configure_price : null;
        this.prices = this.props.prices;
        this.parent = this.props.parent;
        this.config = this.parent.props.config;
        this.tapita = this.props.tapita || 1;
    }

    formatPrice(price, special = true) {
        if (!price)
            return
        const {props} = this
        const classes = props.clasess?props.clasess:{}
        style.price = {...style.price,...this.props.stylePrice};
        style.specialPrice = {...style.specialPrice,...this.props.styleSpecialPrice};
        if (special) {
            return (
                <span className={`${classes['price']}`} style={style.price}>
                    {formatPrice(price)}
                </span>
            );
        } else {
            return (
                <span className={`${classes['price']} ${classes['old']}`} style={style.specialPrice}>
                    {formatPrice(price)}
                </span>
            );

        }
    }

    renderViewTablet=()=>{
        return <div>Table</div>
    };

    renderView=()=>{
        return <div>Phone</div>
    }

    render(){
        if(this.config === 1 || this.tapita === 1){
            return this.renderViewTablet();
        }
        return this.renderView();
    }
}
export default Abstract;