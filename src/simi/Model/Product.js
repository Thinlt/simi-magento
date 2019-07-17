import { sendRequest } from 'src/simi/Network/RestMagento';


export const uploadFile = (callBack, postData) =>{
    sendRequest(`rest/V1/simiconnector/uploadfiles`, callBack, 'POST', {}, postData)
}

export const getProductDetail = (callBack, productId) => {
    sendRequest(`rest/V1/simiconnector/products/${productId}`, callBack);
}