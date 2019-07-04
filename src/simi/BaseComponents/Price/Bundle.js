import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify';
import {configColor} from 'src/simi/Config';
const $ = window.$;
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

    renderViewTablet=()=>{
        let price_label = <div></div>;
        let weee = <div></div>;
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
        let from_to_label = <div></div>;
        let symbol = <span style={{fontSize : window.innerWidth < 768 ? 12 : 28,margin : '0 10px',color : configColor.price_color,fontWeight:800}}>-</span>;
        let from_label = <span className="from-label"
                               style={{fontSize : 28,fontWeight : 800,color : configColor.price_color}}>
                            {Identify.__('From')}
                        </span>;
        let label_style = {
            display : 'flex',
            alignItems : 'baseline'
        };
        let from_to_style = {
            display : 'flex',
            alignItems: 'center'
        };
        if (this.prices.minimal_price && this.prices.minimal_price === 1) {
            if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                //show ex and in tax
                //price_label = <div>{this.prices.price_label}</div>;
                price_excluding_tax =
                    <div style={label_style}>
                        {from_label}
                        <span style={{margin : '5px'}}>{this.formatPrice(this.prices.price_excluding_tax.price)}</span>
                        <span style={{marginLeft : 'auto'}}>{this.prices.price_excluding_tax.label}</span>
                    </div>;
                if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div style={label_style}>
                        {from_label}
                        <span style={{margin :'0 5px'}}>{this.formatPrice(this.prices.price_including_tax.price)}</span>
                        <span style={{marginLeft : 'auto'}}>{this.prices.price_including_tax.label}</span>
                    </div>
                );
            } else {
                price_label = (
                    <div style={label_style} className="bundle-from">
                        {from_label}
                        <span style={{margin :'0 5px'}}>{this.formatPrice(this.prices.price)}</span>
                    </div>
                );
                if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
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
                        <div>
                            <div style={{marginTop : 5}}>{this.prices.from_price_excluding_tax.label}</div>
                            <div style={from_to_style}>
                                {this.formatPrice(this.prices.from_price_excluding_tax.price)}
                                {symbol}
                                {this.formatPrice(this.prices.to_price_excluding_tax.price)}
                            </div>
                        </div>
                    
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                    product_to_label = <div>{this.prices.product_to_label}:</div>;
                    to_price_including_tax =
                        <div>
                            <div style={{marginTop : 5}}>{this.prices.from_price_including_tax.label}</div>
                            <div>
                                {this.formatPrice(this.prices.from_price_including_tax.price)}
                                {symbol}
                                {this.formatPrice(this.prices.to_price_including_tax.price)}
                            </div>
                        </div>

                } else {
                    product_from_label =
                        <div>{this.formatPrice(this.prices.from_price)}</div>;
                    product_to_label =
                        <div>{this.formatPrice(this.prices.to_price)}</div>;
                    from_to_label =
                        <div style={from_to_style}>
                            {product_from_label} {symbol} {product_to_label}
                        </div>
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                }
            } else {
                if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                    product_from_label =
                        <div>{this.prices.product_from_label}: {this.formatPrice(this.prices.from_price)}</div>;
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                    product_to_label =
                        <div>{this.prices.product_to_label}: {this.formatPrice(this.prices.to_price)}</div>;
                } else {
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                }
            }
        }

        if (this.prices.configure) {
            if (this.prices.configure.show_ex_in_price && this.prices.configure.show_ex_in_price === 1) {
                if (this.prices.configure.price_excluding_tax) {
                    configured_price_ex ={
                        price : this.prices.configure.price_excluding_tax.price,
                        label : this.prices.configure.price_excluding_tax.label
                    };
                }
                if (this.prices.configure.price_including_tax) {
                    configured_price_in ={
                        label : this.prices.configure.price_including_tax.label,
                        price : this.prices.configure.price_including_tax.price
                    };
                }
            } else {
                configured_label = {
                    price : this.prices.configure.price
                };
            }
        }

        return (
            <div className="product-prices small">
                {price_label}
                {price_excluding_tax}
                {price_including_tax}
                {price_in}
                {price}
                {weee}
                {from_to_label}
                {from_price_excluding_tax}
                {from_price_including_tax}
                {to_price_excluding_tax}
                {to_price_including_tax}
                {this.showConfiguredPrice('.configured_label',configured_label.price)}
                {this.showConfiguredPrice('.configured_price_ex',configured_price_ex.price,configured_price_ex.label)}
                {this.showConfiguredPrice('.configured_price_in',configured_price_in.price,configured_price_in.label)}
            </div>
        );
    };

    renderView=()=>{
        let price_label = <div></div>;
        let weee = <div></div>;
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
                price_excluding_tax = <div>{this.prices.price_excluding_tax.label}
                    : {this.formatPrice(this.prices.price_excluding_tax.price)}</div>;
                if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div>{this.prices.price_including_tax.label}: {this.formatPrice(this.prices.price_including_tax.price)}</div>
                );
            } else {
                price_label = <div>{this.prices.price_label}: {this.prices.price}</div>;
                if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
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
                        <div>{this.prices.from_price_excluding_tax.label}: {this.formatPrice(this.prices.from_price_including_tax.price)}</div>
                    from_price_including_tax =
                        <div>{this.prices.from_price_including_tax.label}: {this.formatPrice(this.prices.from_price_including_tax.price)}</div>

                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                    product_to_label = <div>{this.prices.product_to_label}:</div>;
                    to_price_excluding_tax =
                        <div>{this.prices.to_price_excluding_tax.label}: {this.formatPrice(this.prices.to_price_excluding_tax.price)}</div>
                    to_price_including_tax =
                        <div>{this.prices.to_price_including_tax.label}: {this.formatPrice(this.prices.to_price_including_tax.price)}</div>
                } else {
                    product_from_label =
                        <div>{this.prices.product_from_label}: {this.formatPrice(this.prices.from_price)}</div>;
                    product_to_label =
                        <div>{this.prices.product_to_label}: {this.formatPrice(this.prices.to_price)}</div>;
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                }
            } else {
                if (this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1) {
                    product_from_label =
                        <div>{this.prices.product_from_label}: {this.formatPrice(this.prices.from_price)}</div>;
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                    product_to_label =
                        <div>{this.prices.product_to_label}: {this.formatPrice(this.prices.to_price)}</div>;
                } else {
                    if (this.prices.show_weee_price && this.prices.show_weee_price === 1) {
                        weee = <div>{this.prices.weee}</div>;
                    }
                }
            }
        }

        if (this.prices.configure) {
            configured_label = <div>{this.prices.configure.product_label}</div>;
            if (this.prices.configure.show_ex_in_price && this.prices.configure.show_ex_in_price === 1) {
                if (this.prices.configure.price_excluding_tax) {
                    configured_price_ex =
                        <div>{this.prices.configure.price_excluding_tax.label}: {this.formatPrice(this.prices.configure.price_excluding_tax.price)}</div>;
                }
                if (this.prices.configure.price_including_tax) {
                    configured_price_in =
                        <div>{this.prices.configure.price_including_tax.label}: {this.formatPrice(this.prices.configure.price_including_tax.price)}</div>;
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
                {weee}
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