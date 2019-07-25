import React from 'react'
import ArrowRight from 'src/simi/BaseComponents/Icon/TapitaIcons/ArrowLeft';

const cateUrlSuffix = '.html';

const HomeCatItem = props => {
    const {item, classes, history} = props;

    const action = () => {
        if (item.url_path)
            history.push(item.url_path + cateUrlSuffix);
    }

    return (
        <div role="presentation" className={classes['home-cate-item']} onClick={() => action()}>
            <div className={classes["cate-img"]}>
                <img src={window.innerWidth < 1024 ?  item.simicategory_filename : item.simicategory_filename_tablet}
                     alt={item.simicategory_name}/>
            </div>
            <div className={classes["cate-title"]}>
                <div className={"--text"}>{item.simicategory_name}</div>
            </div>
            <div className={classes["cate-arrow"]}>
                <ArrowRight color="#fff" style={{width:60,height:60}}/>
            </div>
        </div>
    )
}

export default HomeCatItem