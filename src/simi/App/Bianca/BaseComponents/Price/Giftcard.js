import React from 'react';
import Abstract from './Abstract';
import Identify from 'src/simi/Helper/Identify'

class Giftcard extends Abstract {
    renderView=()=>{
        const price = (<div className="regular">{Identify.__('From ')} {this.formatPrice(this.prices.minimalPrice.amount.value, this.prices.minimalPrice.amount.currency)}</div>);
        return (
            <div className='product-prices'>
                {price}
            </div>
        );
    };

    render(){
        return super.render();
    }
}
export default Giftcard;