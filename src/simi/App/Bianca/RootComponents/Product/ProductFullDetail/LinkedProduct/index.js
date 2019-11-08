import React, {useEffect, useState} from 'react';
import Identify from 'src/simi/Helper/Identify';
import { simiUseQuery } from 'src/simi/Network/Query' ;
import getProductsBySkus from 'src/simi/queries/catalog/getProductsBySkus.graphql';
import Loading from "src/simi/BaseComponents/Loading";
import { GridItem } from 'src/simi/BaseComponents/GridItem';
import {applySimiProductListItemExtraField} from 'src/simi/Helper/Product';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
// import {Carousel} from 'react-responsive-carousel';
import ItemsCarousel from 'react-items-carousel';

require('./linkedProduct.scss');

const responsive = {
    superLargeDesktop: {
      breakpoint: { max: 4000, min: 3000 },
      items: 5,
    },
    desktop: {
      breakpoint: { max: 3000, min: 1024 },
      items: 4,
    },
    tablet: {
      breakpoint: { max: 1024, min: 411 },
      items: 2,
    },
    mobile: {
      breakpoint: { max: 464, min: 0 },
      items: 1,
    },
};

const LinkedProducts = props => {
    const {product, history} = props
    const [activeItemIndex, setActiveItemIndex] = useState(0);
    const {width} = useWindowSize();
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
            const [queryResult, queryApi] = simiUseQuery(getProductsBySkus);
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
            }, [])

            let linkedProducts = <Loading />
            if (data && data.simiproducts && data.simiproducts.items) {
                let count = 0
                data.products = applySimiProductListItemExtraField(data.simiproducts)
                linkedProducts = data.products.items.map((item, index) => {
                    if (count < maxItem) {
                        count ++ 
                        const { small_image } = item;
                        const itemData =  { ...item, small_image: typeof small_image === 'object' ? small_image.url : small_image}
                        return (
                            <div key={index} className="linked-product-item">
                                <GridItem
                                    item={itemData}
                                    handleLink={handleLink}
                                    lazyImage={true}
                                />
                            </div>
                        )
                    }
                    return null
                });
            }

            // calculate items number for Carousel
            const _responseSize = Object.values(responsive);
            const breakPoint = _responseSize.find((itemSize) => {
                if (itemSize.breakpoint) {
                    if (width >= itemSize.breakpoint.min && width < itemSize.breakpoint.max) {
                        return true;
                    }
                }
                return false;
            });
            let numberCards = 4;
            if (breakPoint.items) {
                numberCards = breakPoint.items;
            }

            return (
                <div className="linked-product-ctn">
                    <h2 className="title">
                        <span>
                        {
                            link_type==='related'?Identify.__('Related Products'):link_type==='crosssell'?Identify.__('You may also be interested in'):''
                        }
                        </span>
                    </h2>
                    <div className="linked-products">
                        <ItemsCarousel
                            infiniteLoop={false}
                            gutter={12}
                            activePosition={'center'}
                            chevronWidth={60}
                            disableSwipe={false}
                            alwaysShowChevrons={false}
                            numberOfCards={numberCards}
                            slidesToScroll={2}
                            outsideChevron={true}
                            showSlither={false}
                            firstAndLastGutter={false}
                            activeItemIndex={activeItemIndex}
                            requestToChangeActive={setActiveItemIndex}
                            rightChevron={'>'}
                            leftChevron={'<'}
                        >
                            {linkedProducts}
                        </ItemsCarousel>
                    </div>
                </div>
            )
        }
    }

    return ''
}
export default LinkedProducts