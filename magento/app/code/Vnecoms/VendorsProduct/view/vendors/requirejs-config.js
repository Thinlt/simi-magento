/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            categoryForm:       'Vnecoms_VendorsProduct/catalog/category/form',
            newCategoryDialog:  'Vnecoms_VendorsProduct/js/new-category-dialog',
            categoryTree:       'Vnecoms_VendorsProduct/js/category-tree',
            productGallery:     'Vnecoms_VendorsProduct/js/product-gallery',
            baseImage:          'Vnecoms_VendorsProduct/catalog/base-image-uploader',
            newVideoDialog:     'Vnecoms_VendorsProduct/js/video/new-video-dialog',
            openVideoModal:     'Vnecoms_VendorsProduct/js/video/video-modal',
            productAttributes:  'Vnecoms_VendorsProduct/catalog/product-attributes',
            menu:               'mage/backend/menu'
        }
    },
    "shim": {
        "productGallery": ["jquery/fix_prototype_bootstrap"],
        "Vnecoms_VendorsProduct/catalog/apply-to-type-switcher": ["Vnecoms_VendorsProduct/catalog/type-events"]
    },
    "paths": {
        "Magento_Catalog/catalog/type-events": "Vnecoms_VendorsProduct/catalog/type-events",
        "Magento_Catalog/catalog/apply-to-type-switcher": "Vnecoms_VendorsProduct/catalog/apply-to-type-switcher",
        "Magento_Catalog/js/product/weight-handler":"Vnecoms_VendorsProduct/js/product/weight-handler",
        "Magento_Catalog/js/product-gallery":"Vnecoms_VendorsProduct/js/product-gallery",
        "Magento_ProductVideo/js/get-video-information":"Vnecoms_VendorsProduct/js/video/get-video-information",
        "Magento_Catalog/js/tier-price/percentage-processor":"Vnecoms_VendorsProduct/js/tier-price/percentage-processor",
        "Magento_Catalog/js/tier-price/value-type-select":"Vnecoms_VendorsProduct/js/tier-price/value-type-select",
        "Magento_Catalog/js/utils/percentage-price-calculator":"Vnecoms_VendorsProduct/js/utils/percentage-price-calculator",
        "Magento_Catalog/js/components/custom-options-component":"Vnecoms_VendorsProduct/js/components/custom-options-component",
        "Magento_Catalog/js/components/custom-options-price-type":"Vnecoms_VendorsProduct/js/components/custom-options-price-type",
        "Magento_Catalog/js/components/dynamic-rows-import-custom-options":"Vnecoms_VendorsProduct/js/components/dynamic-rows-import-custom-options"
    }
};
