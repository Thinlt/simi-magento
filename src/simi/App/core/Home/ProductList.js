import React, { useEffect } from 'react'
import { simiUseQuery } from 'src/simi/Network/Query' 
import getCategory from 'src/simi/queries/getCateProductsNoFilter.graphql'

const ProductList = props => {
    const {classes, homeData} = props;
    const [queryResult, queryApi] = simiUseQuery(getCategory, false);
    const {data} = queryResult
    const {runQuery} = queryApi

    useEffect(() => {
        runQuery({
            variables: {

            }
        })
    })

    return '';
}

export default ProductList;