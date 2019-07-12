import React from 'react'
import {Carousel} from "react-responsive-carousel";
import 'react-responsive-carousel/lib/styles/carousel.min.css';
import Identify from "src/simi/Helper/Identify";
import { Colorbtn } from "src/simi/BaseComponents/Button";

const Banner = props => {
    const {classes, history} = props;
    const data = props.data.home.homebanners;
    const bannerCount = data.length;
    const configs = Identify.getStoreConfig();
    const isShowHomeTitle = configs.simiStoreConfig.config.base.is_show_home_title === '1';
    const slideSettings = {
        autoPlay: true,
        showArrows: false,
        showThumbs: false,
        showIndicators: (bannerCount && bannerCount !== 1),
        showStatus: false,
        infiniteLoop: true,
        rtl: parseInt(configs.simiStoreConfig.config.base.is_rtl, 10) === 1,
        lazyLoad: true,
        dynamicHeight : true,
        transitionTime : 500
    }
    let title = null;

    const renderItem = (item, title, key) => {
        let w = '100%';
        let h = '100%'
        return (
            <div 
                style={{position: 'relative', maxWidth: w, minHeight: h}} 
                className={classes['banner-item']} 
                id={`banner-item-${key}`}
            >
                {title}
                <img className="img-responsive" width={w} height={h} src={item.banner_name} alt={item.banner_title}/>
            </div>
        )
    }

    const renderBannerTitle = item => {
        let action = () => {}
        if (parseInt(item.type, 10) === 1) {
            //open product
            let location = {
                pathname: `/product.html/${item.product_id}`,
                state: {'product_id': item.product_id}
            };
            action = () => history.push(location);
        }else if(item.type === "2"){
            let location = {
                pathname: `/products?cat=${item.category_id}`,
                state: {category_page_id: item.category_id}
            }
            action = () => history.push(location) ;
        }else{
            action = (e) => {
                e.preventDefault();
                window.open(item.banner_url);
            }
        }

        return(
            <div className={classes["banner-title"]} onClick={action}>
                <div className={classes["bannner-content"]}>
                    <div className={classes["title"]}>{item.banner_title}</div>
                </div>
                {/* <Colorbtn text={"show"}
                            className={`${classes["banner-action"]}`}/> */}
            </div>
        )
    }

    const bannerData = data.homebanners.map((item, index) => {
        if (isShowHomeTitle && isShowHomeTitle !== 0 && isShowHomeTitle !== '0') {
            title = renderBannerTitle(item)
        }
        title = renderBannerTitle(item)
        if (!item.banner_name) return '';
        return (
            <div
                key={index}
                style={{cursor: 'pointer'}}
            >
                {renderItem(item, title, index)}
            </div>
        );
    })

    return (
        <div className={classes["banner-homepage"]}>
            <div className="container">
                <Carousel {...slideSettings} onChange={(id)=>this.handleShowTitle(id)}>
                    {bannerData}
                </Carousel>
            </div>
        </div>
    ) ;
}

export default Banner;  