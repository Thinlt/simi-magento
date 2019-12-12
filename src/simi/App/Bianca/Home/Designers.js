import React from 'react'
import Identify from "src/simi/Helper/Identify";
import {cateUrlSuffix} from 'src/simi/Helper/Url';
import Scroller from "./Scroller";

const Designers = props => {
    const { history, isPhone} = props;
    const storeConfig = Identify.getStoreConfig() || {};
    const {simiStoreConfig} = storeConfig || {};
    const {config} = simiStoreConfig || {};
    const {vendor_list: data} = config || {};

    const slideSettings = {
        chevronWidth: isPhone ? 16 : 72,
        showChevron: true,
        numberOfCards: isPhone ? 3 : 6,
        slidesToScroll: 3,
        gutter: isPhone ? 12.5 : 16
    }

    let newData = [];
    if (data) {
        data.forEach((item, index)=>{
            if (index < 18 && item.logo) {
                item.url = `/shop-by-desinger.html?id=${item.vendor_id}`;
                item.image = item.logo;
                newData.push(item);
            }
            return false;
        });
    }

    const actionViewAll = () => {
        history.push('/designers' + cateUrlSuffix());
    }

    const lastItems = (
        <div className="last-items">
            <div className="btn" onClick={actionViewAll}><span>{Identify.__('View all')}</span></div>
        </div>
    );

    return (
        <div className={`brand-slider ${isPhone ? 'phone-view':''}`}>
            { data && <h3 className="title">{Identify.__('Shop By Designers')}</h3>}
            <Scroller data={newData} lastItems={lastItems} history={history} slideSettings={slideSettings} isPhone={isPhone}/>
        </div>
    );
}

export default Designers;