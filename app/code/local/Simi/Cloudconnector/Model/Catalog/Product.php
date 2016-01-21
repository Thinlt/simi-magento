<?php

/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Madapter Model
 *
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_Model_Catalog_Product extends Simi_Cloudconnector_Model_Abstract
{

    /**
     * Internal constructor
     */
    public function _construct()
    {
        parent::_construct();
    }

    /**
     * get api result
     *
     * @param   array
     * @return   json
     */
    public function run($data)
    {
        $productId = $data['products'];
        $params = array();
        if (isset($data['params']))
            $params = $data['params'];
        if ($productId) {
            $information = $this->getProductInfo($productId);
        } else {
            $offset = $data['offset'];
            $limit = $data['limit'];
            $update = $data['update'];
            $count = $data['count'];
            $information = $this->getListProduct($offset, $limit, $update, $count, $params);
        }
        return $information;
    }

    /**
     * get product collection
     *
     * @param   boolean
     * @return   object
     */
    public function getProductCollection($update)
    {
        $collection = Mage::getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect('*');
        $collection->addFinalPrice();
        $collection->setOrder('type_id', 'DESC');
        if ($update) {
            $collection->getSelect()->join(array('sync' => $collection->getTable('cloudconnector/sync')),
                'e.entity_id = sync.element_id', array('*'));
            $collection->getSelect()->where('sync.type = ' . self::TYPE_PRODUCT);
        }
        // $collection->addOptionsToResult();
        // // Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
        // // Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->addUrlRewrite(0);
        return $collection;
    }

    /**
     * get products list
     *
     * @param   int , int, boolean, boolean, array
     * @return   json
     */
    public function getListProduct($offset, $limit, $update, $count, $params)
    {
        $products = $this->getProductCollection($update);
        $backendModel = $products->getResource()->getAttribute('media_gallery')->getBackend();
        if ($count)
            return $products->getSize();
        if (!$offset)
            $offset = 0;
        if (!$limit)
            $limit = 10;
        $products->setPageSize($limit)->setCurPage($offset / $limit + 1)// ->load()
        ;
        if ($params)
            foreach ($params as $key => $value) {
                $products->addFieldToFilter($key, $value);
            }
        $products->addOptionsToResult();
        $productsList = array();
        foreach ($products as $product) {
            $productInfo = $this->getInfo($product, $backendModel);
            $productList[] = $productInfo;
            if ($update) {
                $this->removeUpdateRecord($product->getData('id'));
            }
        }
        return $productList;
    }

    /**
     * get product information
     *
     * @param   int
     * @return   json
     */
    public function getProductInfo($productId)
    {
        $product = Mage::getModel('catalog/product')->load($productId);
        $productInfo = $this->getInfo($product, false);
        return array($productInfo);
    }

    /**
     * get json information of a product
     *
     * @param   object
     * @return   json
     */
    public function getInfo($product, $backendModel)
    {
        $productInfo = array();
        $productInfo['id'] = $product->getId();
        $productInfo['sku'] = $product->getData('sku');
        $productInfo['name'] = $product->getData('name');
        $productInfo['type'] = $product->getData('type_id');
        $productInfo['qrcode'] = $product->getData('qrcode') ? $product->getData('qrcode') : '';
        $productInfo['price'] = Mage::helper('tax')->getPrice($product, $product->getData('price'), true);
        // $productInfo['price'] = $product->getData('price');
        if ($product->getData('final_price') && $product->getData('final_price') < $product->getData('price'))
            $productInfo['sale_price'] = Mage::helper('tax')->getPrice($product, $product->getData('final_price'), true);
        $productInfo['description'] = $product->getData('description');
        $productInfo['short_description'] = $product->getData('short_description');
        $productInfo['visibility'] = ($product->getData('visibility') == 1) ? 0 : 1;
        $productInfo['status'] = $product->getData('status');
        $productInfo['virtual'] = $product->getData('type_id') == 'virtual' ? 1 : 0;
        $productInfo['dimensions']['weight'] = $product->getData('weight') ? $product->getData('weight') : '';
        $productInfo['dimensions']['length'] = $product->getData('length') ? $product->getData('length') : '';
        $productInfo['dimensions']['width'] = $product->getData('width') ? $product->getData('width') : '';
        $productInfo['dimensions']['height'] = $product->getData('height') ? $product->getData('height') : '';
        $productInfo['created_at'] = $product->getData('created_at');
        $productInfo['updated_at'] = $product->getData('updated_at');
        $productInfo['tax_class_id'] = $product->getData('tax_class_id');
        $productInfo['categories'] = $this->getCategories($product);
        $productInfo['images'] = $this->getProductImage($product, $backendModel);
        $productInfo['manage_stock'] = (int)Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($product)->getQty();
        $productInfo['attribute_set'] = $product->getData('attribute_set_id');
        $productInfo['attributes'] = $this->getAttributes($product);
        if ($productInfo['type'] == 'configurable') {
            $productInfo['variants'] = $this->getProductVariants($product);
            $productInfo['manage_stock'] = 1;
        } else if ($productInfo['type'] == 'downloadable') {
            $productInfo['download'] = $this->getDownloadabledProducts($product);
        } else if ($productInfo['type'] == 'grouped') {
            $productInfo['group_items'] = $this->getGroupedItems($product);
        } else if ($productInfo['type'] == 'bundle') {
            $productInfo['sale_price_type'] = $product->getData('price_type');
            $productInfo['bundle_items'] = $this->getBundleItems($product);
        }
        if (count($product->getOptions())) {
            $productInfo['options'] = $this->getProductOptions($product);
        }
        return $productInfo;
    }

    /**
     * get product attributes
     *
     * @param   object
     * @return   json
     */
    public function getAttributes($product)
    {
        $atts = array();
        $mainAttributes = array('sku', 'name', 'type_id', 'entity_type_id', 'entity_id', 'price', 'final_price', 'attribute_set_id',
            'description', 'short_description', 'status', 'created_at',
            'updated_at', 'tax_class_id', 'attribute_set_id',
            'minimal_price', 'updated_at', 'thumbnail', 'small_image', 'visibility',
            'media_gallery', 'media', 'weight', 'length', 'width', 'height', 'image',
            'options_container', 'page_layout', 'custom_design'
        );
        $attributes = $product->getAttributes();

        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {  // only return attribute visible in font-end [kenshin]
                if ($attribute->getFrontend()->getValue($product)
                    && !in_array($attribute->getAttributeCode(), $mainAttributes)
                ) {
                    $attributeCode = $attribute->getAttributeCode();
                    $attributeId = $attribute->getId();
                    $value = '';
                    if ($attribute->getFrontend()->getValue($product))
                        $value = $attribute->getFrontend()->getValue($product);
                    $atts[$attributeId] = $value;
                }
            }
        }
        return $atts;
    }

    /**
     * get product options
     *
     * @param   object
     * @return   json
     */
    public function getProductOptions($product)
    {
        $options = $product->getOptions();
        $optionlist = array();
        if (count($options))
            foreach ($options as $option) {
                $optionArray = array(
                    'product_id' => $option->getData('product_id'),
                    'option_id' => $option->getData('option_id'),
                    'title' => $option->getData('default_title'),
                    'isRequired' => $option->getData('is_require'),
                    'type' => $option->getData('type'),
                    'values' => $this->getOptionValues($option),
                );
                $optionlist[] = $optionArray;
            }
        return $optionlist;
    }

    /**
     * get product option values
     *
     * @param   object
     * @return   json
     */
    public function getOptionValues($option)
    {
        $values = array();
        if ($option->getGroupByType() == Mage_Catalog_Model_Product_Option::OPTION_GROUP_SELECT) {
            $tmpValues = array();
            foreach ($option->getValues() as $value) {
                /* @var $value Mage_Catalog_Model_Product_Option_Value */
                $tmpValues['title'] = $value->getData('default_title');
                $tmpValues['value_id'] = $value->getData('option_type_id');
                $tmpValues['sku'] = $value->getData('sku');
                $tmpValues['price'] = $value->getData('default_price');
                $tmpValues['price_type'] = $value->getData('default_price_type');
                $values[] = $tmpValues;
            }
        } else {
            $tmpValues['title'] = $option->getData('default_title');
            $tmpValues['sku'] = $option->getData('sku');
            $tmpValues['value_id'] = $option->getData('option_type_id');
            $tmpValues['price'] = $option->getData('default_price');
            $tmpValues['price_type'] = $option->getData('default_price_type');
            $values[] = $tmpValues;
        }

        return $values;
    }

    /**
     * get product variants (configurable)
     *
     * @param   object
     * @return   json
     */
    public function getProductVariants($product)
    {
        $attributes = $product->getTypeInstance(true)->getConfigurableAttributes($product);
        $productIds = $product->getTypeInstance(true)->getUsedProductIds($product);
        $collection = $product->getCollection()
            ->addAttributeToSelect('*')
            ->addFieldToFilter('entity_id', array('in' => $productIds));
        $basePrice = $product->getFinalPrice();
        $attributeInfo = array();
        if ($collection->getSize()) {
            foreach ($collection as $productChild) {
                $attributeInfo[] = $this->productChildInfo($productChild, $attributes, $basePrice);
            }
        }
        return $attributeInfo;
    }

    public function productChildInfo($productChild, $attributes, $basePrice)
    {
        $attributeInfo = array();
        $attributeInfo['child_id'] = $productChild->getId();
        $attributeInfo['sku'] = $productChild->getSku();
        $attributeInfo['qty'] = (int)Mage::getModel('cataloginventory/stock_item')
            ->loadByProduct($productChild)->getQty();
        $priceAttribute = 0;
        foreach ($attributes as $attribute) {
            $productAttribute = $attribute->getProductAttribute();
            $attributeCode = $productAttribute->getAttributeCode();
            foreach ($attribute->getProductAttribute()->getSource()->getAllOptions() as $option) {
                if ($option['value'] != '' && $productChild->getData($attributeCode) == $option['value']) {
                    $attributeInfo[$attribute->getData('attribute_id')] = $option['label'];
                    // $attributeInfo['values'][] = array($option['value'] => $option['label']);
                }
            }
            $prices = $attribute->getPrices();
            foreach ($prices as $price) {
                if ($productChild->getData($attributeCode) == $price['value_index']) {
                    $priceAttribute += $this->getVariantPrice($price, $basePrice);
                }
            }
        }
        $attributeInfo['price'] = $priceAttribute + $basePrice;
        return $attributeInfo;
    }

    /**
     * get variant information
     *
     * @param   object
     * @return   json
     */
    public function getVariantPrice($value, $basePrice)
    {
        $price = 0;
        if ($value['is_percent']) {
            $price = (float)$value['pricing_value'] * $basePrice / 100;
        } else {
            $price = (float)$value['pricing_value'];
        }
        return $price;
    }

    /**
     * get product images list
     *
     * @param   object
     * @return   json
     */
    public function getProductImage($product, $backendModel)
    {
        if ($backendModel)
            $backendModel->afterLoad($product);
        $images = $product->getMediaGalleryImages();
        $imageList = array();
        if (count($images)) {
            $items = $images->getItems();
            foreach ($items as $key => $value) {
                $imageList[] = $this->getImageInfo($value);
            }
        }
        return $imageList;
    }

    /**
     * get product images information
     *
     * @param   object
     * @return   json
     */
    public function getImageInfo($value)
    {
        $imageInfo = array();
        $imageInfo['id'] = $value->getId();
        $imageInfo['url'] = $value->getData('url');
        $imageInfo['name'] = $value->getData('file');
        $imageInfo['position'] = $value->getData('position');
        return $imageInfo;
    }

    /**
     * get product category id list
     *
     * @param   object
     * @return   json
     */
    public function getCategories($product)
    {
        return $product->getCategoryIds();
    }

    /**
     * get product download
     *
     * @param   object
     * @return   json
     */
    public function getDownloadabledProducts($product)
    {
        $links = Mage::getModel('downloadable/link')
            ->getCollection()
            ->addTitleToResult()
            ->addPriceToResult()
            ->addFieldToFilter('product_id', array('eq' => $product->getId()));
        $urls = array();
        foreach ($links as $link) {
            $urls[] = $this->getDownloadInfo($link);
        }
        return $urls;
    }

    /**
     * get download information
     *
     * @param   object
     * @return   json
     */
    public function getDownloadInfo($link)
    {
        $downloadInfo = array();
        $downloadInfo['id'] = $link->getData('link_id');
        $downloadInfo['title'] = $link->getData('title');
        $downloadInfo['price'] = $link->getData('price');
        $downloadInfo['file'] = $link->getData('link_type');
        // $downloadInfo['max_download'] = 0;
        // $downloadInfo['max_download_unlimited'] = 0;
        $downloadInfo['shareable'] = $link->getData('is_shareable');
        $downloadInfo['file_download'] = $this->getFileDownload($link);
        $downloadInfo['link_download'] = $link->getData('link_url');
        $downloadInfo['sort_order'] = $link->getData('sort_order');
        $downloadInfo['count_download'] = $link->getData('number_of_downloads');
        return $downloadInfo;
    }

    /**
     * get file download name url
     *
     * @param   object
     * @return   json
     */
    public function getFileDownload($link)
    {
        $fileInfo = array();
        if ($link->getData('link_type') == 'file') {
            $fileInfo['file_name'] = $link->getData('link_file');
            $fileInfo['url'] = Mage::getUrl('cloudconnector/download/links',
                array('id' => $link->getId(), '_secure' => true)
            );
        }
        return $fileInfo;
    }

    /**
     * get grouped product items
     *
     * @param   object
     * @return   json
     */
    public function getGroupedItems($product)
    {
        $productItems = $product->getTypeInstance(true)->getAssociatedProducts($product);
        $productItemInfo = array();
        $position = 0;
        foreach ($productItems as $item) {
            $productItemInfo[] = $this->getGroupedItemInfo($item, $position);
            $position++;
        }
        return $productItemInfo;
    }

    /**
     * get grouped item information
     *
     * @param   object
     * @return   json
     */
    public function getGroupedItemInfo($item, $position)
    {
        $itemInfo = array();
        $itemInfo['product_id'] = $item->getId();
        $itemInfo['name'] = $item->getData('name');
        $itemInfo['sku'] = $item->getData('sku');
        $itemInfo['price'] = $item->getData('price');
        $itemInfo['default_qty'] = $item->getData('qty');
        $itemInfo['position'] = $position;
        return $itemInfo;
    }

    /**
     * get bundled item list
     *
     * @param   object
     * @return   json
     */
    public function getBundleItems($product)
    {
        $typeInstance = $product->getTypeInstance(true);
        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($product), $product
        );
        $productItems = $typeInstance->getOptionsCollection($product);
        $_options = $productItems->appendSelections($selectionCollection, false,
            Mage::helper('catalog/product')->getSkipSaleableCheck()
        );
        $productItemInfo = array();
        foreach ($_options as $item) {
            $productItemInfo[] = $this->getBundleItemInfo($item);
        }
        return $productItemInfo;
    }

    /**
     * get bundle item information
     *
     * @param   object
     * @return   json
     */
    public function getBundleItemInfo($item)
    {
        $itemInfo = array();
        $itemInfo['id'] = $item->getId();
        $itemInfo['product_id'] = $item->getData('parent_id');
        $itemInfo['option_id'] = $item->getData('option_id');
        $itemInfo['required'] = $item->getData('required');
        $itemInfo['position'] = $item->getData('position');
        $itemInfo['type'] = $item->getData('type');
        $itemInfo['title'] = $item->getData('default_title');
        $itemInfo['values'] = $this->getChildProducts($item->getSelections());
        return $itemInfo;
    }

    /**
     * get bundle child product list
     *
     * @param   object
     * @return   json
     */
    public function getChildProducts($selections)
    {
        $productInfo = array();
        foreach ($selections as $product) {
            $productInfo[] = $this->getChildProductInfo($product);
        }
        return $productInfo;
    }

    /**
     * get bundle child product information
     *
     * @param   object
     * @return   json
     */
    public function getChildProductInfo($product)
    {
        $childInfo = array();
        $childInfo['id'] = $product->getId();
        $childInfo['name'] = $product->getData('name');
        $childInfo['position'] = $product->getData('position');
        $childInfo['sku'] = $product->getData('sku');
        $childInfo['default'] = $product->getData('is_default');
        $childInfo['qty'] = $product->getData('selection_qty');
        $childInfo['user_defined'] = $product->getData('selection_can_change_qty');
        $childInfo['selection_id'] = $product->getData('selection_id');
        if ($product->getData('selection_price_value'))
            $childInfo['ex_price'] = $product->getData('selection_price_value');
        return $childInfo;
    }

    /**
     * pull data from cloud
     *
     * @param   array
     * @return
     */
    public function pull($data)
    {
        return $this->saveProduct($data);
    }

    /**
     * create customer group
     *
     * @param   json
     * @return   json
     */
    public function saveProduct($data)
    {
        if (isset($data['id'])) {
            $product = Mage::getModel('catalog/product')->load($data['id']);
            if (!$product)
                return ['errors' => 'not exits'];
        } else {
            $product = Mage::getModel('catalog/product');
        }
        Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
        $product
            ->setTypeId($data['type'])
            ->setAttributeSetId($data['attribute_set'])
            ->setSku($data['sku'])
            ->setWebsiteIDs(array(1));

        //product visible in the catalog
        $product
            ->setCategoryIds($data['category'])
            ->setStatus($data['status'])
            ->setVisibility($data['visibility']);

        //Configuring stock:
        $product->setStockData(array(
            'use_config_manage_stock' => 0, // use global config?
            'manage_stock' => $data['manage_stock'],
            'is_in_stock' => 1, // in stock
            'qty' => $data['qty'],
        ));

        //attributes,price
        $product
            ->setName($data['name'])
            ->setShortDescription($data['short_description'])
            ->setDescription($data['description'])
            // set up prices
            ->setPrice($data['price'])
            ->setTaxClassId($data['tax_class_id'])// Taxable Goods by default
            ->setWeight($data['weight']);

        if ($data['sale_price'] != 0)
            $product->setSpecialPrice($data['sale_price']);

        // addition information such as color, size, brand

        if (!empty($data['attribute'])) {
            foreach ($data['attribute'] as $code => $value) {
                $optionId = $this->getOptionId($code, $value);
                $product->setData($code, $optionId);
            }
        }

        // custom option
        if (isset($data['custom_options']) && !empty($data['custom_options'])) {
            //  if (isset($data['id'])) {
            //   // delete old option
            //  $customOptions = $product->getOptions();
            //   foreach ($customOptions as $option) {
            //          $option->delete();
            //   }
            // }
            $product
                ->setCanSaveCustomOptions(true)
                ->setProductOptions($data['custom_options'])
                ->setHasOptions(true);
        }


        // images
//        $images = array(
//            'thumbnail'   => 'mtk004t.jpg',
//            'small_image' => 'http://www.stepblogging.com/wp-content/uploads/2015/05/magento-products.png',
//            'image'       => 'http://www.stepblogging.com/wp-content/uploads/2015/05/magento-products.png',
//        );
//
//        $dir = Mage::getBaseDir('media') . DS . 'example/amasty/';
//
//        foreach ($images as $imageType => $imageFileName) {
//            $path = $dir . $imageFileName;
//            if (file_exists($path)) {
//                try {
//                    $product->addImageToMediaGallery($path, $imageType, false);
//                } catch (Exception $e) {
//                    echo $e->getMessage();
//                }
//            } else {
//                echo "Can not find image by path: `{$path}`<br/>";
//            }
//        }
        if ($data['type'] == 'bundle') {
            $product = $this->setBundlesProduct($product, $data['bundle']);
        }

        $product->save();
        if ($data['type'] == 'grouped')
            $this->setGroupProduct($data['group'], $product->getId());
        return ['product_id' => $product->getId()];
    }

    /**
     * get id product attribute by code and values
     * @param $attribute_code
     * @param $label
     * @return string
     */
    function getOptionId($attribute_code, $label)
    {
        $attribute_model = Mage::getModel('eav/entity_attribute');
        $attribute_options_model = Mage::getModel('eav/entity_attribute_source_table');
        $attribute_code = $attribute_model->getIdByCode('catalog_product', $attribute_code);
        $attribute = $attribute_model->load($attribute_code);
        $attribute_table = $attribute_options_model->setAttribute($attribute);
        $options = $attribute_options_model->getAllOptions(false);
        $optionId = '';
        foreach ($options as $option) {
            if ($option['label'] == $label) {
                $optionId = $option['value'];
                break;
            }
        }
        return $optionId;
    }

    public function setGroupProduct($data, $product_id)
    {
        if (!empty($data)) {
            $products_links = Mage::getModel('catalog/product_link_api');
            foreach ($data as $group_id) {
                $products_links->assign("grouped", $product_id, $group_id);
            }
        }
    }

    public function setBundlesProduct($product, $bundle)
    {
        //flags for saving custom options/selections
        $product->setCanSaveCustomOptions(true);
        $product->setCanSaveBundleSelections(true);
        $product->setAffectBundleProductSelections(true);
        //registering a product because of Mage_Bundle_Model_Selection::_beforeSave
        Mage::register('product', $product);
        //setting the bundle options and selection data
        $product->setBundleOptionsData($bundle['bundleOptions']);
        $product->setBundleSelectionsData($bundle['bundleSelections']);
        return $product;
    }

    public function setConfigableProduct($product, $data)
    {
        $product->getTypeInstance()->setUsedProductAttributeIds(array(92)); //attribute ID of attribute 'color' in my store
        $configurableAttributesData = $product->getTypeInstance()->getConfigurableAttributesAsArray();
        $product->setCanSaveConfigurableAttributes(true);
        $product->setConfigurableAttributesData($configurableAttributesData);
        $configurableProductsData = array();
        $configurableProductsData['920'] = array( //['920'] = id of a simple product associated with this configurable
            '0' => array(
                'label' => 'Green', //attribute label
                'attribute_id' => '92', //attribute ID of attribute 'color' in my store
                'value_index' => '24', //value of 'Green' index of the attribute 'color'
                'is_percent' => '0', //fixed/percent price for this option
                'pricing_value' => '21' //value for the pricing
            )
        );
        $product->setConfigurableProductsData($configurableProductsData);
        return $product;
    }

    public function getAttribute($attributeId, $name)
    {
        $storeId = 0;
        $attribute = Mage::getModel('catalog/product')
            ->setStoreId($storeId)
            ->getResource()
            ->getAttribute($attributeId);
        if ($attribute->getData('frontend_input') == 'multiselect' || $attribute->getData('frontend_input') == 'select')
            foreach ($attribute->getSource()->getAllOptions() as $optionId => $optionValue) {
                if (is_array($optionValue)) {
                    if ($optionValue['label'] && $optionValue['label'] == $name)
                        $options[] = $optionValue['label'];
                } else {
                    if ($optionValue && $optionValue['label'] == $name)
                        $options[] = $optionValue;
                }
            }

        return array();
    }
}

