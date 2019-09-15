import React from 'react'
import ProxyClasses from './ProxyClasses'
const SearchFormTrigger = props => {
    const classes = props.classes || ProxyClasses
    const trigger = props.searchTrigger || (() => {})
    return (
        <div onClick={trigger} className={classes['search-trigger']}>
            <i className="icon-magnifier icons"></i>
        </div>
    );
}
export default SearchFormTrigger