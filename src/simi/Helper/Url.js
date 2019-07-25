import makeOptimizedUrl from 'src/util/makeUrl'
import Identify from './Identify'

export const resourceUrl = (path, { type, width } = {}) => {
    const urlBuffer = window.SMCONFIGS.media_url_prefix?window.SMCONFIGS.media_url_prefix:''
    let result = makeOptimizedUrl(path, {type, width})
    //fix error when path is not full url, when the result does not directory ./pub
    if ((path.indexOf('http://') === -1) && (path.indexOf('https://') === -1)) { //url does not have protocol
        if (urlBuffer) {
            if (result.indexOf('media%2Fcatalog%2Fproduct') !== -1) {
                result = result.replace('media%2Fcatalog%2Fproduct', urlBuffer + 'media%2Fcatalog%2Fproduct')
            } else if (result.indexOf('media%2Fcatalog%2Fcategory') !== -1) {
                result = result.replace('media%2Fcatalog%2Fcategory', urlBuffer + 'media%2Fcatalog%2Fcategory')
            }
        }
    } else { //url has protocol
        if (path.indexOf('place_holder')) {
            return path
        }
    }
    return result
}

export const convertToSlug = (Text) => {
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

/*
Logo Url
*/
export const logoUrl = () => {
    return window.SMCONFIGS.logo_url ?
        window.SMCONFIGS.logo_url :
    'https://www.simicart.com/skin/frontend/default/simicart2.1/images/simicart/new_logo_small.png'
}

/*
Url suffix
*/
export const cateUrlSuffix = () => {
    const savedSuffix = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'CATEGORY_URL_SUFFIX')
    if (savedSuffix)
        return savedSuffix
    try {
        const storeConfig = Identify.getStoreConfig()
        const suffix = storeConfig.simiStoreConfig.config.catalog.seo.category_url_suffix
        Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'CATEGORY_URL_SUFFIX', suffix);
        return suffix
    } catch (err) {

    }
    return '.html'
}

export const productUrlSuffix = () => {
    const savedSuffix = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'PRODUCT_URL_SUFFIX')
    if (savedSuffix)
        return savedSuffix
    try {
        const storeConfig = Identify.getStoreConfig()
        const suffix = storeConfig.simiStoreConfig.config.catalog.seo.product_url_suffix
        Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'PRODUCT_URL_SUFFIX', suffix);
        return suffix
    } catch (err) {

    }
    return '.html'
}