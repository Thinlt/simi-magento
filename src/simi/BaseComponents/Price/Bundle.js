import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify';
import {configColor} from 'src/simi/Config';

class BundlePrice extends Abstract {

    showConfiguredPrice =(element,price,label = null)=>{
      $(function () {
          if(price) {
              $(element).show();
              $(element).children('.price').html(Identify.formatPrice(price));
              $(element).children('.label-price').html(label);
          }
      })
    };

    renderView=()=>{
        let price_label = <div></div>;
        let price_excluding_tax = <div></div>;
        let price_including_tax = <div></div>;
        let price = <div></div>;
        let price_in = <div></div>;
        let product_from_label = <div></div>;
        let from_price_excluding_tax = <div></div>;
        let from_price_including_tax = <div></div>;
        let product_to_label = <div></div>;
        let to_price_excluding_tax = <div></div>;
        let to_price_including_tax = <div></div>;
        let configured_label = <div></div>;
        let configured_price_ex = <div></div>;
        let configured_price_in = <div></div>;

        if (this.prices.minimal_price && this.prices.minimal_price === 1) {
            if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                //show ex and in tax
                price_label = <div>{this.prices.price_label}</div>;
                price_excluding_tax = <div>{Identify.__('Excl. Tax')}
                    : {this.formatPrice(this.prices.price_excluding_tax.price)}</div>;
                price_including_tax = (
                    <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.price_including_tax.price)}</div>
                );
            } else {
                price_label = <div>{this.prices.price_label}: {this.prices.price}</div>;
                if (this.prices.price_in && this.prices.price_in !== '') {
                    price_in = <div>{this.prices.price_in}</div>;
                }
            }
        } else {
            //show from to with tax
            if (this.prices.show_from_to_tax_price && this.prices.show_from_to_tax_price === 1) {
                if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                    product_from_label = <div>{this.prices.product_from_label}:</div>;
                    from_price_excluding_tax =
                        <div>{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.from_price_including_tax.price)}</div>
                    from_price_including_tax =
                        <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.from_price_including_tax.price)}</div>

                    product_to_label = <div>{this.prices.product_to_label}:</div>;
                    to_price_excluding_tax =
                        <div>{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.to_price_excluding_tax.price)}</div>
                    to_price_including_tax =
                        <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.to_price_including_tax.price)}</div>
                } else {
                    product_from_label =
                        <div>{this.prices.product_from_label}: {this.formatPrice(this.prices.from_price)}</div>;
                    product_to_label =
                        <div>{this.prices.product_to_label}: {this.formatPrice(this.prices.to_price)}</div>;
                }
            } else {
                if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                    product_from_label =
                        <div>{this.prices.product_from_label}: {this.formatPrice(this.prices.from_price)}</div>;
                    product_to_label =
                        <div>{this.prices.product_to_label}: {this.formatPrice(this.prices.to_price)}</div>;
                }
            }
        }

        if (this.prices.configure) {
            configured_label = <div>{this.prices.configure.product_label}</div>;
            if (this.prices.configure.show_ex_in_price && this.prices.configure.show_ex_in_price === 1) {
                if (this.prices.configure.price_excluding_tax) {
                    configured_price_ex =
                        <div>{Identify.__('Excl. Tax')}: {this.formatPrice(this.prices.configure.price_excluding_tax.price)}</div>;
                }
                if (this.prices.configure.price_including_tax) {
                    configured_price_in =
                        <div>{Identify.__('Incl. Tax')}: {this.formatPrice(this.prices.configure.price_including_tax.price)}</div>;
                }
            } else {
                configured_label =
                    <div>{this.prices.configure.product_label}: {this.formatPrice(this.prices.configure.price)}</div>;
            }
        }
        return (
            <div className="product-prices small">
                {price_label}
                {price_excluding_tax}
                {price_including_tax}
                {price_in}
                {price}
                {product_from_label}
                {from_price_excluding_tax}
                {from_price_including_tax}
                {product_to_label}
                {to_price_excluding_tax}
                {to_price_including_tax}
                {configured_label}
                {configured_price_ex}
                {configured_price_in}
            </div>
        );
    }

    render(){
        return super.render();
    }
}
export default BundlePrice;