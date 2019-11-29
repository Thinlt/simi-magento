import React from 'react'
import NewcollectionsItem from './NewcollectionsItem';
import Identify from "src/simi/Helper/Identify";

const Newcollections = props => {
    const { data, history, isPhone} = props;
    let {homenewcollections: newcollections} = data && data.home && data.home.homenewcollections || {};

    const render = () => {
        if(newcollections instanceof Array && newcollections.length > 0) {
            return newcollections.map((item, key) => {
                return (
                    <NewcollectionsItem item={item} isPhone={isPhone} history={history} key={key}/>
                );
            });
        }
        return null;
    }
    return (
        <div className={`newcollections ${Identify.isRtl() ? 'newcollections-rtl' : ''}`}>
            <div className="container">
                <div className="collections-wrap">
                {render()}
                </div>
            </div>
        </div>
    )
}

export default Newcollections;