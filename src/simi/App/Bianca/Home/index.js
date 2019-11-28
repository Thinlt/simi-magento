import React, { useEffect, useState } from 'react'
import { getHomeData } from 'src/simi/Model/Home';
import Banner from './Banner';
import HomeCat from "./HomeCat";
import Brands from "./Brands";
import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner'
import { withRouter } from 'react-router-dom';
import ProductList from './ProductList';
import Identify from 'src/simi/Helper/Identify';
import * as Constants from 'src/simi/Config/Constants';
import { getOS } from 'src/simi/App/Bianca/Helper';
import Designers from './Designers';
import Newcollections from './Newcollections';
import Instagram from './Instagram';
require('./home.scss');

if (getOS() === 'MacOS') require('./home-ios.scss');

const Home = props => {
    const {history} = props;
    const [isPhone, setIsPhone] = useState(window.innerWidth < 1024)
    const simiSessId = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID)
    const cached_home = simiSessId?Identify.ApiDataStorage(`home_lite_${simiSessId}`):null
    const storeConfig = Identify.getStoreConfig() || [];
    const {simiStoreConfig: {config: {brands: brands}}} = storeConfig;

    const [data, setHomeData] = useState(cached_home)

    const resizePhone = () => {
        window.onresize = function () {
            const width = window.innerWidth;
            const newIsPhone = width < 1024
            if(isPhone !== newIsPhone){
                setIsPhone(newIsPhone)
            }
        }
    }
    useEffect(() => {
        if(!data) {
            getHomeData(setData);
        }
        resizePhone();
    },[data, isPhone])

    const setData = (data) => {
        if(!data.errors) {
            if (simiSessId)
                Identify.ApiDataStorage(`home_lite_${simiSessId}`,'update', data)
            setHomeData(data)
        }
    }

    if(!data) {
        return <LoadingSpiner />
    } 

    return (
        <div className="home-wrapper">
            <div className="banner-wrap">
                <Banner data={data} history={history} isPhone={isPhone} />
            </div>
            {
                brands && 
                <div className="shop-by-brand-wrap">
                    <h3 className="title">{Identify.__('Shop By Brands')}</h3>
                    <Brands data={brands} history={history} isPhone={isPhone}/>
                </div>
            }
            <div className="featured-products-wrap">
                <ProductList homeData={data} history={history}/>
            </div>
            <div className="popular-categories-wrap">
                <h3 className="title">{Identify.__('Popular Categories')}</h3>
                <HomeCat catData={data} history={history} isPhone={isPhone}/>
            </div>
            <div className="new-collections-wrap">
                <Newcollections data={data} history={history} isPhone={isPhone}/>
            </div>
            <div className="shop-by-designers-wrap">
                <Designers history={history} isPhone={isPhone}/>
            </div>
            <div className="shop-our-instagram-wrap">
                <h3 className="title">{Identify.__('Shop Our Instagram')}</h3>
                <Instagram data={'simicart.official'} />
            </div>
        </div>
    );
}

export default withRouter(Home);