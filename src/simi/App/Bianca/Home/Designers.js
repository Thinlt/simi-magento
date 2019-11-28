import React from 'react'
import Identify from "src/simi/Helper/Identify";
import Scroller from "./Scroller";

const Designers = props => {
    const { history, isPhone} = props;
    const storeConfig = Identify.getStoreConfig() || {};
    const {simiStoreConfig} = storeConfig || {};
    const {config} = simiStoreConfig || {};
    const {vendor_list: data} = config || {};

    const slideSettings = {
        chevronWidth: isPhone ? 48 : 72,
        showChevron: true,
        numberOfCards: isPhone ? 3 : 6,
        slidesToScroll: isPhone ? 3 : 6,
    }

    let newData = [];
    if (data) {
        data.forEach((item, index)=>{
            if (index < 4) {
                item.url = `/shop-by-desinger.html?id=${item.vendor_id}`;
                item.image = item.logo;
                newData.push(item);
            }
            return false;
        });
    }

    return (
        <div className={`brand-slider ${isPhone ? 'phone-view':''}`}>
            { data && <h3 className="title">{Identify.__('Shop By Designers')}</h3>}
            <Scroller data={newData} history={history} slideSettings={slideSettings} isPhone={isPhone}/>
        </div>
    );
}

export default Designers;