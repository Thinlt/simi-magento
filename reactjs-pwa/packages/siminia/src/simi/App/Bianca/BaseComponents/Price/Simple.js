import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify'

class Simple extends Abstract {
    renderView=()=>{
        let price_label = <div></div>;
        let special_price_label = <div></div>;
        let price_excluding_tax = <div></div>;
        let price_including_tax = <div></div>;
        let price = <div></div>;
        if (this.prices.has_special_price) {
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
            
            price_label = (
                <div className="special">
                    <span className="label">{Identify.__('Regular Price: ')}</span>
                    {this.formatPrice(this.prices.regularPrice.amount.value, this.prices.regularPrice.amount.currency, false)}
                    <span className="sale_off">-{this.prices.discount_percent}%</span>
                </div>
            );
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
            <div className='product-prices'>
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