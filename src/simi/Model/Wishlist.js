import { sendRequest } from 'src/simi/Network/RestMagento';

export const getWishlist = (callBack) => {
    sendRequest('rest/V1/simiconnector/wishlistitems', callBack)
}
