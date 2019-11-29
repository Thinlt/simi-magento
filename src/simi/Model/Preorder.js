import { sendRequest } from 'src/simi/Network/RestMagento';

export const startpreorderscomplete = (callBack, depositOrderId, quote_id = null) => {
    const getParams = quote_id?{quote_id}:{}
    getParams.depositOrderId = depositOrderId
    sendRequest('rest/V1/simiconnector/startpreorderscompletes', callBack, 'GET', getParams)
}
