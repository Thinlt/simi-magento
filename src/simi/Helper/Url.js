class Url{
    static parse_query_string(query)
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

    static convertToSlug(Text)
    {
        return Text
            .toLowerCase()
            .replace(/[^\w ]+/g,'')
            .replace(/ +/g,'-')
            ;
    }

    static hashchange(href,obj,open = 'open'){
        if(obj.state[open]){
            window.onhashchange = function(e) {
                var oldURL = e.oldURL.split('#')[1];
                var newURL = e.newURL.split('#')[1];
                console.log('old:'+oldURL+' new:'+newURL);
                if (oldURL === href) {
                    e.preventDefault();
                    obj.setState({open: false});
                }
            }
        }
    }
}

export default Url;
