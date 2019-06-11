import * as Constants from 'src/simi/Config/Constants';

class Identify {
    static SESSION_STOREAGE = 1;
    static LOCAL_STOREAGE = 2;

    static randomString(charCount = 20) {
        let text = "";
        const possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for (let i = 0; i < charCount; i++)
            text += possible.charAt(Math.floor(Math.random() * possible.length));
        return btoa(text + Date.now());
    }

    static __(text) {
        return text
    }

    /*
    URL param
    */
    static findGetParameter(parameterName) {
        var result = null,
            tmp = [];
        var items = location.search.substr(1).split("&");
        for (var index = 0; index < items.length; index++) {
            tmp = items[index].split("=");
            if (tmp[0] === parameterName) result = decodeURIComponent(tmp[1]);
        }
        return result;
    }

    /*
    Store config
    */
    static getStoreConfig() {
        return this.getDataFromStoreage(this.SESSION_STOREAGE, Constants.STORE_CONFIG);
    }

    /*
    Dashboard config handlers
    */
    static getAppDashboardConfigs() {
        let data = this.getDataFromStoreage(this.SESSION_STOREAGE, Constants.DASHBOARD_CONFIG);
        if (data === null) {
            data = window.DASHBOARD_CONFIG;
            if (data)
                this.storeDataToStoreage(this.SESSION_STOREAGE, Constants.DASHBOARD_CONFIG, data);
        }
        return data;
    }

    /* 
    store/get data from storage
    */
    static storeDataToStoreage(type, key, data) {
        if (typeof(Storage) !== "undefined") {
            if (!key)
                return;
            //process data
            const pathConfig = key.split('/');
            let rootConfig = key;
            if (pathConfig.length === 1) {
                rootConfig = pathConfig[0];
            }
            //save to storegae
            data = JSON.stringify(data);
            if (type === this.SESSION_STOREAGE) {
                sessionStorage.setItem(rootConfig, data);
                return;
            }

            if (type === this.LOCAL_STOREAGE) {
                localStorage.setItem(rootConfig, data);
                return;
            }
        }
        console.log('This Browser dont supported storeage');
    }
    static getDataFromStoreage(type, key) {
        if (typeof(Storage) !== "undefined") {
            let value = "";
            let data = '';
            if (type === this.SESSION_STOREAGE) {
                value = sessionStorage.getItem(key);
            }
            if (type === this.LOCAL_STOREAGE) {
                value = localStorage.getItem(key);
            }
            try {
                data = JSON.parse(value) || null;
            } catch (err) {
                data = value;
            }
            return data
        }
        console.log('This browser does not support local storage');
    }

    /*
    Version control
    */
    //version control 
    static detectPlatforms() {
        if (navigator.userAgent.match(/iPad|iPhone|iPod/)) {
            return 1;
        } else if (navigator.userAgent.match(/Android/)) {
            return 2;
        } else {
            return 3;
        }
    }

}

export default Identify;
