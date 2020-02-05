import React, { useState} from 'react';
import { GridItem } from 'src/simi/App/Bianca/BaseComponents/GridItem';
import useWindowSize from 'src/simi/App/Bianca/Hooks';
import ItemsCarousel from 'react-items-carousel';
import ChevronLeft from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronLeft';
import ChevronRight from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronRight';
import {getRecentViewedProducts} from '../../Helper/Biancaproduct'
import Identify from 'src/simi/Helper/Identify';
import CompareProduct from '../CompareProducts/index';

require('./recentViewed.scss');

const responsive = {
    superLargeDesktop: {
      breakpoint: { max: 4000, min: 1900 },
      items: 5,
      chevronWidth: 72,
      iconWidth: 48
    },
    desktop: {
      breakpoint: { max: 1900, min: 1176 },
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
      items: 2,
      chevronWidth: 20,
      iconWidth: 16
    },
};

const RecentViewed = props => {
    const isPhone = window.innerWidth < 1024 
    const [activeItemIndex, setActiveItemIndex] = useState(0);
    const [openCompareModal, setOpenCompareModal] = useState(false);
    const {width} = useWindowSize();
    const maxItem = 8 //max 10 items
    const productsRecent = getRecentViewedProducts();

    const closeCompareModal = () => {
        setOpenCompareModal(false);
    }

    const showModalCompare = () => {
        setOpenCompareModal(true);
    }

    if (productsRecent && productsRecent.length) {
        let count = 0
        const recentProducts = productsRecent.map((item, index) => {
            if (count < maxItem) {
                count ++ 
                const { small_image } = item;
                const itemData =  { ...item, small_image: typeof small_image === 'object' ? small_image.url : small_image}
                return (
                    <div key={index} className="recent-product-item">
                        <GridItem
                            item={itemData}
                            lazyImage={true}
                            openCompareModal={showModalCompare}
                        />
                    </div>
                )
            }
            return null
        });

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
        let numberCards = 4, chevWidth = 72, iconWidth = 24, gutter=isPhone?11:16; // default values
        if (breakPoint.items) {
            numberCards = breakPoint.items;
            chevWidth = breakPoint.chevronWidth;
            iconWidth = breakPoint.iconWidth;
            if (breakPoint.gutter) gutter = breakPoint.gutter;
        }

        return (
            <React.Fragment>
                <div className="recent-viewed-slide">
                    <div className="recent-product-ctn">
                        <div className="recent-viewed-title">
                            {Identify.__('Recently Viewed Products')}
                        </div>
                        <div className="recent-products">
                            <CompareProduct history={history} openModal={openCompareModal} closeModal={closeCompareModal}/> 
                            <ItemsCarousel
                                infiniteLoop={false}
                                gutter={gutter}
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
                                classes={{ wrapper: "wrapper", itemsWrapper: 'items-wrapper', itemsInnerWrapper: 'items-inner-wrapper', itemWrapper: 'item-wrapper', rightChevronWrapper: 'right-chevron-wrapper', leftChevronWrapper: 'left-chevron-wrapper' }}
                            >
                                {recentProducts}
                            </ItemsCarousel>
                        </div>
                    </div>
                </div>
            </React.Fragment>
        )
    }

    return ''
}
export default RecentViewed