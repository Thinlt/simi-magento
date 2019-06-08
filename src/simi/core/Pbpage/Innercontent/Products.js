import React, { useEffect } from 'react';
import LoadingSpiner from "src/simi/BaseComponents/Loading/LoadingSpiner"
import getCategory from 'src/simi/queries/getCateProductsNoFilter.graphql'
import { useQuery } from '@magento/peregrine'
import Product from './Product'

/*
import {GridItemHoc} from "../../../Tapita/Products/HoC"
import LoadingSpiner from "../../../../BaseComponent/Loading/LoadingSpiner"
import ProductModelCollection from "../../../../Model/product/ModelCollection"
import Identify from '../../../../Helper/Identify'
import * as Constants from "../../../../Config/Constants";
*/

const Products = props => {
    if (!props.item || !props.item.data || !props.item.data.openCategoryProducts) {
        return ''
    }
    const id = props.item.data.openCategoryProducts
    const pageSize = 12
    const currentPage = 0
    const isPhone = window.innerWidth < 768

    const [queryResult, queryApi] = useQuery(getCategory);
    const { data, error, loading } = queryResult;
    const { runQuery, setLoading } = queryApi;

    useEffect(() => {
        setLoading(true);
        runQuery({
            variables: {
                id: Number(id),
                pageSize: Number(pageSize),
                currentPage: Number(currentPage),
                stringId: String(id),
            }
        });
    }, [id, pageSize, currentPage]);

    if (data && data.products && data.products.items) {
        let count = 0
        let maxItem = 4
        const products = []
        const style = {minWidth: 170, display: 'inline-block'}
        style.width = '50%'
        if (!isPhone) {
            style.width = '25%'
        }
        if (props.item && props.item.type === 'product_scroll') {
            maxItem = 12
            style.width = '30%'
            if (!isPhone) {
                style.width = '25%'
            }
        }

        data.products.items.every((item, index) => {
            const itemKey = `pb-product-items-${index}-${item.entity_id}`;
            if (count < maxItem) {
                count ++ 
                const { small_image } = item;
                const itemData =  {
                    ...item,
                    small_image:
                        typeof small_image === 'object' ? small_image.url : small_image
                }
                
                products.push (
                    <div key={itemKey} className="pb-product-item" style={style}>
                        <Product
                            item={itemData}
                            classes={{}}
                            />
                    </div>
                )
                return true
            }
            return false
        });
        return products
    }
    return <LoadingSpiner />        
}

export default Products