import { sendRequest } from 'src/simi/Network/RestMagento';

export const getOrderDetail = (id,callBack) => {
    sendRequest(`/rest/V1/simiconnector/orders/${id}`, callBack)
}