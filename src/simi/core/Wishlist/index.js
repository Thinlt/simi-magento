import React from 'react'
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'

const Wishlist = props => {
    console.log(props)
    return (
        <React.Fragment>
            {TitleHelper.renderMetaHeader({
                title:Identify.__('Favourites')
            })}
            <div>loading</div>
        </React.Fragment>
    )
}


export default Wishlist;