import React from 'react';
import {formatPrice as helperFormatPrice} from 'src/simi/Helper/Pricing';
import {configColor} from 'src/simi/Config';
import PropTypes from 'prop-types';
import BundlePrice from './Bundle';
import Simple from './Simple';
import Grouped from './Grouped';
const style = {
    price: {
        color: configColor.price_color,
    },
    specialPrice: {
        color: configColor.special_price_color
    }
};

class PriceComponent extends React.Component {
    constructor(props) {
        super(props);
        this.type = this.props.type;
    }

    formatPrice(price, special = true) {
        const {props} = this
        const classes = props.clasess?props.clasess:{}
        style.price = {...style.price,...this.props.stylePrice};
        style.specialPrice = {...style.specialPrice,...this.props.styleSpecialPrice};
        if (special) {
            return (
                <span className={`${classes['price']}`} style={style.price}>
                    {helperFormatPrice(price)}
                </span>
            );
        } else {
            return (
                <span className={`${classes['price']} ${classes['old']}`} style={style.specialPrice}>
                    {helperFormatPrice(price)}
                </span>
            );

        }
    }

    renderView() {
        this.prices = this.props.prices;
        if (this.type === "bundle") {
            return <BundlePrice prices={this.prices} parent={this} classes={this.props.classes} />
        }
        else if (this.type === "grouped") { // for list page
            return <Grouped prices={this.prices} parent={this} classes={this.props.classes} />
        }
        else {
            ////simple, configurable ....
            return <Simple prices={this.prices} parent={this} classes={this.props.classes} size={this.props.size ? this.props.size : window.innerWidth<768 ? 20 : 28}/>
        }
    }

    render() {
        const {props} = this
        const classes = props.clasess?props.clasess:{}
        return (
            <div className={`price-${this.type} ${classes[`price-${this.type}`]}`}>{this.renderView()}</div>
        );
    }
}
PriceComponent.defaultProps = {
    prices : 0,
    type : 'simple',
    stylePrice : {},
    styleSpecialPrice : {}
};
PriceComponent.propTypes = {
    type : PropTypes.string,
    stylePrice : PropTypes.object,
    styleSpecialPrice : PropTypes.object
};
export default PriceComponent;