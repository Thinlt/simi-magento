import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import Price from 'src/simi/BaseComponents/Price';
import defaultClasses from './productprice.css'

class ProductPrice extends React.Component {

    constructor(props){
        super(props);
        this.state = {
            customOptionPrice: {exclT:0, inclT:0}
        };
        this.classes = defaultClasses
    }
    
    setCustomOptionPrice(exclT, inclT) {
        this.setState({
            customOptionPrice: {exclT, inclT}
        })
    }



    calcPrices(price) {
        const {customOptionPrice} = this.state
        const calculatedPrices = JSON.parse(JSON.stringify(price))
        calculatedPrices.minimalPrice.excl_tax_amount.value += customOptionPrice.exclT;
        calculatedPrices.minimalPrice.amount.value += customOptionPrice.inclT;
        calculatedPrices.regularPrice.excl_tax_amount.value += customOptionPrice.exclT;
        calculatedPrices.regularPrice.amount.value += customOptionPrice.inclT;
        calculatedPrices.maximalPrice.excl_tax_amount.value += customOptionPrice.exclT;
        calculatedPrices.maximalPrice.amount.value += customOptionPrice.inclT;
        return calculatedPrices
    }

    render(){
        const {data} = this.props
        const {simiExtraField} = data
        const {classes} = this
        const prices = this.calcPrices(data.price)

        let stockLabel = ''
        if (simiExtraField && simiExtraField.attribute_values) {
            if (parseInt(simiExtraField.attribute_values.is_salable, 10) !== 1)
                stockLabel = Identify.__('Out of stock');
            else 
                stockLabel = Identify.__('In stock');
        }
                
        const priceLabel = (data.type_id === "grouped")?'':(
            <div className={classes['prices-layout']}>
                <Price config={1} key={Identify.randomString(5)} prices={prices} type={data.type_id} classes={classes}/>
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