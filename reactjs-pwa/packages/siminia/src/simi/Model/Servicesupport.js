import { sendRequest } from 'src/simi/Network/RestMagento';

export const switchServiceSupport = (callBack, quoteItemId, quote_id, add_buy_service = false) => {
    const getParams = {quote_id}
    if (add_buy_service)
        getParams.add_buy_service = 1
    else
        getParams.remove_buy_service = 1
    sendRequest('rest/V1/simiconnector/quoteitems/' + quoteItemId, callBack, 'GET', getParams)
}
