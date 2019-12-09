import React from 'react'
import {cateUrlSuffix} from 'src/simi/Helper/Url';
import Identify from "src/simi/Helper/Identify";
import {smoothScrollToView} from 'src/simi/Helper/Behavior';

const HomeCatItem = props => {
    const {item, history, isPhone} = props;
    const isFull = item.is_show_name !== '1';

    const action = (e) => {
        if (item.url_path) history.push(item.url_path + cateUrlSuffix());
        smoothScrollToView($('#siminia-main-page'));
    }

    if(!item.simicategory_filename && !item.simicategory_filename_tablet) {
        return null;
    }

    let img = '';

    if(isPhone) {
        if(item.simicategory_filename_tablet) {
            img = item.simicategory_filename_tablet;
        } else {
            img = item.simicategory_filename;
        }
    } else {
        if(item.simicategory_filename) {
            img = item.simicategory_filename;
        } else {
            img = item.simicategory_filename_tablet;
        }
    }

    return (
        <div className={`home-cate-item ${isFull ? 'full-width':''}`} role="presentation">
            <div className="cate-title">
                <span className="--text">{Identify.normalizeName(item.simicategory_name)}</span>
            </div>
            <div className="cate-img" onClick={action}>
                <img src={img} alt={item.simicategory_name}/>
            </div>
            <div className="cate-btn">
                <div className="btn" onClick={action}><span>{Identify.__('View all')}</span></div>
            </div>
        </div>
    );
}

export default HomeCatItem