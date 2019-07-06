//prepare product price and options
export const prepareProduct = (product) => {
    const modedProduct = Object.assign({}, product)
    const price = modedProduct.price
    price.has_special_price = false
    if (price.regularPrice.amount.value < price.minimalPrice.amount.value) {
        price.has_special_price = true
    }
    price.show_ex_in_price = 1

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