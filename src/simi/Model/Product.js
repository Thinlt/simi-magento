import { sendRequest } from 'src/simi/Network/RestMagento';


export const uploadFile = (callBack, postData) =>{
    sendRequest(`rest/V1/simiconnector/uploadfiles`, callBack, 'POST', {}, postData)
}

export const getReviews = (callBack, id) => {
    sendRequest(`rest/V1/simiconnector/reviews`, callBack, 'GET', {'filter[product_id]': id, limit: 999}, {})
}

export const submitReview =(callBack, params)=>{
    sendRequest(`rest/V1/simiconnector/reviews`, callBack, 'POST', {}, params)
}