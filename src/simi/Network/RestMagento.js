import { RestApi } from '@magento/peregrine';
import { addRequestVars } from 'src/simi/Helper/Network'
const { request } = RestApi.Magento2;

export async function sendRequest(method='GET', getData= {}, bodyData= {}, endPoint, callback) {
    dataGet = addRequestVars(dataGet)
    let dataGet = Object.keys(getData).map(function (key) {
        return encodeURIComponent(key) + '=' +
            encodeURIComponent(data[key]);
    })
    dataGet = dataGet.join('&')
    if(endPoint.includes('?')){
        endPoint += "&" + dataGet;
    } else {
        endPoint += "?" + dataGet;
    }
    const response = await request(endPoint, {
        method: method,
        body: JSON.stringify(bodyData)
    });
    console.log(response)
    callback(response)
}
