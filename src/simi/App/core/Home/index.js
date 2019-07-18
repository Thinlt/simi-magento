import React, { useEffect, useState } from 'react'
import { getHomeData } from 'src/simi/Model/Home';
import Banner from './Banner';
import HomeCat from "./HomeCat";
import defaultClasses from './style.css';
import classify from 'src/classify';
import LoadingSpiner from 'src/simi/BaseComponents/Loading/LoadingSpiner'
import { withRouter } from 'react-router-dom';
import { compose } from 'redux';
import ProductList from './ProductList';
import Identify from 'src/simi/Helper/Identify';
import * as Constants from 'src/simi/Config/Constants';

const Home = props => {
    const { classes, history } = props;
    const simiSessId = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID)
    const cached_home = simiSessId?Identify.ApiDataStorage(`home_lite_${simiSessId}`):null

    const [data, setHomeData] = useState(cached_home)
    useEffect(() => {
        if(!data) {
            getHomeData(setData);
        }
    },[])

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
        <React.Fragment>
            <Banner data={data} classes={classes} history={history}/>
            <HomeCat catData={data} classes={classes} history={history}/>
            <ProductList homeData={data} classes={classes} history={history}/>
        </React.Fragment>

    );
}

export default compose(
    classify(defaultClasses),
    withRouter
)(Home);