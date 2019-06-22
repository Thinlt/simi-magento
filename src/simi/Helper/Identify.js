import * as Constants from 'src/simi/Config/Constants';

class Identify {
    static SESSION_STOREAGE = 1;
    static LOCAL_STOREAGE = 2;
    /*
    connecter
    */
    static hasConnector() {
        return (window.SMCONFIGS && window.SMCONFIGS.has_connector)
    }

    /*
    String
    */

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

    static isRtl() {
        let is_rtl = false;
        const configs = this.getStoreConfig();
        if (configs !== null && configs.storeview && configs.storeview.base && configs.storeview.base.is_rtl) {
            is_rtl = parseInt(configs.storeview.base.is_rtl, 10) === 1;
        }
        return is_rtl;
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
    static saveStoreConfig(data) {
        if (data.simiStoreConfig && data.simiStoreConfig.config_json && (typeof data.simiStoreConfig.config_json) === 'string') {
            const simi_config = JSON.parse(data.simiStoreConfig.config_json)
            if (simi_config && simi_config.storeview) {
                data.simiStoreConfig.config = simi_config.storeview
                if (simi_config.storeview && simi_config.storeview.base && simi_config.storeview.base.customer_identity)
                    this.storeDataToStoreage(Identify.LOCAL_STOREAGE, Constants.SIMI_SESS_ID, simi_config.storeview.base.customer_identity)
            }
        }
        this.storeDataToStoreage(Identify.SESSION_STOREAGE, Constants.STORE_CONFIG, data)
    }
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
    App Settings
    */
    static getAppSettings() {
        return this.getDataFromStoreage(this.LOCAL_STOREAGE, Constants.APP_SETTINGS);
    }

    static storeAppSettings(data) {
        return this.storeDataToStoreage(this.LOCAL_STOREAGE, Constants.APP_SETTINGS, data)
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
