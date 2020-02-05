import { sendRequest } from 'src/simi/Network/RestMagento';

export const getAllOrders = (callBack) => {
    sendRequest(`/rest/V1/simiconnector/orders`, callBack, 'GET', {limit: 999999, offset: 0})
}

export const getTryToBuyOrders = (callBack) => {
    sendRequest(`/rest/V1/simiconnector/mytrytobuys`, callBack, 'GET', {limit: 999999, offset: 0})
}

export const getOrderDetail = (id,callBack) => {
    sendRequest(`/rest/V1/simiconnector/orders/${id}`, callBack)
}

export const getReOrder = (id, callBack) => {
    sendRequest(`/rest/V1/simiconnector/orders/${id}?reorder=1`,callBack)
}