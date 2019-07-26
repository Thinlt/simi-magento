import React from 'react'
import Product from 'src/simi/App/core/RootComponents/Product'
import Category from 'src/simi/App/core/RootComponents/Category'
import resolveUrl from 'src/simi/queries/urlResolver.graphql'
import { simiUseQuery } from 'src/simi/Network/Query';
import Loading from 'src/simi/BaseComponents/Loading'
import { getDataFromUrl } from 'src/simi/Helper/Url';

var parsedFromDocumentOnce = false
const TYPE_PRODUCT = 'PRODUCT'
const TYPE_CATEGORY = 'CATEGORY'

const NoMatch = props => {
    const {location} = props
    const renderByTypeAndId = (type, id, preloadedData = null) => {
        if (type === TYPE_PRODUCT)
            return <Product {...props} preloadedData={preloadedData}/>
        else if (type === TYPE_CATEGORY)
            return <Category {...props} id={parseInt(id, 10)}/>
    }

    if (
        !parsedFromDocumentOnce &&
        document.body.getAttribute('data-model-type') &&
        document.body.getAttribute('data-model-id')
    ) {
        const type = document.body.getAttribute('data-model-type')
        const id = document.body.getAttribute('data-model-id')
        parsedFromDocumentOnce = true
        const result = renderByTypeAndId(type, id)
        if (result)
            return result
    } else if (location && location.pathname) {
        const pathname = location.pathname

        //load from dict
        const dataFromDict = getDataFromUrl(pathname)
        if (dataFromDict && dataFromDict.id) {
            let type = TYPE_CATEGORY
            const id = dataFromDict.id
            if (dataFromDict.sku)  {
                type = TYPE_PRODUCT
            }
            const result = renderByTypeAndId(type, id, dataFromDict)
            if (result)
                return result
        }
        //get type from server
        const [queryResult, queryApi] = simiUseQuery(resolveUrl);
        const { data } = queryResult;
        const { runQuery } = queryApi;
        if (!data) {
            runQuery({
                variables: {
                    urlKey: pathname
                }
            });
        }
        if (data && data.urlResolver) {
            const result = renderByTypeAndId(data.urlResolver.type, data.urlResolver.id)
            if (result)
                return result
        }
    }

    return (
        <Loading />
    )
}
export default NoMatch