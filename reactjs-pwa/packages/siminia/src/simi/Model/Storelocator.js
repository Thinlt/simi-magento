import { sendRequest } from 'src/simi/Network/RestMagento';

export const getStorelocators = (callBack) => {
    sendRequest('rest/V1/simiconnector/storelocations', callBack, 'GET')
}
