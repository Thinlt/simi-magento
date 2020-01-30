import React, {useState} from 'react'
import ItemsCarousel from 'react-items-carousel';
import Identify from "src/simi/Helper/Identify";
import ScrollerItem from "./ScrollerItem";
import ChevronLeft from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronLeft';
import ChevronRight from 'src/simi/App/Bianca/BaseComponents/Icon/ChevronRight';

/**
 * @param {*} props 
 * props.data - data items
 * props.filter - filter items function - return true | false
 * props.slideSetting - Slide setting
 * props.renderItem - render item component
 * props.lastItems - to add more items to slider
 */
const Scroller = props => {
    const {history, isPhone, renderItem, lastItems, initItemIndex} = props;
    const data = props.data;
    const [activeItemIndex, setActiveItemIndex] = useState(initItemIndex || 0);

    const slideSettings = {
        infiniteLoop: false,
        gutter: 16, //Space between cards.
        firstAndLastGutter: false,
        activePosition: 'center',
        disableSwipe: false,
        numberOfCards: 6,
        slidesToScroll: 1,
        outsideChevron: true,
        showSlither: false,
        activeItemIndex: activeItemIndex,
        requestToChangeActive: setActiveItemIndex,
        chevronWidth: 24,
        leftChevron: <ChevronLeft style={{width: '24px', height: '24px'}} />,
        rightChevron: <ChevronRight style={{width: '24px', height: '24px'}} />,
        outsideChevron: true,
        alwaysShowChevrons: false,
        slidesToScroll: 6,
        disableSwipe: false,
        onStateChange: props.onStateChange ? props.onStateChange() : null,
        classes: { wrapper: "wrapper", itemsWrapper: 'items-wrapper', itemsInnerWrapper: 'items-inner-wrapper', itemWrapper: 'item-wrapper', rightChevronWrapper: 'right-chevron-wrapper', leftChevronWrapper: 'left-chevron-wrapper' },
        ...props.slideSettings
    }
    const {showChevron} = slideSettings;
    const _settings = {...slideSettings,
        leftChevron: showChevron === false ? null : slideSettings.leftChevron,
        rightChevron: showChevron === false ? null : slideSettings.rightChevron,
    }

    const onClickItem = props.onClickItem || null;
    let items = [];
    if(data){
        items = data.filter((item) => {
            if (props.filter) return props.filter(item); else return true;
        }).map((item, index) => {
            return (
                <div key={index} style={{cursor: 'pointer'}}>
                    <ScrollerItem item={item} history={history} isPhone={isPhone} renderItem={renderItem} index={index} onClick={onClickItem}/>
                </div>
            );
        });
    }

    if (props.children && props.children instanceof Array) {
        props.children.forEach((item)=>{
            items.push(item);
        });
    }

    if (lastItems && lastItems instanceof Array) {
        if (Identify.isRtl()) {
            lastItems.reverse().forEach(item => {
                items.unshift(item);
            });
        } else {
            lastItems.forEach(item => {
                items.push(item);
            });
        }
    }

    let rtlStyle = {};
    if (Identify.isRtl()) {
        rtlStyle = {direction: 'ltr'}
    }

    return (
        <div className={`scroller ${Identify.isRtl() ? 'scroller-rtl' : ''}`} style={rtlStyle}>
            <div className={`container scroller-container`}>
                <ItemsCarousel
                    {..._settings}>
                    {items}
                </ItemsCarousel>
            </div>
        </div>
    );
}

export default Scroller;