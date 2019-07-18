import React from 'react'
import {Colorbtn} from 'src/simi/BaseComponents/Button'
const prdUrlSuffix = '.html';
const cateUrlSuffix = '.html';

const BannerItem = props => {
    const {classes, history, item} = props;

    const renderBannerTitle = item => {
        let action = () => {}
        if (parseInt(item.type, 10) === 1) {
            //product detail
            if (item.url_key) {
                action = () => history.push(item.url_key + prdUrlSuffix);
            }
        } else if(parseInt(item.type, 10) === 2){
            //category
            if (item.url_path) {
                action = () => history.push(item.url_path + cateUrlSuffix);
            }
        } else {
            action = (e) => {
                e.preventDefault();
                window.open(item.banner_url);
            }
        }

        return(
            <div role="presentation" className={classes["banner-title"]} onClick={action}>
                <div className={classes["bannner-content"]}>
                    <div className={classes["title"]}>{item.banner_title}</div>
                </div>
                <Colorbtn 
                    text={"Show"}
                    className={`${classes["banner-action"]}`}/>
            </div>
        )
    }
    
    const w = '100%';
    const h = '100%';
    return (
        <div 
            style={{position: 'relative', maxWidth: w, minHeight: h}} 
            className={classes['banner-item']}
        >
            {renderBannerTitle(item)}
            <img className="img-responsive" width={w} height={h} src={window.innerWidth < 1024 ? item.banner_name : item.banner_name_tablet} alt={item.banner_title}/>
        </div>
    )
}

export default BannerItem;