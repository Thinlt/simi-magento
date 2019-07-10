import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import Price from 'src/simi/BaseComponents/Price';
import defaultClasses from './productprice.css'

class ProductPrice extends React.Component {

    constructor(props){
        super(props);
        this.state = {prices: props.data.price};
        this.classes = defaultClasses
        console.log(props.data.price)
        console.log('construct')
    }
    
    updatePrices(prices) {
        this.setState({prices: prices});
    }

    render(){
        console.log('render')
        const {data, simiExtraField} = this.props
        const {classes} = this

        let stockLabel = ''
        if (simiExtraField && simiExtraField.attribute_values) {
            if (parseInt(simiExtraField.attribute_values.is_salable, 10) !== 1)
                stockLabel = Identify.__('Out of stock');
            else 
                stockLabel = Identify.__('In stock');
        }
                
        const priceLabel = (data.type_id === "grouped")?'':(
            <div className={classes['prices-layout']}>
                <Price config={1} prices={this.state.prices} type={data.type_id} classes={classes}/>
            </div>
        );
        return (
            <div className={classes['prices-container']} id={data.type_id}>
                {priceLabel}
                <div className={classes['product-stock-status']}>
                    <div className={classes['stock-status']}>
                        {stockLabel}
                    </div>
                    {
                        data.sku && 
                        <div className={`${classes["product-sku"]} flex`} id="product-sku">
                            <span className={classes['sku-label']}>{Identify.__('Sku') + ": "}</span>
                            {data.sku}
                        </div>
                    }
                </div>
            </div>

        );
    }
}
export default ProductPrice;