import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify'

class Configurable extends Abstract {
    renderView=()=>{
        let price = null, special_price = null, price_label = null, special_price_label = null, price_excluding_tax = null, price_including_tax = null;
        const hasDiscount = this.prices.regularPrice.amount.value !== this.prices.minimalPrice.amount.value;
        if (hasDiscount) {
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                special_price_label = (<div>{this.prices.special_price_label}</div>);
                price_excluding_tax = (
                    <div className="regular">{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.minimalPrice.excl_tax_amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
                price_including_tax = (
                    <div className="regular">{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
            } else {
                price = (<div className="regular">{this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>);
            }
            special_price = (
                <div className="special">{this.formatPrice(this.prices.regularPrice.amount.value, this.prices.regularPrice.amount.currency, false)}</div>
            );
            // price_label = (
            //     <div className="old-price">{this.formatPrice(this.prices.regularPrice.amount.value, false)}</div>
            // );
        } else {
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                price_excluding_tax = (
                    <div className="regular">{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.minimalPrice.excl_tax_amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
                price_including_tax = (
                    <div className="regular">{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
            } else {
                price = (<div className="regular">{this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>);
            }
        }
        return (
            <div className='product-prices' >
                {price}
                {special_price}
                {price_label}
                {special_price_label}
                {price_excluding_tax}
                {price_including_tax}
            </div>
        );
    };

    render(){
        return super.render();
    }
}
export default Configurable;