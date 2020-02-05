import React from 'react'
export const SearchContext = React.createContext({
    waiting: false, 
    searchTrigger: () => {}
})
export default SearchContext