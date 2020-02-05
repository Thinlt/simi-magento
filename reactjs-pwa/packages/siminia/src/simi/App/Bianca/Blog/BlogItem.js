import React from "react";
import { Link } from 'src/drivers';
import Identify from 'src/simi/Helper/Identify'
import {getFormattedDate} from './BlogHelper'


require('./BlogItem.scss')

export const BlogItem = props => {
    const { item } = props;

    const img_link = item.featured_image_file;
    const image = item.featured_image_file && (
        <div className="benecos-article-image"  style={{backgroundImage: `url("${img_link}")`}}></div>
    );

    let url_page = item.url_key ? 'blog/' + item.url_key : '';
    if (!url_page) {
        url_page = 'post/' + item.id;
    }
    const locationDest = {
        pathname: "/" + url_page,
        state: {
            post_id: item.id,
            item_data: item,
        },
    };

    return (
        <div className="article-item" {...props}>
            {image}
            <div className="article-description">
                {item.publish_date && <div className="date">
                    {getFormattedDate(item.publish_date)}
                </div>}
                <Link to={locationDest} className="title">
                    {item.title}
                </Link>
            </div>
        </div>
    );
};
