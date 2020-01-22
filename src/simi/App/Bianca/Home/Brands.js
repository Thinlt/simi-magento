import React from 'react'
import Identify from "src/simi/Helper/Identify";
import Scroller from "./Scroller";

const Brands = props => {
    const { history, isPhone, data} = props;

    const slideSettings = {
        showChevron: true,
        chevronWidth: isPhone ? 36 : 72,
        numberOfCards: isPhone ? 3 : 6,
        slidesToScroll: isPhone ? 1 : 2,
        gutter: isPhone ? 12.5 : 16
    }

    data.forEach((item, index)=>{
        item.url = `/brands.html?filter=%7B"brand"%3A"${item.option_id}"%7D`;
    });

    let startItemIndex = 0;
    if (Identify.isRtl()) {
        data.reverse();
        startItemIndex = (data.length - 1)
    }

    return (
        <div className="brand-slider">
            <h3 className="title">{Identify.__('Shop By Brands')}</h3>
            <Scroller data={data} initItemIndex={startItemIndex} slideSettings={slideSettings} history={history} isPhone={isPhone}/>
        </div>
    );
}

export default Brands;