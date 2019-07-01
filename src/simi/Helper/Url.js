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


export const parse_query_string = (query) =>
{
    //query = query.substring(1);
    var vars = query.split("&");
    var query_string = {};

    for (var i = 0; i < vars.length; i++) {
        var pair = vars[i].split("=");
        // If first entry with this name
        if (typeof query_string[pair[0]] === "undefined") {
            query_string[pair[0]] = decodeURIComponent(pair[1]);
            // If second entry with this name
        } else if (typeof query_string[pair[0]] === "string") {
            var arr = [query_string[pair[0]], decodeURIComponent(pair[1])];
            query_string[pair[0]] = arr;
            // If third or later entry with this name
        } else {
            query_string[pair[0]].push(decodeURIComponent(pair[1]));
        }
    }
    return query_string;
}

export const convertToSlug = (Text) =>
{
    return Text
        .toLowerCase()
        .replace(/[^\w ]+/g,'')
        .replace(/ +/g,'-')
        ;
}

export const hashchange = (href,obj,open = 'open') =>
{
    if(obj.state[open]){
        window.onhashchange = function(e) {
            var oldURL = e.oldURL.split('#')[1];
            var newURL = e.newURL.split('#')[1];
            if (oldURL === href) {
                e.preventDefault();
                obj.setState({open: false});
            }
        }
    }
}