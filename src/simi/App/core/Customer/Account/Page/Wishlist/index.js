import React, { useEffect } from 'react';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Loading from "src/simi/BaseComponents/Loading";
import Item from "./Item";
import { simiUseQuery } from 'src/simi/Network/Query'
import getWishlistQuery from 'src/simi/queries/getWishlist.graphql'


const Wishlist = props => {
    const {classes, history} = props    
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

    let rows = null
    if (!data || !data.wishlist) {
        return <Loading />;
    } else {
        const {wishlist} = data
        if (wishlist.items_count && wishlist.items && wishlist.items.length) {
            const wishlistData = wishlist.items;
            rows = wishlistData.map((item, index) => {
                const itemKey = `tablet-product-items-${
                    item.product_id
                }-${index}`;
                return (
                    <div
                        className={`${
                            index % 4 === 0 ? classes["first"] : ""
                        } ${classes['siminia-product-list-item']}`}
                        key={itemKey}
                    >
                        <Item
                            item={item}
                            lazyImage={true}
                            className={`${
                                index % 4 === 0 ? classes["first"] : ""
                            }`}
                            showBuyNow={true}
                            parent={this}
                            getWishlist={getWishlist}
                            history={history}
                        /> 
                    </div>
                );
            });
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

export default Wishlist;