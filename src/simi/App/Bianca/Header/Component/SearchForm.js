import React, {useState, useEffect, useCallback} from 'react'
import Identify from "src/simi/Helper/Identify";
import SearchAutoComplete from './searchAutoComplete/index'

const SearchForm = props => {
    let searchField = null
    const [showAC, setShowAC] = useState(false)
    const [searchVal, setSearchVal] = useState('')
    const [waiting, setWaiting] = useState(true)

    const toggleSearch = () => {
        setWaiting(!waiting)
    }

    const callback = useCallback(toggleSearch);

    useEffect(() => {
        if (typeof(props.toggleSearch) === 'function') {
            props.toggleSearch(callback)
        }
    });

    const startSearch = () => {
        if (searchVal) {
            props.history.push(`/search.html?query=${searchVal}`) 
        }
    }
    const handleSearchField = () => {
        if (searchField.value) {
            setShowAC(true)
            if (searchField.value !== searchVal)
                setSearchVal(searchField.value) 
        } else {
            setShowAC(false)
        }
    }


    const classes = props.classes

    
    let isWaiting = false;
    if (props.waiting) {
        if (waiting) {
            isWaiting = true;
        }
    }
    
    let waitingClass = '';
    if (isWaiting) {
        waitingClass = 'waiting';
    }
    
    const renderHtml = () => {
        let formClass = ''
        if (!props.outerComponent) {
            formClass = ' '+waitingClass
        }
        return (
            <div className={classes['header-search-form']+formClass}>
                { !isWaiting &&
                    <>
                        <label htmlFor="siminia-search-field" className="hidden">{Identify.__('Search')}</label>
                        <i className="icon-magnifier icons"></i>
                        <input 
                            type="text" 
                            id="siminia-search-field"
                            ref={(e) => {searchField = e}}
                            placeholder={Identify.__('Search')}
                            onChange={() => handleSearchField()}
                            onKeyPress={(e) => {if (e.key === 'Enter') startSearch()}}
                        />
                        {/* <div role="button" tabIndex="0" className={classes['search-icon']} onClick={() => startSearch()} onKeyUp={() => startSearch()}>
                            <Search style={{width: 30, height: 30, display: 'block'}} />
                        </div> */}
                        <SearchAutoComplete visible={showAC} setVisible={setShowAC} value={searchVal} />
                    </>
                }
            </div>
        );
    }

    if (props.outerComponent) {
        const OuterComponent = props.outerComponent
        return (
            <OuterComponent className={'header-search '+waitingClass}>
                {renderHtml()}
            </OuterComponent>
        )
    }
    
    return renderHtml();
}
export default SearchForm