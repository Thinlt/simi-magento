import React from 'react'
import ArrowRight from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowLeft';
import { convertToSlug } from 'src/simi/Helper/Url';

const HomeCat = props => {
    const {classes, data, history} = props;

    const renderCat = () => {
        if(data.home.homecategories && data.home.homecategories.homecategories instanceof Array && data.home.homecategories.homecategories.length > 0) {
            const dataCat = data.home.homecategories.homecategories;
            let cate = dataCat.map((item, key) => {
                let location = {
                    pathname: '/' + convertToSlug(item.simicategory_name) + '.html',
                    state: {
                        category_page_id: item.category_id
                    }
                }
                return (
                    <div className={classes["home-cate-item"]} key={key} onClick={() => history.push(location)}>
                        <div className={classes["cate-img"]}>
                            <img src={item.simicategory_filename}
                                 alt={item.simicategory_name}/>
                        </div>
                        <div className={classes["cate-title"]}>
                            <div className={classes["--text"]}>{item.simicategory_name}</div>
                        </div>
                        <div className={classes["cate-arrow"]}>
                            <ArrowRight color="#fff" style={{width:60,height:60}}/>
                        </div>
                    </div>
                )
            })

            return (
                <div className={classes["default-list-cate"]}>
                    {cate}
                </div>
            )
            
        }
    }

    return (
        <div className={classes["default-category"]}>
            <div className="container">
                {renderCat()}
            </div>
        </div>
    )
}

export default HomeCat;