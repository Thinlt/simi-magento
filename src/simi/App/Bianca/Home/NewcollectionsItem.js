import React from 'react'
import {cateUrlSuffix} from 'src/simi/Helper/Url';
import Identify from "src/simi/Helper/Identify";
import {smoothScrollToView} from 'src/simi/Helper/Behavior';

const NewcollectionsItem = props => {
    const {item, history, isPhone} = props;

    const action = (url) => {
        if (url) history.push(url + cateUrlSuffix());
        smoothScrollToView($('#siminia-main-page'));
    }

    return (
        <div className={`item ${isPhone ? 'phone-view':''}`} role="presentation">
            {item.newcollections_name ? 
                <h3 className="title">{Identify.__(item.newcollections_name)}</h3>
                :
                <h3 className="title">{Identify.__('New Collections')}</h3>
            }
            <div className="collections-images">
                <div className="img-row-1">
                    <div className="img-1" onClick={() => action(item.url_path_0)}>
                        {item.newcollections_filename_0 && <img src={item.newcollections_filename_0} alt={item.cat_name_0}/>}
                    </div>
                </div>
                <div className="img-row-2">
                    <div className="img-col-1">
                        <div className="img-2" onClick={() => action(item.url_path_1)}>
                            {item.newcollections_filename_1 && <img src={item.newcollections_filename_1} alt={item.cat_name_1}/>}
                        </div>
                        <div className="img-3" onClick={() => action(item.url_path_2)}>
                            {item.newcollections_filename_2 && <img src={item.newcollections_filename_2} alt={item.cat_name_2}/>}
                        </div>
                    </div>
                    <div className="img-col-2">
                        <div className="img-4" onClick={() => action(item.url_path_3)}>
                            {item.newcollections_filename_3 && <img src={item.newcollections_filename_3} alt={item.cat_name_3}/>}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}

export default NewcollectionsItem