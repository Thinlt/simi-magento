import { sendRequest } from 'src/simi/Network/RestMagento';

export const getVendors = (callBack, vendorIds) => {
    const ids = vendorIds.join(',')
    sendRequest(`/rest/V1/simiconnector/vendors`, callBack, 'POST', {limit: 999}, {ids})
}