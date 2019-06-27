import React, { useEffect } from 'react';
import Identify from 'src/simi/Helper/Identify'
import TitleHelper from 'src/simi/Helper/TitleHelper'
import Loading from "src/simi/BaseComponents/Loading";
import Item from "./Item";
import { simiUseQuery } from 'src/simi/Network/Query'
import getWishlist from 'src/simi/queries/getWishlist.graphql'


const Wishlist = props => {
    const {classes} = props    
    const [queryResult, queryApi] = simiUseQuery(getWishlist);
    const { data } = queryResult;
    const { runQuery } = queryApi;
    let isLoaded = 0
    const setIsLoaded = () => {
        isLoaded = 1
    }

    useEffect(() => {
        if (!data || isLoaded) {        
            runQuery({});
        }
    }, [data, isLoaded]);

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
                            setIsLoaded={setIsLoaded}
                            isLoaded={isLoaded}
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