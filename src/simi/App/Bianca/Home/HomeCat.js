import React from 'react'
import HomeCatItem from './HomeCatItem';
import Identify from "src/simi/Helper/Identify";

const HomeCat = props => {
    const { catData, history, isPhone} = props;
    let {homecategories: categories} = catData && catData.home && catData.home.homecategories || {};
    const renderCat = () => {
        if(categories instanceof Array && categories.length > 0) {
            return categories.map((item, key) => {
                return (
                    <HomeCatItem isPhone={isPhone} item={item} history={history} key={key}/>
                );
            });
        }
        return null;
    }
    return (
        <div className={`default-category ${Identify.isRtl() ? 'default-category-rtl' : ''}`}>
            <div className="container home-container">
                {renderCat()}
            </div>
        </div>
    )
}

export default HomeCat;