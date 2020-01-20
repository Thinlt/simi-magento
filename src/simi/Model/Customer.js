import { sendRequest } from 'src/simi/Network/RestMagento';

export const createAccount = (callBack, accountInfo) => {
    sendRequest('rest/V1/simiconnector/customers', callBack, 'POST', {}, accountInfo)
}

export const socialLogin = (callBack, accountInfo) => {
    sendRequest('rest/V1/simiconnector/customers/sociallogin', callBack, 'POST', {}, accountInfo)
}

export const logout = (callBack, {}) => {
    sendRequest('rest/V1/simiconnector/customers/logout', callBack, 'POST', {}, {})
}

export const createPassword = (callBack, passwordInfo) => {
    sendRequest('rest/V1/simiconnector/customers/createpassword', callBack, 'POST', {}, passwordInfo)
}

export const simiSignIn = (callBack, postData) => {
    sendRequest('rest/V1/integration/customer/token', callBack, 'POST', {getSessionId: 1}, postData)
}

export const editCustomer = (callBack, postData) => {
    sendRequest('rest/V1/simiconnector/customers', callBack, 'PUT', {}, postData);
}

export const checkExistingCustomer = (callBack, email) => {
    const params = {};
    params['customer_email'] = email;
    sendRequest('rest/V1/simiconnector/customers/checkexisting', callBack, 'GET', params);
}

export const forgotPassword = (callBack, email) => {
    sendRequest('rest/V1/simiconnector/customers/forgetpassword', callBack, 'GET', {email});
}

export const vendorLogin = (callBack, postData) => {
    sendRequest('/rest/V1/simiconnector/vendor/login',callBack, 'POST',{getSessionId: 1},postData)
}

export const vendorRegister = (callBack, vendorInfo) => {
    sendRequest('/rest/V1/simiconnector/vendor/register', callBack, 'POST', {},vendorInfo)
}

export const getSizeChart = (callBack, customerId) => {
    sendRequest('/rest/V1/simiconnector/sizechart', callBack, 'GET',{customer_id: customerId});
}

export const getMyReserved = (callBack) => {
    sendRequest('/rest/V1/simiconnector/reserve', callBack, 'GET', {});
}

export const cancelMyReserved = (callBack, id) => {
    sendRequest('/rest/V1/simiconnector/reserve', callBack, 'PUT', {}, {id: id});
}

export const addGiftVoucher = (callBack, postData) => {
    sendRequest('/rest/V1/giftcard/mine/addcode', callBack, 'POST', {}, postData);
}

export const getGiftCodes = (callBack, page, limit, orderId, dir) => {
    sendRequest('/rest/V1/giftcard/mine/codes', callBack, 'GET', {page, limit, order: orderId, dir});
}

export const getHistoryCodes = (callBack, page, limit, orderId, dir) => {
    sendRequest('/rest/V1/giftcard/mine/history', callBack, 'GET', {page, limit, order: orderId, dir});
}

export const removeCode = (callBack, postData) => {
    sendRequest('/rest/V1/giftcard/mine/remove', callBack, 'POST', {}, postData);
}