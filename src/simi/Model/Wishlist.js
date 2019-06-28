import { sendRequest } from 'src/simi/Network/RestMagento';

export const getWishlist = (callBack) => {
    sendRequest('rest/V1/simiconnector/wishlistitems', callBack)
}

export const removeWlItem = (id, callBack) => {
    sendRequest(`rest/V1/simiconnector/wishlistitems/${id}`, callBack, 'DELETE')
}

export const addWlItemToCart = (id, callBack) =>{
    sendRequest(`rest/V1/simiconnector/wishlistitems/${id}`, callBack, 'GET', {add_to_cart: 1})
}

/*
example of using
import { getWishlist } from 'src/simi/Model/Wishlist'
const Wishlist = props => {
    const [data, setData] = useState(null)
    useEffect(() => {
        if (!data)
            getWishlist(setData)
    });
    return (
        <React.Fragment>
            {TitleHelper.renderMetaHeader({
                title:Identify.__('Favourites')
            })}
            {data?JSON.stringify(data):'loading'}
        </React.Fragment>
    )
}
*/