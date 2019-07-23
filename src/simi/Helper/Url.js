import makeOptimizedUrl from 'src/util/makeUrl'

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