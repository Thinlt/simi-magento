import { sendRequest } from 'src/simi/Network/RestMagento';

import { Util } from '@magento/peregrine';
const { BrowserPersistence } = Util;
const storage = new BrowserPersistence();

export const addToCart = (callBack, params) => {
    let getParams = storage.getItem('cartId');
    getParams = getParams?{quote_id: getParams}:{}
    sendRequest('rest/V1/simiconnector/quoteitems', callBack, 'POST', getParams, params)
}
