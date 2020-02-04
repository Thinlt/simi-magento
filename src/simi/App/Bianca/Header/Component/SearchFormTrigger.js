import React, { useEffect } from 'react'
import ProxyClasses from './ProxyClasses'
const $ = window.$
const SearchFormTrigger = props => {
    const classes = props.classes || ProxyClasses
    const trigger = props.searchTrigger || (() => { })
    useEffect(() => {
        window.addEventListener('click', function (e) {
            if (!document.getElementById('searchFormHeader').contains(e.target) && !document.getElementById('header-search-form').contains(e.target)) {
                // click outside
                if (!$('.header-search').hasClass('waiting')) {
                    // hide search form
                    trigger()
                }
            }
        });
    })
    return (
        <div id="searchFormHeader" onClick={trigger} className={classes['search-trigger']}>
            <i className="icon-magnifier icons"></i>
        </div>
    );
}
export default SearchFormTrigger