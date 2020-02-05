import React, { useEffect, useState } from 'react';
import classify from 'src/classify';
import { connect } from 'src/drivers';
import { compose } from 'redux';
import PageTitle from 'src/simi/App/core/Customer/Account/Components/PageTitle';
import { toggleMessages } from 'src/simi/Redux/actions/simiactions';
import SizeChart from './SizeChart';
import defaultClasses from './style.scss';
import {getSizeChart} from 'src/simi/Model/Customer';
import Loading from "src/simi/BaseComponents/Loading";

const MySizeChart = props => {
    const [sizeData, setSizeData] = useState('')
    const { id } = props.data; 
    useEffect(()=>{
        if(id){
            getSizeChart(sizeChartCallback, id);
        }
    },[id])

    const sizeChartCallback = (data) => {
        const {totalRecords , items} = data;
        setSizeData({totalRecords, items});
    }

    return (
        <div className='my-size-chart-area'>
            <PageTitle title={'Size Chart History'.toUpperCase()}/>
            {sizeData
            ?
                <SizeChart data={sizeData} isPhone={props.isPhone}/>
            : <Loading/>}
        </div>
    )
    
}

const mapDispatchToProps = {
    toggleMessages,
};

export default compose(
    classify(defaultClasses),
    connect(
        null,
        mapDispatchToProps
    )
)(MySizeChart);
