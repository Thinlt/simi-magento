import React from 'react'
import Identify from "src/simi/Helper/Identify";
import Scroller from "./Scroller";

const Designers = props => {
    const { history, isPhone} = props;
    const storeConfig = Identify.getStoreConfig() || [];
    const {simiStoreConfig: {config: {brands: data}}} = storeConfig;

    const slideSettings = {
        chevronWidth: 72,
        showChevron: !isPhone
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

export default Designers;