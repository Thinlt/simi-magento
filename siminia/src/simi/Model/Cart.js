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

export const updateGiftVoucher = (callBack, giftVoucher, isSignedIn, storeCode) => {
    const cartId = storage.getItem('cartId');
    if(isSignedIn){
        sendRequest('/rest/' + storeCode + '/V1/carts/mine/aw-giftcard/' + giftVoucher, callBack, 'PUT', {} )
    } else {
        sendRequest('/rest/' + storeCode + '/V1/guest-carts/' + cartId + '/aw-giftcard/' + giftVoucher , callBack, 'PUT',{})
    }
}

export const deleteGiftCode = (callBack, giftVoucher, isSignedIn, storeCode) => {
    const cartId = storage.getItem('cartId');
    if(isSignedIn){
        sendRequest('/rest/' + storeCode + '/V1/carts/mine/aw-giftcard/' + giftVoucher, callBack, 'DELETE', {})
    } else {
        sendRequest('/rest/' + storeCode + '/V1/guest-carts/' + cartId + '/aw-giftcard/' + giftVoucher , callBack, 'DELETE',{})
    }
}

export const updateEstimateShipping = (callBack, params, isSignedIn) => {
    const cartId = storage.getItem('cartId');

    if(isSignedIn){
        sendRequest('/rest/default/V1/carts/mine/estimate-shipping-methods', callBack, 'POST', {}, params )
    } else {
        sendRequest('/rest/default/V1/guest-carts/' + cartId + '/estimate-shipping-methods' , callBack, 'POST',{},params)
    }
}

export const updateSubProductSpecialItem = (callBack, cartItemId, subProductSku, quantity) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams ? {quote_id: getParams} : {};
    getParams.subproductsku = subProductSku
    getParams.newquantity = quantity
    sendRequest('rest/V1/simiconnector/quoteitems/' + cartItemId, callBack, 'PUT', getParams)
}

export const removeAllItems = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams ? {quote_id: getParams} : {};
    sendRequest('rest/V1/simiconnector/quoteitems/', callBack, 'PUT', getParams, params);
}