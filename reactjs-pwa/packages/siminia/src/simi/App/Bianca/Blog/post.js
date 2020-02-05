import React, {useState} from "react";
import Loading from 'src/simi/BaseComponents/Loading'
import Identify from 'src/simi/Helper/Identify'
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb'
import ReactHTMLParser from 'react-html-parser';
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import {getArticle} from 'src/simi/Model/Blog';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getFormattedDate} from './BlogHelper'
require('./style.scss')

const BlogPost = props => {
    const [data, setData] = useState(null)
    const {post_id, history} = props

    if (!data) {
        getArticle((data)=>setData(data), post_id);
        return <Loading />
    }

    const breadcrumb = [
        {
            name: Identify.__("Home"),
            link: "/"
        },
        {
            name: Identify.__("Blog"),
            link: "/blog"
        },
        {
            name: data.title,
        }
    ];

    return (
        <React.Fragment>
            {data && TitleHelper.renderMetaHeader({
                title: data.title || null,
                desc: data.meta_description || null
            })}
            <div className="blog-single-app-page">
                <BreadCrumb breadcrumb={breadcrumb} />
                <div className="container">
                    <div className="post-main">
                        <div className="post-date">{getFormattedDate(data.publish_date)}</div>
                        <h2 className="title">{data.title}</h2>
                        {data.featured_image_file && <div className="post-featured-img">
                            <img src={data.featured_image_file} alt='featuredimage'/>
                        </div>}
                        {data.content && <div className="post-content">{ReactHTMLParser(data.content)}</div>}
                    </div>
                    <Whitebtn text={Identify.__("Back")} className="back-to-blog back-bottom" onClick={() => history.push(`/blog`)} />
                </div>
            </div>
        </React.Fragment>
    )
}

export default BlogPost;