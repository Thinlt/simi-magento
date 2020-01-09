import { sendRequest } from 'src/simi/Network/RestMagento';

export const  getUrlFromPathBySimiUrlDict = (callBack, urlPath) => {
    sendRequest('/rest/V1/simiconnector/urldicts/detail', callBack, 'GET', {url: urlPath})
}