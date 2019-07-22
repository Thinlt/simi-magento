import React, { useEffect } from 'react';
import { connect } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Item from "./Item";
import { simiUseQuery } from 'src/simi/Network/Query'
import getWishlistQuery from 'src/simi/queries/wishlist/getWishlist.graphql'
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import { getCartDetails } from 'src/actions/cart';
import {hideFogLoading} from 'src/simi/BaseComponents/Loading/GlobalLoading';
import classes from './index.css'
import Pagination from 'src/simi/BaseComponents/Pagination';

const Wishlist = props => {
    const { history, toggleMessages, getCartDetails} = props    
    const [queryResult, queryApi] = simiUseQuery(getWishlistQuery, false);
    const { data } = queryResult;
    const { runQuery } = queryApi;

    const getWishlist = () => {
        runQuery({});
    }

    useEffect(() => {
        if (!data) {        
            getWishlist();
        }
    }, [data]);

    
    const renderItem = (item, index) => {
        return (
            <div
                className={`${
                    index % 4 === 0 ? classes["first"] : ""
                } ${classes['siminia-wishlist-item']}`}
                key={item.id}
            >
                <Item
                    item={item}
                    lazyImage={true}
                    className={`${
                        index % 4 === 0 ? classes["first"] : ""
                    }`}
                    classes={classes}
                    showBuyNow={true}
                    parent={this}
                    getWishlist={getWishlist}
                    toggleMessages={toggleMessages}
                    getCartDetails={getCartDetails}
                    history={history}
                /> 
            </div>
        )
    }

    let rows = null
    if (data && data.wishlist) {
        hideFogLoading()
        const {wishlist} = data
        if (wishlist.items_count && wishlist.items && wishlist.items.length) {
            const wishlistData = wishlist.items;
            return (
                rows = <Pagination data={wishlistData} renderItem={renderItem} classes={classes} itemsPerPageOptions={[8, 16, 32]} limit={8}/>
            )
        }
    }

    return (
        <div className={classes["account-my-wishlist"]}>
            {TitleHelper.renderMetaHeader({
                title:Identify.__('Favourites')
            })}
            <div className={classes["customer-page-title"]}>
                {Identify.__("Favourites")}
            </div>
            <div className={classes["account-favourites"]}>
                <div className={classes["product-grid"]}>
                    {rows ? rows : (
                        <div className={classes["no-product"]}>
                            <p>
                                {Identify.__(
                                    "There are no products matching the selection"
                                )}
                            </p>
                        </div>
                    )}
                </div>
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