import React from 'react'
import Identify from "src/simi/Helper/Identify";
import Search from 'src/simi/BaseComponents/Icon/Search'
import defaultClasses from '../header.css'
import { mergeClasses } from 'src/classify'

class SearchForm extends React.Component{    
    handleSearch = () => {
        if (this.searchField && this.searchField.value) {
            this.props.history.push(`/search.html?query=${this.searchField.value}`) 
        }
    }

    render() {
        const classes = mergeClasses(defaultClasses, this.props.classes)
        return (
            <div className={classes['header-search-form']}>
                <label htmlFor="siminia-search-field" className="hidden">{Identify.__('Search')}</label>
                <input 
                    type="text" 
                    id="siminia-search-field"
                    ref={(e) => {this.searchField = e}}
                    placeholder={Identify.__('What are you looking for?')}
                    onKeyPress={(e) => {if (e.key === 'Enter') this.handleSearch()}}
                />
                <div role="button" tabIndex="0" className={classes['search-icon']} onClick={() => this.handleSearch()} onKeyUp={() => this.handleSearch()}>
                    <Search style={{width: 30, height: 30, display: 'block'}} />
                </div>
            </div>
        );
    }
}
export default SearchForm