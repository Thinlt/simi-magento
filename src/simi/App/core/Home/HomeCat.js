import React from 'react'
import HomeCatItem from './HomeCatItem';

const HomeCat = props => {
    const {classes, catData, history, isPhone} = props;

    const renderCat = () => {
        if(catData.home.homecategories && catData.home.homecategories.homecategories instanceof Array && catData.home.homecategories.homecategories.length > 0) {
            const dataCat = catData.home.homecategories.homecategories;
            const cate = dataCat.map((item, key) => {
                return (
                    <HomeCatItem isPhone={isPhone} item={item} history={history} key={key} classes={classes}/>
                )
            })

            return (
                <div className={classes['default-list-cate']}>
                    {cate}
                </div>
            )
            
        }
    }

    return (
        <div className={classes["default-category"]}>
            <div className={`container ${classes['home-container']}`}>
                {renderCat()}
            </div>
        </div>
    )
}

export default HomeCat;