import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify'

class Simple extends Abstract {
    renderView=()=>{
        ////simple, configurable ....
        let price_label = <div></div>;
        let special_price_label = <div></div>;
        let price_excluding_tax = <div></div>;
        let price_including_tax = <div></div>;
        let price = <div></div>;
        if (this.prices.has_special_price !== null && this.prices.has_special_price === 1) {
            console.log(this.prices)
            let sale_off = 0;
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                special_price_label = (<div>{this.prices.special_price_label}</div>);
                price_excluding_tax = (
                    <div>{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.minimalPrice.excl_tax_amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
                price_including_tax = (
                    <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
            } else {
                price = (<div >{this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>);
            }
            sale_off = 100 - (this.prices.minimalPrice.amount.value / this.prices.regularPrice.amount.value) * 100;
            sale_off = sale_off.toFixed(0);
            price_label = (
                <div>{this.formatPrice(this.prices.regular_price, false)} <span
                    className="sale_off">-{sale_off}%</span></div>
            );
        } else {
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                price_excluding_tax = (
                    <div>{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.minimalPrice.excl_tax_amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
                price_including_tax = (
                    <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>
                );
            } else {
                price = (<div>{this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>);
            }
        }
        return (
            <div className="product-prices small" >
                {price}
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
export default Simple;