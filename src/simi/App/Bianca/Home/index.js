import React, { useEffect, useState } from 'react'
import { getHomeData } from 'src/simi/Model/Home';
import Banner from './Banner';
import HomeCat from "./HomeCat";
import Brands from "./Brands";
import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner'
import { withRouter } from 'react-router-dom';
import ProductList from './ProductList';
import Identify from 'src/simi/Helper/Identify';
// import * as Constants from 'src/simi/Config/Constants';
import { getOS } from 'src/simi/App/Bianca/Helper';
import Designers from './Designers';
import Newcollections from './Newcollections';
import Instagram from './Instagram';
// import Chats from 'src/simi/App/Bianca/BaseComponents/Chats';
require('./home.scss');

if (getOS() === 'MacOS') require('./home-ios.scss');

const Home = props => {
    const {history} = props;
    const [isPhone, setIsPhone] = useState(window.innerWidth < 1024)
    // const simiSessId = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID)
    const cached_home = Identify.ApiDataStorage(`home_lite`)
    const storeConfig = Identify.getStoreConfig() || {};
    const config = storeConfig.simiStoreConfig && storeConfig.simiStoreConfig.config || {};
    const {brands, instagram} = config || [];
    const [data, setHomeData] = useState(cached_home);

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
            Identify.ApiDataStorage(`home_lite`,'update', data);
            setHomeData(data);
        }
    }

    if(!data) {
        return <LoadingSpiner />
    } 

    return (
        <div className={`home-wrapper ${getOS()}`}>
            <div className={`banner-wrap ${isPhone ? 'mobile':''}`}>
                <Banner data={data} history={history} isPhone={isPhone} />
            </div>
            {
                brands && 
                <div className={`shop-by-brand-wrap ${isPhone ? 'mobile':''}`}>
                    <Brands data={brands} history={history} isPhone={isPhone}/>
                </div>
            }
            <div className={`featured-products-wrap ${isPhone ? 'mobile':''}`}>
                <ProductList homeData={data} history={history}/>
            </div>
            <div className={`popular-categories-wrap ${isPhone ? 'mobile':''}`}>
                <h3 className="title">{Identify.__('Popular Categories')}</h3>
                <HomeCat catData={data} history={history} isPhone={isPhone}/>
            </div>
            <div className={`new-collections-wrap ${isPhone ? 'mobile':''}`}>
                <Newcollections data={data} history={history} isPhone={isPhone}/>
            </div>
            <div className={`shop-by-designers-wrap ${isPhone ? 'mobile':''}`}>
                <Designers history={history} isPhone={isPhone}/>
            </div>
            {
                instagram && instagram.enabled === '1' && instagram.userid &&
                <div className={`shop-our-instagram-wrap ${isPhone ? 'mobile':''}`}>
                    <Instagram data={instagram.userid} history={history} isPhone={isPhone}/>
                </div>
            }
            {/* <div className={`home-chats ${isPhone ? 'mobile':''}`}>
                <Chats data={instant_contact} history={history} isPhone={isPhone}/>
            </div> */}
        </div>
    );
}

export default withRouter(Home);