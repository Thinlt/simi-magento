import { sendRequest } from 'src/simi/Network/RestMagento';

export const submitQuote = (callBack, postData) => {
    sendRequest(`/rest/V1/simiconnector/service`, callBack, 'POST', {}, postData)
}

export const uploadFile = (callBack, postData) =>{
    sendRequest(`rest/V1/simiconnector/uploadfiles`, callBack, 'POST', {}, postData)
}