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

const Home = props => {
    const { classes, history } = props;
    const [data, setHomeData] = useState(null)
    useEffect(() => {
        if(!data) {
            getHomeData(setData);
        }
    },[])

    const setData = (data) => {
        if(!data.errors) {
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