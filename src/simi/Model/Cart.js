import { sendRequest } from 'src/simi/Network/RestMagento';

import { Util } from '@magento/peregrine';
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

export const addToCart = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams?{quote_id: getParams}:{}
    sendRequest('rest/V1/simiconnector/quoteitems', callBack, 'POST', getParams, params)
}

export const removeItemFromCart = (callBack, itemId, isSignedIn) => {
    if (isSignedIn)
        sendRequest('rest/V1/carts/mine/items/' + itemId, callBack, 'DELETE')
    else {
        const cartId = storage.getItem('cartId');
        if (!cartId) {
            callBack({});
            return;
        }
        sendRequest('rest/V1/guest-carts/'+ cartId + '/items/' + itemId, callBack, 'DELETE')
    }
}

export const updateCoupon = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams ? {quote_id: getParams} : {};
    sendRequest('rest/V1/simiconnector/quoteitems', callBack, 'PUT', getParams, params)
}

export const updateGiftVoucher = (callBack, params, isSignedIn) => {
    const cartId = storage.getItem('cartId');
    const giftVoucher = params['aw-giftcard'];

    if(isSignedIn){
        sendRequest('/rest/default/V1/carts/mine/aw-giftcard/' + giftVoucher, callBack, 'PUT', {}, params )
    } else {
        sendRequest('/rest/default/V1/guest-carts/' + cartId + '/aw-giftcard/' + giftVoucher , callBack, 'PUT',{},params)
    }
}

export const deleteGiftCode = (callBack, params, isSignedIn) => {
    const cartId = storage.getItem('cartId');
    const giftVoucher = params['aw-giftcard'];

    sendRequest('/rest/default/V1/guest-carts/' + cartId + '/aw-giftcard/' + giftVoucher, callBack, 'DELETE', {}, params)
}