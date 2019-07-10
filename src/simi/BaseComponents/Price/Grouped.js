import React from 'react';
import Abstract from './Abstract';
import {configColor} from 'src/simi/Config';
import Identify from 'src/simi/Helper/Identify';
class Grouped extends Abstract {

    renderView = ()=>{
        let from_label = <span className="from-label"
                               style={{fontSize : 20,fontWeight : 800,color : configColor.price_color,marginRight : 5}}>
                            {Identify.__('From')}
                        </span>;
        let label_style = {
            display : 'flex',
            alignItems : 'baseline'
        };
        if(this.prices.show_ex_in_price && this.prices.show_ex_in_price === 1){
            let price_excl = <div>
                                    <div>{Identify.__('Excl. Tax')}</div>
                                    <div style={label_style}>
                                        {from_label}
                                        {this.formatPrice(this.prices.price_excluding_tax.price)}
                                    </div>
                            </div>;
            let price_incl = <div>
                                <div>{Identify.__('Incl. Tax')}</div>
                                <div style={label_style}>
                                    {from_label}
                                    {this.formatPrice(this.prices.price_including_tax.price)}
                                </div>
                            </div>;
            return(
                <div>
                    {price_excl}
                    {price_incl}
                </div>
            )
        }else {
            let price = <div style={label_style}>
                    {from_label}
                    {this.formatPrice(this.prices.price)}
                </div>;
            return(
                <div>
                    {price}
                </div>
            )
        }
    }

    render(){
        return super.render();
    }
}
export default Grouped;