import makeOptimizedUrl from 'src/util/makeUrl'

export const resourceUrl = (path, { type, width } = {}) => {
    const urlBuffer = window.SMCONFIGS.media_url_prefix?window.SMCONFIGS.media_url_prefix:''
    let result = makeOptimizedUrl(path, {type, width})
    if (urlBuffer) {
        if (result.indexOf('media%2Fcatalog%2Fproduct') !== -1) {
            result = result.replace('media%2Fcatalog%2Fproduct', urlBuffer + 'media%2Fcatalog%2Fproduct')
        } else if (result.indexOf('media%2Fcatalog%2Fcategory') !== -1) {
            result = result.replace('media%2Fcatalog%2Fcategory', urlBuffer + 'media%2Fcatalog%2Fcategory')
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