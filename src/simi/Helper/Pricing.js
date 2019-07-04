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