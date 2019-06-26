import React, {useState, useEffect} from 'react'
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import { getWishlist } from 'src/simi/Model/Wishlist'


const Wishlist = props => {
    const [data, setData] = useState(null)
    useEffect(() => {
        console.log(data)
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


export default Wishlist;