import React, { useState } from 'react';
import { connect } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Item from "./Item";
import {getWishlist} from 'src/simi/Model/Wishlist'
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { getCartDetails } from 'src/actions/cart';
// import Pagination from 'src/simi/BaseComponents/Pagination';
import Loading from 'src/simi/BaseComponents/Loading'
import {hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading'
// import {smoothScrollToView} from 'src/simi/Helper/Behavior'

require("./index.scss");

const Wishlist = props => {    
    const { history, toggleMessages, getCartDetails, wishlistCode} = props    
    const [data, setData] = useState(null) 

    const gotWishlist = (data) => {
        hideFogLoading()
        if (data.errors && data.errors.length) {
            const errors = data.errors.map(error => {
                return {
                    type: 'error',
                    message: error.message,
                    auto_dismiss: true
                }
            });
            toggleMessages(errors)
        } else {
            setData(data)
        }
    }

    const getWishlistItem = () => {
        const params = {limit: 9999, no_price: 1}
        if (wishlistCode)
            params.code = wishlistCode
        getWishlist(gotWishlist, params)
    }

    if (!data) {
        getWishlistItem()
    }

    const renderItem = (item, index) => {
        return (
            <div
                key={item.wishlist_item_id}
                className={`siminia-wishlist-item`}
            >
                <Item
                    item={item}
                    lazyImage={true}
                    className={`${
                        index % 4 === 0 ? "first" : ""
                    }`}
                    showBuyNow={true}
                    parent={this}
                    getWishlist={getWishlistItem}
                    toggleMessages={toggleMessages}
                    getCartDetails={getCartDetails}
                    history={history}
                /> 
            </div>
        )
    }

    let rows = null
    if (data && data.wishlistitems) {
        const {wishlistitems, total} = data
        if (total && wishlistitems && wishlistitems.length) {
            rows = wishlistitems.map((item, index) => renderItem(item, index))
        }
    } else {
        rows = <Loading />
    }
    return (
        <div className="account-my-wishlist">
            {TitleHelper.renderMetaHeader({
                    title:Identify.__('Favourites')
            })}
            <div className="account-favourites">
                <div className="product-grid">
                    {rows ? rows : (
                        <div className="no-product">
                            <p>
                                {Identify.__(
                                    "There are no products matching the selection"
                                )}
                            </p>
                        </div>
                    )}
                </div>
                {(rows && rows.length) && 
                    <div className="wishlist-action">
                        <div role="presentation" className="wishlist-sharing" onClick={() => history.push('/sharewishlist.html')}>{Identify.__('Share wishlist')}</div>
                    </div>
                }
            </div>
        </div>
    )
}

const mapDispatchToProps = {
    toggleMessages,
    getCartDetails
}
export default connect(
    null,
    mapDispatchToProps
)(Wishlist);