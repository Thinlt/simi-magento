import React from 'react';
import Abstract from './Abstract';

class Simple extends Abstract {

    renderViewTablet=()=>{
        let price_label = <div></div>;
        let weee = <div></div>;
        let low_price_label = <div></div>;
        let low_price = <div></div>;
        let special_price_label = <div></div>;
        let price_excluding_tax = <div></div>;
        let price_including_tax = <div></div>;
        let price = <div></div>;
        const style = {
            display : 'flex',
            fontSize : this.props.size ? this.props.size : 28,
            fontWeight : 800,
            alignItems : 'center'
        };
        const ex_in_style = {fontSize : 14,fontWeight : 400,marginLeft : 5};
        const sale_off_style = {fontWeight : 500,fontSize : 26,marginLeft : 20};
        if (this.prices.has_special_price !== null && this.prices.has_special_price === 1) {
            let sale_off = 0;
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                special_price_label = (<div>{this.prices.special_price_label}</div>);
                price_excluding_tax = (
                    <div style={style}>
                        {this.formatPrice(this.prices.price_excluding_tax.price)}
                        <span style={ex_in_style} className="ex-tax">
                            {this.prices.price_excluding_tax.label}
                        </span>
                    </div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div style={style}>
                        {this.formatPrice(this.prices.price_including_tax.price)}
                        <span style={ex_in_style}>{this.prices.price_including_tax.label}</span>
                    </div>
                );
                sale_off = 100 - (this.prices.price_including_tax.price / this.prices.regular_price) * 100;
                sale_off = sale_off.toFixed(0);
                price_label = (
                    <div style={{...sale_off_style,display:'flex',marginLeft : 0,alignItems:'baseline'}}>{this.formatPrice(this.prices.regular_price, false)} <span
                        className="sale_off" style={{marginLeft:10}}>-{sale_off}%</span></div>
                );
                return(
                    <div>
                        {price_label}
                        {price_excluding_tax}
                        {price_including_tax}
                    </div>
                )
            } else {
                //special_price_label = (<div>{this.prices.special_price_label}</div>);
                price = (<div className="a">{this.formatPrice(this.prices.price)}</div>);
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                sale_off = 100 - (this.prices.price / this.prices.regular_price) * 100;
                sale_off = sale_off.toFixed(0);
                price_label = (
                    <div className="special-price-label" style={sale_off_style}>{this.formatPrice(this.prices.regular_price, false)} <span
                        className="sale_off">-{sale_off}%</span></div>
                );
                return(
                    <div style={style} className="special-price">
                        {price}
                        {price_label}
                    </div>
                )
            }

        }else {
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                price_excluding_tax = (
                    <div style={style}>
                        {this.formatPrice(this.prices.price_excluding_tax.price)}
                        <span style={ex_in_style}>{this.prices.price_excluding_tax.label}</span>
                    </div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div style={style}>
                        {this.formatPrice(this.prices.price_including_tax.price)}
                        <span style={ex_in_style}>{this.prices.price_including_tax.label}</span>
                    </div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 2) {
                    weee = <div>{this.prices.weee}</div>
                }
                return(
                    <div>
                        {price_excluding_tax}
                        {price_including_tax}
                    </div>
                )
            } else {
                if (false) {
                    //if (this.prices.price_label) {
                    price_label = (
                        <div>{this.prices.price_label}</div>
                    );
                }
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price = (<div style={style}>{this.formatPrice(this.prices.price)}</div>);
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 2) {
                    weee = <div>{this.prices.weee}</div>
                }
            }
        }
        return(
            <div>
                {price}
                {price_label}
                {special_price_label}
                {price_excluding_tax}
                {price_including_tax}
                {weee}
                {low_price_label}
                {low_price}
            </div>
        )
    };

    renderView=()=>{
        ////simple, configurable ....
        let price_label = <div></div>;
        let weee = <div></div>;
        let low_price_label = <div></div>;
        let low_price = <div></div>;
        let special_price_label = <div></div>;
        let price_excluding_tax = <div></div>;
        let price_including_tax = <div></div>;
        let price = <div></div>;
        if (this.prices.has_special_price !== null && this.prices.has_special_price === 1) {
            let sale_off = 0;
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                special_price_label = (<div>{this.prices.special_price_label}</div>);
                price_excluding_tax = (
                    <div>{this.prices.price_excluding_tax.label}: {this.formatPrice(this.prices.price_excluding_tax.price)}</div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div>{this.prices.price_including_tax.label}: {this.formatPrice(this.prices.price_including_tax.price)}</div>
                );
                sale_off = 100 - (this.prices.price_including_tax.price / this.prices.regular_price) * 100;
                sale_off = sale_off.toFixed(0);
            } else {
                //special_price_label = (<div>{this.prices.special_price_label}</div>);
                price = (<div >{this.formatPrice(this.prices.price)}</div>);
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                sale_off = 100 - (this.prices.price / this.prices.regular_price) * 100;
                sale_off = sale_off.toFixed(0);
            }
            price_label = (
                <div>{this.formatPrice(this.prices.regular_price, false)} <span
                    className="sale_off">-{sale_off}%</span></div>
            );

        } else {
            if (this.prices.show_ex_in_price !== null && this.prices.show_ex_in_price === 1) {
                price_excluding_tax = (
                    <div>{this.prices.price_excluding_tax.label}: {this.formatPrice(this.prices.price_excluding_tax.price)}</div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price_including_tax = (
                    <div>{this.prices.price_including_tax.label}: {this.formatPrice(this.prices.price_including_tax.price)}</div>
                );
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 2) {
                    weee = <div>{this.prices.weee}</div>
                }
            } else {
                if (false) {
                    //if (this.prices.price_label) {
                    price_label = (
                        <div>{this.prices.price_label}</div>
                    );
                }
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 1) {
                    weee = <div>{this.prices.weee}</div>
                }
                price = (<div>{this.formatPrice(this.prices.price)}</div>);
                if (this.prices.show_weee_price !== null && this.prices.show_weee_price === 2) {
                    weee = <div>{this.prices.weee}</div>
                }
            }
        }
        return (
            <div className="product-prices small" >
                {price}
                {price_label}
                {special_price_label}
                {price_excluding_tax}
                {price_including_tax}
                {weee}
                {low_price_label}
                {low_price}
            </div>
        );
    };

    render(){
        return super.render();
    }
}
export default Simple;