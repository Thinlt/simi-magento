import React from 'react'
import Wishlist from 'src/simi/App/Bianca/Customer/Account/Page/Wishlist'
import Identify from 'src/simi/Helper/Identify'
require('./index.scss')

const Sharedwishlist = props => {
    const {history} = props
    const wishlistCode = Identify.findGetParameter('code')
    if (!wishlistCode)
        history.push('/')
    return (
        <div className="container shared-wl-ctn">
            <div className="shared-wishlist-title">{Identify.__("Your friend's wishlist")}</div>
            <Wishlist public={true} wishlistCode={wishlistCode} />
        </div>
    )
}

export default Sharedwishlist