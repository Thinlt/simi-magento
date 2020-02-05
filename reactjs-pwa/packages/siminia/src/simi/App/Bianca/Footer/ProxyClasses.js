//create proxy convert classes to normal className
const defaultClasses = {}
var proxyClasses = new Proxy(defaultClasses, {
    get: function ( proxyClasses, key ) {
        if (proxyClasses[key]) {
            return proxyClasses[key];
        }
        return key;
    }
});
export default proxyClasses