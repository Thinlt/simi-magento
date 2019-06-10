import React, { useEffect, useState } from 'react';
import LoadingSpiner from "src/simi/BaseComponents/Loading/LoadingSpiner"
import getCategory from 'src/simi/queries/getCateProductsNoFilter.graphql'
import { useQuery } from '@magento/peregrine'
import { GridItem } from 'src/simi/BaseComponents/GridItem'

const Products = props => {
    if (!props.item || !props.item.data || !props.item.data.openCategoryProducts) {
        return ''
    }
    const [isPhone, setIsPhone] = useState(window.innerWidth < 768)
    $(window).resize(function () {
        const width = window.innerWidth;
        const newIsPhone = width < 1024;
        if(newIsPhone !== isPhone){
            setIsPhone(newIsPhone)
        }
    })

    const id = props.item.data.openCategoryProducts
    const pageSize = 12
    const currentPage = 0

    const [queryResult, queryApi] = useQuery(getCategory);
    const { data } = queryResult;
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
        let maxItem = 6
        const products = []
        const style = {minWidth: 170, display: 'inline-block', padding: 5}
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
                        <GridItem
                            item={itemData}
                            classes={{}}
                            handleLink={props.handleLink}
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