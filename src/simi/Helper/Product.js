import {taxConfig} from './Pricing'

//prepare product price and options
export const prepareProduct = (product) => {
    const modedProduct = JSON.parse(JSON.stringify(product))
    const price = modedProduct.price
    price.has_special_price = false
    if (price.regularPrice.amount.value < price.minimalPrice.amount.value) {
        price.has_special_price = true
    }
    const merchantTaxConfig = taxConfig()
    price.show_ex_in_price = (price.regularPrice.adjustments && price.regularPrice.adjustments.length)?parseInt(merchantTaxConfig.tax_display_type, 10) === 3?1:0:0

    price.minimalPrice.excl_tax_amount = addExcludedTaxAmount(price.minimalPrice.amount, price.minimalPrice.adjustments)
    price.regularPrice.excl_tax_amount = addExcludedTaxAmount(price.regularPrice.amount, price.regularPrice.adjustments)
    price.maximalPrice.excl_tax_amount = addExcludedTaxAmount(price.maximalPrice.amount, price.maximalPrice.adjustments)
    modedProduct.price = price
    return modedProduct
}

const addExcludedTaxAmount = (amount, adjustments) => {
    let excludedTaxPrice = amount.value
    if (adjustments && adjustments.length) {
        adjustments.forEach(adjustment => {
            if (adjustment.description === 'INCLUDED' && adjustment.code === 'TAX') {
                excludedTaxPrice = excludedTaxPrice - adjustment.amount.value
            }
        })
    }
    return {
        value :excludedTaxPrice,
        currency: amount.currency
    }
}