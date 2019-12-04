import React from 'react'
import Identify from 'src/simi/Helper/Identify';
import { Price } from '@magento/peregrine';

require('./Childproducts.scss');

const Childproducts = props => {
    let {childProducts} = props
    const {cartCurrencyCode} = props
    childProducts = JSON.parse(childProducts)
    let child_products = []
    if (childProducts && childProducts.length) {
        child_products = childProducts.map((childProduct, index) => {
            let childProductOption = []
            if (childProduct.frontend_option) {
                childProductOption = childProduct.frontend_option.map((frontend_option, option_index) => {
                    return (
                        <div className="child-product-option" key={option_index}>
                            {frontend_option.label} : {frontend_option.value}
                        </div>
                    )
                })
            }
            return (
                <div key={index} className="child-product-item">
                    <div className="child-product-image">
                        <img alt={childProduct.image} src={childProduct.image} />
                    </div>
                    <div className="child-product-info">
                        <div className="child-product-name">
                            {childProduct.name}
                        </div>
                        <div className="child-product-options">
                            {childProductOption}
                        </div>
                        <div className='item-qty-price'>
                            <span className='qty'>{Identify.__("Quantity")}: {childProduct.quantity}</span>
                            <span className='price'><Price currencyCode={cartCurrencyCode} value={parseFloat(childProduct.product_final_price)} /></span>
                        </div>
                    </div>
                </div>
            )
        })
    }
    return  (
        <div className="child-products-details">
            {child_products}
        </div>
    )
}

export default Childproducts