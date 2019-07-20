import React from 'react'
import {Carousel} from "react-responsive-carousel";
import 'react-responsive-carousel/lib/styles/carousel.min.css';
import Identify from "src/simi/Helper/Identify";
import BannerItem from "./BannerItem";

const Banner = props => {
    const {classes, history} = props;
    const data = props.data.home.homebanners;
    const bannerCount = data.length;
    const configs = Identify.getStoreConfig();

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

    const bannerData = data.homebanners.map((item, index) => {
        if (!item.banner_name) return '';
        return (
            <div
                key={index}
                style={{cursor: 'pointer'}}
            >
                <BannerItem item={item} classes={classes} history={history}/>
            </div>
        );
    })

    return (
        <div className={classes["banner-homepage"]}>
            <div className="container">
                <Carousel {...slideSettings}>
                    {bannerData}
                </Carousel>
            </div>
        </div>
    ) ;
}

export default Banner;  