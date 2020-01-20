import { sendRequest } from 'src/simi/Network/RestMagento';

export const sendOTPForLogin = (params, callBack) => {
    sendRequest('/rest/V1/simiconnector/sentotpforlogin', callBack, 'GET', params)
}

export const verifyOTPForLogin = (mobile, otp, callBack) => {
    sendRequest('/rest/V1/simiconnector/verifyotpforlogin', callBack, 'GET', {mobile, otp})
}