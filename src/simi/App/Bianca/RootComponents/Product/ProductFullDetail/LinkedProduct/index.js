import React, {useEffect, useState} from 'react';
import Identify from 'src/simi/Helper/Identify';
import { simiUseQuery } from 'src/simi/Network/Query' ;
import getProductsBySkus from 'src/simi/queries/catalog/getProductsBySkus.graphql';
import Loading from "src/simi/BaseComponents/Loading";
import { GridItem } from 'src/simi/App/Bianca/BaseComponents/GridItem';
import {applySimiProductListItemExtraField} from 'src/simi/Helper/Product';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
// import {Carousel} from 'react-responsive-carousel';
import ItemsCarousel from 'react-items-carousel';
import ChevronLeft from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronLeft';
import ChevronRight from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronRight';

require('./linkedProduct.scss');

const responsive = {
    superLargeDesktop: {
      breakpoint: { max: 4000, min: 1470 },
      items: 5,
      chevronWidth: 72,
      iconWidth: 48
    },
    desktop: {
      breakpoint: { max: 1470, min: 1176 },
      items: 4,
      chevronWidth: 72,
      iconWidth: 48
    },
    tablet: {
      breakpoint: { max: 1176, min: 588 },
      items: 2,
      chevronWidth: 20,
      iconWidth: 16
    },
    mobile: {
      breakpoint: { max: 588, min: 0 },
      items: 1,
      chevronWidth: 20,
      iconWidth: 16
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

            let linkedProducts = [<Loading key={Identify.randomString(3)}/>]
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
                    if (width > itemSize.breakpoint.min && width <= itemSize.breakpoint.max) {
                        return true;
                    }
                }
                return false;
            });
            let numberCards = 4, chevWidth = 72, iconWidth = 24; // default values
            if (breakPoint.items) {
                numberCards = breakPoint.items;
                chevWidth = breakPoint.chevronWidth;
                iconWidth = breakPoint.iconWidth;
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
                            gutter={16} //Space between cards.
                            firstAndLastGutter={false}
                            activePosition={'center'}
                            chevronWidth={chevWidth}
                            disableSwipe={false}
                            alwaysShowChevrons={false}
                            numberOfCards={numberCards}
                            slidesToScroll={1}
                            outsideChevron={true}
                            showSlither={false}
                            activeItemIndex={activeItemIndex}
                            requestToChangeActive={setActiveItemIndex}
                            leftChevron={<ChevronLeft className="chevron-left" style={{width: `${iconWidth}px`}} />}
                            rightChevron={<ChevronRight className="chevron-right" style={{width: `${iconWidth}px`}} />}
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