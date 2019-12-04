import makeOptimizedUrl from 'src/util/makeUrl';
import Identify from 'src/simi/Helper/Identify';

export const resourceUrl = (path, { type, width } = {}) => {
	const urlBuffer = window.SMCONFIGS.media_url_prefix ? window.SMCONFIGS.media_url_prefix : '';
	let result = makeOptimizedUrl(path, { type, width });
	//fix error when path is not full url, when the result does not directory ./pub
	if (path.indexOf('http://') === -1 && path.indexOf('https://') === -1) {
		//url does not have protocol
		if (urlBuffer) {
			if (result.indexOf('media%2Fcatalog%2Fproduct') !== -1) {
				result = result.replace('media%2Fcatalog%2Fproduct', urlBuffer + 'media%2Fcatalog%2Fproduct');
			} else if (result.indexOf('media%2Fcatalog%2Fcategory') !== -1) {
				result = result.replace('media%2Fcatalog%2Fcategory', urlBuffer + 'media%2Fcatalog%2Fcategory');
			}
		}
	} else {
		//url has protocol
		if (path.indexOf('place_holder')) {
			return path;
		}
	}
	return result;
};

export const convertToSlug = (Text) => {
	return Text.toLowerCase().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
};

/*
Logo Url
*/
export const logoUrl = () => {
	// const dbConfig = Identify.getAppDashboardConfigs()
	const { storeConfig } = Identify.getStoreConfig() || { storeConfig: null };
	let logoUrl = '';
	if (storeConfig && storeConfig.header_logo_src) {
		if (isSecureContext) {
			logoUrl = storeConfig.secure_base_media_url + 'logo/' + storeConfig.header_logo_src;
		} else {
			logoUrl = storeConfig.base_media_url + 'logo/' + storeConfig.header_logo_src;
		}
		return logoUrl;
	}
	return '/images/logo.png';
};
export const logoAlt = () => {
	const { storeConfig } = Identify.getStoreConfig() || { storeConfig: null };
	let logoAlt = '';
	if (storeConfig && storeConfig.logoAlt) {
		logoAlt = storeConfig.logo_alt;
		return logoAlt;
	}
	return ''; // default logo static file
};

/*
Footer Logo Url
*/
export const footerLogoUrl = () => {
	const { simiStoreConfig } = Identify.getStoreConfig() || { simiStoreConfig: null };
	const { storeConfig } = Identify.getStoreConfig() || { storeConfig: null };
    var footerLogoUrl = ''
    var relativePath = ''
    if (simiStoreConfig && 
        simiStoreConfig.config &&
        simiStoreConfig.config.base &&
        simiStoreConfig.config.base.footer_logo
        ) {
            relativePath = simiStoreConfig.config.base.footer_logo
            if (storeConfig) {
                if(isSecureContext){
                    footerLogoUrl = storeConfig.secure_base_media_url + 'footer_logo/' + relativePath
                }else{
                    footerLogoUrl = storeConfig.base_media_url + 'footer_logo/' + relativePath
                }
            }
            return footerLogoUrl
    }
	
    return '/images/logo_footer.png'
};

export const footerLogoAlt = () => {
    const { simiStoreConfig } = Identify.getStoreConfig() || { simiStoreConfig: null };
    var footerLogoAlt = ''
    if (simiStoreConfig && 
        simiStoreConfig.config &&
        simiStoreConfig.config.base &&
        simiStoreConfig.config.base.footer_logo_alt
        ) {
            footerLogoAlt = simiStoreConfig.config.base.footer_logo_alt
            return footerLogoAlt        
    }
    return ''
};

/*
Url suffix
*/
export const cateUrlSuffix = () => {
	const savedSuffix = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'CATEGORY_URL_SUFFIX');
	if (savedSuffix) return savedSuffix;
	try {
		const storeConfig = Identify.getStoreConfig();
		const suffix = storeConfig.simiStoreConfig.config.catalog.seo.category_url_suffix;
		Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'CATEGORY_URL_SUFFIX', suffix);
		return suffix;
	} catch (err) {}
	return '.html';
};

export const productUrlSuffix = () => {
	const savedSuffix = Identify.getDataFromStoreage(Identify.LOCAL_STOREAGE, 'PRODUCT_URL_SUFFIX');
	if (savedSuffix) return savedSuffix;
	try {
		const storeConfig = Identify.getStoreConfig();
		const suffix = storeConfig.simiStoreConfig.config.catalog.seo.product_url_suffix;
		Identify.storeDataToStoreage(Identify.LOCAL_STOREAGE, 'PRODUCT_URL_SUFFIX', suffix);
		return suffix;
	} catch (err) {}
	return '.html';
};

/*
Local url dictionary
*/

export const getDataFromUrl = (url) => {
	let localUrlDict = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'LOCAL_URL_DICT');
	localUrlDict = localUrlDict ? localUrlDict : {};
	return localUrlDict[url];
};

export const saveDataToUrl = (url, data, is_dummy_data = true) => {
	let localUrlDict = Identify.getDataFromStoreage(Identify.SESSION_STOREAGE, 'LOCAL_URL_DICT');
	localUrlDict = localUrlDict ? localUrlDict : {};
	if (!localUrlDict[url] || !is_dummy_data) {
		data.is_dummy_data = is_dummy_data;
		localUrlDict[url] = data;
		Identify.storeDataToStoreage(Identify.SESSION_STOREAGE, 'LOCAL_URL_DICT', localUrlDict);
	}
};

export const saveCategoriesToDict = (category) => {
	if (category) {
		if (category.children && Array.isArray(category.children) && category.children.length) {
			category.children.forEach((childCat) => {
				saveCategoriesToDict(childCat);
			});
		}
		if (category.url_path)
			saveDataToUrl('/' + category.url_path + cateUrlSuffix(), { id: category.id, name: category.name });
	}
};

export const isSecure = window.isSecureContext;
