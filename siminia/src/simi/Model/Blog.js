import { sendRequest } from 'src/simi/Network/RestMagento';

export const  getArticles = (callBack, params = {}) => {
    sendRequest('/rest/V1/simiconnector/articles', callBack, 'GET', params)
}

export const  getArticle = (callBack, postId) =>{
    sendRequest(`/rest/V1/simiconnector/articles/${postId}`, callBack, 'GET')
};