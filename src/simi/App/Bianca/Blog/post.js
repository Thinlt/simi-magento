import React from "react";
import Loading from 'src/simi/BaseComponents/Loading'
import Identify from 'src/simi/Helper/Identify'
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb'
import ReactHTMLParser from 'react-html-parser';
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import {getArticle} from 'src/simi/Model/Blog';
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getFormattedDate} from './BlogHelper'
require('./style.scss')

class BlogPost extends React.Component {
    constructor(props) {
        super(props);
        this.BlogModel = new BlogModel({ obj: this });
    }

    componentWillMount() {
        if (this.props.post_id) {
            this.id = this.props.post_id;
        } else {
            this.id = this.props.match.params.id;
        }
        getArticle((data)=>this.processData(data), this.id);
    }

    processData(data) {
        this.setState({ data });
    }

    render() {
        const { data } = this.state;

        if (!data) {
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
                        <Whitebtn text={Identify.__("Back")} className="back-to-blog" onClick={() => this.handleLink(`/blog`)} />
                        <div className="post-main">
                            <div className="post-date">{getFormattedDate(data.publish_date)}</div>
                            <h2 className="title">{data.title}</h2>
                            {data.featured_image_file && <div className="post-featured-img">
                                <img src={data.featured_image_file} alt='featuredimage'/>
                            </div>}
                            {data.content && <div className="post-content">{ReactHTMLParser(data.content)}</div>}
                            {data.testimonial && <div className="post-testimonial">
                                <div className="user-test">{data.testimonial}</div>
                                {data.testimonial_author && <div className="author-test">{data.testimonial_author}</div>}
                            </div>}
                        </div>
                        <Whitebtn text={Identify.__("Back")} className="back-to-blog back-bottom" onClick={() => this.handleLink(`/blog`)} />

                    </div>
                </div>
            </React.Fragment>
        )
    }
}

export default BlogPost;