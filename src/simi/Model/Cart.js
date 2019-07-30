import { sendRequest } from 'src/simi/Network/RestMagento';

import { Util } from '@magento/peregrine';
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

export const addToCart = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams?{quote_id: getParams}:{}
    sendRequest('rest/V1/simiconnector/quoteitems', callBack, 'POST', getParams, params)
}

export const updateCoupon = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams ? {quote_id: getParams} : {};
    sendRequest('rest/V1/simiconnector/quoteitems', callBack, 'PUT', getParams, params)
}

export const calculateCart = (callBack) => {
    const cartId = storage.getItem('cartId');
    if (!cartId)
        callBack({})
    const params = {cartId: cartId, address: {}}
    sendRequest('rest/V1/carts/mine/billing-address', callBack, 'POST', {}, params)
}