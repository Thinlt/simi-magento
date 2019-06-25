import { addRequestVars } from 'src/simi/Helper/Network'
import Identify from 'src/simi/Helper/Identify'

const prepareData = (endPoint, getData, method, header, bodyData) => {
    let requestMethod = method
    let requestEndPoint = endPoint
    const requestHeader = header
    const requestBody = bodyData

    //add session/store/currencies
    getData = addRequestVars(getData)

    //incase no support PUT & DELETE
    try {
        const merchantConfigs = Identify.getStoreConfig();
        if (method.toUpperCase() === 'PUT' 
            && merchantConfigs.simiStoreConfig.config.base.is_support_put !== undefined
            && parseInt(merchantConfigs.simiStoreConfig.config.base.is_support_put, 10) === 0) {
            requestMethod = 'POST';
            getData.is_put = '1'
        }

        if (method.toUpperCase() === 'DELETE' && 
            merchantConfigs.simiStoreConfig.config.base.is_support_delete !== undefined
            && parseInt(merchantConfigs.simiStoreConfig.config.base.is_support_delete, 10) === 0) {
            requestMethod = 'POST';
            getData.is_delete = '1'
        }
        
    } catch (err) {}
    let dataGetString = Object.keys(getData).map(function (key) {
        return encodeURIComponent(key) + '=' +
            encodeURIComponent(getData[key]);
    })
    dataGetString = dataGetString.join('&')
    if(requestEndPoint.includes('?')){
        requestEndPoint += "&" + dataGetString;
    } else {
        requestEndPoint += "?" + dataGetString;
    }

    //header
    requestHeader['accept'] = 'application/json'
    requestHeader['content-type'] = 'application/json'

    return {requestMethod, requestEndPoint, requestHeader, requestBody}
}

export async function sendRequest(endPoint, callBack, method='GET', getData= {}, bodyData= {}) {
    const header = {cache: 'default', mode: 'cors'}
    const {requestMethod, requestEndPoint, requestHeader, requestBody} = prepareData(endPoint, getData, method, header, bodyData)
    const requestData = {}
    requestData['method'] = requestMethod
    requestData['headers'] = requestHeader
    requestData['body'] = (requestBody && requestMethod !== 'GET')?JSON.stringify(requestBody):null
    requestData['credentials'] = 'same-origin';
    
    const _request = new Request(requestEndPoint, requestData);
    let result = null

    fetch(_request)
        .then(function (response) {
            if (response.ok) {
                return response.json();
            }
        })
        .then(function (data) {
            if (data) {
                if (Array.isArray(data) && data.length === 1 && data[0])
                    result = data[0]
                else
                    result = data
            } else
                result =  {'error' : Identify.__('Network response was not ok')}
            callBack(result)
        }).catch((error) => {
        result =  {'error' : Identify.__('Something when wrong')}
        console.warn(error);
        callBack(result)
    });
}
