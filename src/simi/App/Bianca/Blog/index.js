import React from "react";
import Identify from 'src/simi/Helper/Identify'
import BreadCrumb from 'src/simi/BaseComponents/BreadCrumb';
import { BlogItem } from './BlogItem';
import ReactHTMLParser from 'react-html-parser';
import { Link } from 'src/drivers';
import {getArticles} from 'src/simi/Model/Blog';
import Loading from 'src/simi/BaseComponents/Loading'
import { Whitebtn } from 'src/simi/BaseComponents/Button';
import LoadingMore from 'src/simi/BaseComponents/Loading/ReactLoading'
import TitleHelper from 'src/simi/Helper/TitleHelper';
import {getFormattedDate} from './BlogHelper'
require('./style.scss')
const $ = window.$;
class Blog extends React.Component {

    constructor(props) {
        super(props);
        this.total = 0;
        this.limit = 12;
        this.offset = 0;
        //loadingMore: 0-no, 1-loading, 2-loaded
        this.loadingMore = 0

        this.state = {
            isPhone: window.innerWidth <= 1024
        }
    }

    handleLink = (link) => {
        const {history} = this.props
        history.push(link)
    }

    componentDidMount() {
        const obj = this;
        $(window).resize(function () {
            const width = window.innerWidth;
            const isPhone = width <= 1024;
            if(obj.state.isPhone !== isPhone){
                obj.setState({isPhone})
            }
        })
        document.addEventListener('scroll', this.trackScrolling);
        this.loadingMore = 0
        getArticles((data)=>this.processData(data), {limit: this.limit, offset: this.offset});
    }

    componentWillUnmount() {
        document.removeEventListener('scroll', this.trackScrolling);
    }

    isBottom(el) {
        return el.getBoundingClientRect().top <= window.innerHeight;
    }

    trackScrolling = () => {
        const wrappedElement = document.getElementById('blog-items-load-more')
        if (wrappedElement && this.isBottom(wrappedElement)) {
            this.loadMoreBlog()
        }
    };

    processData(data) {
        this.total = data.total;
        const oldData = this.state.data;

        if (this.loadingMore === 1 && oldData && oldData.articles) {
            data.articles = oldData.articles.concat(data.articles);
            this.loadingMore = 2
        }
        this.setState({ data });
    }

    renderSpecialPost = (data) => {
        if (data && data.url_key) {
            let url_page = data.url_key ? 'blog/' + data.url_key : '';
            if (!url_page) {
                url_page = 'post/' + data.id;
            }
            this.locationDest = {
                pathname: "/" + url_page,
                state: {
                    post_id: data.id,
                    post_data: data,
                }
            };

            const image = data.featured_image_file && (
                <div className="special-image-container"><div className="benecos-article-image" style={{backgroundImage: `url("${data.featured_image_file}")`}}></div></div>
            );

            return <div className="article-item item-featured-post">
                <div className="article-description">
                    {data.publish_date && <div className="date">
                        {getFormattedDate(data.publish_date)}
                    </div>}
                    <div className="title">
                        <Link to={this.locationDest}>
                            {Identify.__(data.title)}
                        </Link>
                    </div>
                    {data.short_content && <div className="description">
                        {ReactHTMLParser(data.short_content)}
                    </div>}
                    <Whitebtn text={Identify.__("Read more")} onClick={() => this.handleLink(this.locationDest)} className={'btn-news-readmore'} />
                </div>
                {image}
            </div>
        }
        return '';
    }

    renderContent = (data) => {
        let html = null;
        if (data.length) {
            html = data.map((item, index) => {
                if (index === 0) {
                    return <div key={index} className="special-post col-md-12">{this.renderSpecialPost(item)}</div>
                }
                return <div className="col-xs-12 col-sm-6 col-md-4" key={index}><BlogItem item={item} /></div>
            });
        }
        return html;
    }

    loadMoreBlog = () => {
        if (this.loadingMore === 1) {
            //loading, skip
            return
        }
        const newOffset = parseInt(this.offset, 10) + parseInt(this.limit, 10);

        if (newOffset < this.total) {
            this.offset = newOffset
            getArticles((data)=>this.processData(data), {limit: this.limit, offset: this.offset});
            this.loadingMore = 1;
        }
    }

    renderList = (posts) => {
        let html = null;
        if (posts instanceof Array && posts.length) {
            html = posts.map(post => {
                let url_page = post.url_key ? 'blog/' + post.url_key : '';
                if (!url_page) {
                    url_page = 'post/' + post.id;
                }
                const locationDest = {
                    pathname: "/" + url_page,
                    state: {
                        post_id: post.id,
                        item_data: post,
                    },
                };

                return <Link key={post.url_key} to={locationDest} className="latest-item" key={post.id}>{post.title}</Link>
            })
        }
        return html;
    }

    render() {
        this.storeConfig = Identify.getStoreConfig();
        if (!this.storeConfig || !this.storeConfig.storeConfig || !this.storeConfig.storeConfig.id) {
            return ''
        }
        const { data, isPhone } = this.state;
        if (!data || !data.hasOwnProperty('articles')) {
            return <Loading />
        }

        const config = data.config || null;

        const breadcrumb = [
            {
                name: Identify.__("Home"),
                link: "/"
            },
            {
                name: Identify.__('Blog')
            }
        ];

        const articles = []
        const storeId = this.storeConfig.storeConfig.id.toString()
        data.articles.map(post => {
            if (post.store_ids && post.store_ids.length && post.store_ids.includes(storeId)) {
                articles.push(post)
            }
        })

        return (
            <React.Fragment>
                {config && TitleHelper.renderMetaHeader({
                    title: config.meta_title || null,
                    desc: config.meta_description || null
                })}
                <div className="blog-app-page">
                    <BreadCrumb breadcrumb={breadcrumb} />
                    {data.hasOwnProperty('articles') && data.articles.length < 1 && !data.special_post ? <div className="text-center">{Identify.__("Empty news post")}</div> :
                        <div className="container">
                            <div className="row">
                                <div className="blog-left-col col-md-3">
                                    {isPhone ? '' : <React.Fragment>
                                        <div className="latest-title">{Identify.__("Latest Posts")}</div>
                                        {this.renderList(articles)}
                                    </React.Fragment>}
                                </div>
                                <div className="blog-main-col col-md-9">
                                    <div className="blog-article-content">
                                        <div className="blog-grid row">
                                            {this.renderContent(articles)}
                                        </div>
                                        {((parseInt(this.limit, 10) + parseInt(this.offset, 10)) < parseInt(this.total, 10)) ? <div id="blog-items-load-more"><LoadingMore divStyle={{marginTop: 5}} /></div> : ''}
                                    </div>
                                </div>
                            </div>
                        </div>}
                </div>
            </React.Fragment>
        );
    }
}

export default Blog;
