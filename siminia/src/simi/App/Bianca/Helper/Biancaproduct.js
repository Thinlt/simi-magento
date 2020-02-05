import Identify from '../../../Helper/Identify.js'

export const addRecentViewedProducts = (data) => {
    let recentViewed = getRecentViewedProducts()
    recentViewed = recentViewed?recentViewed:[]
    if (data.id) {
        const notexisted = recentViewed.every(function(item) {
            if (item.id === data.id) return false
            else return true
        })
        if (notexisted)
            recentViewed.push(data)
    }
    Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, 'BIANCA_RECENT_VIEW', recentViewed)
}
export const getRecentViewedProducts = () => {
    return Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'BIANCA_RECENT_VIEW');
}
