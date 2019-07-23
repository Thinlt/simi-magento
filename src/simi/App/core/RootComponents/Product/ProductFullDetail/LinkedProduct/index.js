import React, {useEffect} from 'react'
import Identify from 'src/simi/Helper/Identify'
import classes from './linkedProduct.css'
import { simiUseQuery } from 'src/simi/Network/Query' 
import getLinkedProducts from 'src/simi/queries/catalog/getLinkedProducts.graphql'
import Loading from "src/simi/BaseComponents/Loading"
import { GridItem } from 'src/simi/BaseComponents/GridItem'

const LinkedProducts = props => {
    const {product, history} = props
    const link_type = props.link_type?props.link_type:'related'
    const maxItem = 8 //max 10 items
    const handleLink = (link) => {
        history.push(link)
    }
    if (product.product_links && product.product_links.length) {
        const matchedSkus = []
        product.product_links.map((product_link) => {
            if (product_link.link_type === link_type)
                matchedSkus.push(product_link.linked_product_sku)
        })
        if (matchedSkus.length) {
            const [queryResult, queryApi] = simiUseQuery(getLinkedProducts);
            const {data} = queryResult
            const {runQuery} = queryApi

            useEffect(() => {
                runQuery({
                    variables: {
                        stringSku: matchedSkus,
                        currentPage: 1,
                        pageSize: maxItem,
                    }
                })
            },[data])

            let linkedProducts = <Loading />
            if (data && data.products && data.products.items) {
                linkedProducts = []
                data.products.items.every((item, index) => {
                    let count = 0
                    if (count < maxItem) {
                        count ++ 
                        const { small_image } = item;
                        const itemData =  {
                            ...item,
                            small_image:
                                typeof small_image === 'object' ? small_image.url : small_image
                        }
                        if (itemData)
                            linkedProducts.push (
                                <div key={index} className={classes.linkedProductItem}>
                                    <GridItem
                                        item={itemData}
                                        classes={classes}
                                        handleLink={handleLink}
                                        lazyImage={true}
                                    />
                                </div>
                            )
                        return true
                    }
                    return false
                });
            }

            return (
                <div className={classes.linkedProductCtn}>
                    <h2 className={classes.title}>
                        <span>
                        {
                            link_type==='related'?Identify.__('Related Products'):link_type==='crosssell'?Identify.__('You may also be interested in'):''
                        }
                        </span>
                    </h2>
                    <div className={classes.linkedProducts}>
                        {linkedProducts}
                    </div>
                </div>
            )
        }
    }

    return ''
}
export default LinkedProducts