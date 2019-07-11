import React from 'react';
import Identify from 'src/simi/Helper/Identify';
import Price from 'src/simi/BaseComponents/Price';
import defaultClasses from './productprice.css'
import ObjectHelper from 'src/simi/Helper/ObjectHelper';

const initState = {
    customOptionPrice: {exclT:0, inclT:0}
}

class ProductPrice extends React.Component {

    constructor(props){
        super(props);
        const {configurableOptionSelection} = props
        this.state = {...initState, ...{sltdConfigOption: ObjectHelper.mapToObject(configurableOptionSelection)}};
        this.classes = defaultClasses
    }
    
    setCustomOptionPrice(exclT, inclT) {
        this.setState({
            customOptionPrice: {exclT, inclT}
        })
    }


    static getDerivedStateFromProps(nextProps, prevState) {
        const {configurableOptionSelection} = nextProps
        const {sltdConfigOption} = prevState
        const newSltdConfigOption = ObjectHelper.mapToObject(configurableOptionSelection)
        if (!ObjectHelper.shallowEqual(sltdConfigOption, newSltdConfigOption))
            return {...initState, ...{sltdConfigOption: newSltdConfigOption}}
        return {}
    }

    calcConfigurablePrice = (price) => {
        const {sltdConfigOption} = this.state
        const {data} = this.props
        const {simiExtraField} = data

        if (simiExtraField) {
            const {configurable_options} = simiExtraField.app_options
            if (configurable_options && configurable_options.index && configurable_options.optionPrices) {
                let sub_product_id = null
                for (const index_id in configurable_options.index) {
                    const index = configurable_options.index[index_id] 
                    if (ObjectHelper.shallowEqual(index, sltdConfigOption)) {
                        sub_product_id = index_id;
                        break;
                    }
                }
                if (sub_product_id) {
                    let sub_product_price = configurable_options.optionPrices[sub_product_id]
                    if (!sub_product_price)
                        sub_product_price = configurable_options.optionPrices[parseInt(sub_product_id, 10)]
                    if (sub_product_price) {
                        price.minimalPrice.excl_tax_amount.value = sub_product_price.basePrice.amount
                        price.minimalPrice.amount.value = sub_product_price.finalPrice.amount
                        price.regularPrice.excl_tax_amount.value = sub_product_price.basePrice.amount
                        price.regularPrice.amount.value = sub_product_price.finalPrice.amount
                        price.maximalPrice.excl_tax_amount.value = sub_product_price.basePrice.amount
                        price.maximalPrice.amount.value = sub_product_price.finalPrice.amount
                    }
                }
            }
        }
    }

    calcPrices(price) {
        const {customOptionPrice} = this.state
        const calculatedPrices = JSON.parse(JSON.stringify(price))
        const {data} = this.props
        if (data.type_id === 'configurable')
            this.calcConfigurablePrice(calculatedPrices)
        
        // custom option
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
        if (simiExtraField) {
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