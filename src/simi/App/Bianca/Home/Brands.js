import React from 'react'
import Identify from "src/simi/Helper/Identify";
import Scroller from "./Scroller";

const Brands = props => {
    const { history, isPhone, data} = props;

    const slideSettings = {
        showChevron: true,
        chevronWidth: isPhone ? 36 : 72,
        numberOfCards: isPhone ? 3 : 6,
        slidesToScroll: isPhone ? 3 : 6,
    }

    data.forEach((item, index)=>{
        item.url = `/shop-by-brand.html?option_id=${item.option_id}`;
    });

    return (
        <div className="brand-slider">
            <Scroller data={data} history={history} slideSettings={slideSettings} isPhone={isPhone}/>
        </div>
    );
}

export default Brands;