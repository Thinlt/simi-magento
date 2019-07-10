import React from 'react';
import Identify from './Identify'
import { Price } from '@magento/peregrine'

export const formatPrice = (value, currency = null) => {
    if (!currency)
        currency = currencyCode()
    return (
        <Price 
            currencyCode={currency}
            value={value}
        />
    )
}

export const currencyCode = () => {
    const storeConfig = Identify.getStoreConfig()
    if (!storeConfig)
        return 'USD'
    if (storeConfig && storeConfig.simiStoreConfig 
        && storeConfig.simiStoreConfig.config && storeConfig.simiStoreConfig.config.base 
        && storeConfig.simiStoreConfig.config.base.currency_code)
        return storeConfig.simiStoreConfig.config.base.currency_code
    if (storeConfig.storeConfig && storeConfig.storeConfig.default_display_currency_code)
        return storeConfig.storeConfig.default_display_currency_code
}

export const taxConfig = () => {
    const storeConfig = Identify.getStoreConfig()
    if (storeConfig && storeConfig.simiStoreConfig 
        && storeConfig.simiStoreConfig.config && storeConfig.simiStoreConfig.config.tax)
        return storeConfig.simiStoreConfig.config && storeConfig.simiStoreConfig.config.tax
    return (
        {
            "tax_display_type": "3",
            "tax_display_shipping": "3",
            "tax_cart_display_price": "3",
            "tax_cart_display_subtotal": "3",
            "tax_cart_display_shipping": "3",
            "tax_cart_display_grandtotal": "0",
            "tax_cart_display_full_summary": "0",
            "tax_cart_display_zero_tax": "0",
            "tax_sales_display_price": "1",
            "tax_sales_display_subtotal": "1",
            "tax_sales_display_shipping": "1",
            "tax_sales_display_grandtotal": "0",
            "tax_sales_display_full_summary": "0",
            "tax_sales_display_zero_tax": "0"
        }
    )
}