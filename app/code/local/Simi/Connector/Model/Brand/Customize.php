<?php
/**
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category
 * @package     Connector
 * @copyright   Copyright (c) 2012
 * @license
 */

/**
 * Connector Model
 *
 * @category
 * @package     Connector
 * @author      Developer
 */
class Simi_Connector_Model_Brand_Customize extends Simi_Connector_Model_Abstract
{
    public function getFeaturedBrands($data)
    {
        $featuredBrands = Mage::helper("shopbybrand")->getFeaturedBrands();
        $information = $this->statusSuccess();
        $store = Mage::app()->getStore()->getId();
        $list = array();
        $logo_width = isset($data->with) && $data->with != '' ? $data->with : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_width', $store);
        $logo_height = isset($data->height) && $data->height != '' ? $data->height : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_height', $store);
        $is_show_logo = Mage::getStoreConfig('shopbybrand/brand_list/display_brand_image', $store);

        foreach ($featuredBrands as $item) {
            if ($item->getStatus() == 1) {
                if($is_show_logo){
                    $path = 'brands/thumbnail' . DS . $item->getId();
                    $img = Mage::helper('shopbybrand/image')
                        ->init($item, $path)->resizeThumbnail($logo_width, $logo_height);
                    $list[] = array(
                        'brand_id' => $item->getBrandId(),
                        'url_key' => $item->getUrlKey(),
                        'option_id' => $item->getOptionId(),
                        'position_brand' => $item->getPositionBrand(),
                        'status' => $item->getStatus(),
                        'name' => $item->getName(),
                        'logo_image' => $img,
                    );
                }else{
                    $list[] = array(
                        'brand_id' => $item->getBrandId(),
                        'url_key' => $item->getUrlKey(),
                        'option_id' => $item->getOptionId(),
                        'position_brand' => $item->getPositionBrand(),
                        'status' => $item->getStatus(),
                        'name' => $item->getName(),
                    );
                }

            }
        }
        $information['data'] = $list;
        return $information;
    }

    public function getCategoryBrands()
    {
        $cats = Mage::helper('shopbybrand/brand')->getParentCategories();
        $categories = $cats['parent'];
        $information = $this->statusSuccess();
        $count = count($categories);
        $list = array();
        if ($count || count($cats['children'])) {
            foreach ($categories as $category) {
                $childs = array();
                if (isset($cats['children'][$category->getId()]))
                    $childs = $cats['children'][$category->getId()];
                if (count($childs) >= 1) {
                    foreach ($childs as $child) {
                        $list[] = array(
                            'category_id' => $category->getId(),
                            'category_name' => $category->getName(),
                            'chid' => array(
                                'category_id' => $child->getId(),
                                'category_name' => $child->getName(),
                            ),
                        );
                    }

                } else {
                    $list[] = array(
                        'category_id' => $category->getId(),
                        'category_name' => $category->getName(),
                    );
                }
            }
        }
        $information['data'] = $list;
        return $information;
    }

    public function getBrands($data)
    {
        $store = Mage::app()->getStore()->getId();
        $shopbybrands = Mage::getSingleton('shopbybrand/brand')->getBrandsData();
        $logo_width = isset($data->with) && $data->with != '' ? $data->with : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_width', $store);
        $logo_height = isset($data->height) && $data->height != '' ? $data->height : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_height', $store);
        $information = $this->statusSuccess();
        $list = array();
        $store = Mage::app()->getStore()->getId();
        $showNumberOfProducts = Mage::getStoreConfig('shopbybrand/brand_list/display_product_number', $store);
        $onlyBrandHaveProduct = Mage::getStoreConfig('shopbybrand/brand_list/display_brand_have_product', $store);
        $is_show_logo = Mage::getStoreConfig('shopbybrand/brand_list/display_brand_image', $store);

        $noImg = '';
        if ($shopbybrands[0]) {
            $path = 'brands/thumbnail' . DS . $shopbybrands[0]['brand_id'];
            $noImg = Mage::helper('shopbybrand/image')
                ->resizeThumbnail1('', $path, $logo_width, $logo_height);
        }

        foreach ($shopbybrands as $brand) {
            $path = 'brands/thumbnail' . DS . $brand['brand_id'];

            $img = ($brand['thumbnail_image'] == NULL) ? $noImg : Mage::helper('shopbybrand/image')
                ->resizeThumbnail1($brand['thumbnail_image'], $path, $logo_width, $logo_height);

            if ($onlyBrandHaveProduct && $brand['number_product'] > 0) {
                if ($showNumberOfProducts) {
                    $item = array(
                        'brand_id' => $brand['brand_id'],
                        'url_key' => $brand['url_key'],
                        'name' => $brand['name'],
                        'category_ids' => explode(",", $brand['category_ids']),
                        'number_product' => $brand['number_product'],
                    );
                } else {
                    $item = array(
                        'brand_id' => $brand['brand_id'],
                        'url_key' => $brand['url_key'],
                        'name' => $brand['name'],
                        'category_ids' => explode(",", $brand['category_ids']),
                    );
                }
                if($is_show_logo){
                    $item['logo_image'] = $img;
                }

                $list[] = $item;
            } elseif (!$onlyBrandHaveProduct) {
                if ($showNumberOfProducts) {
                    $item = array(
                        'brand_id' => $brand['brand_id'],
                        'url_key' => $brand['url_key'],
                        'name' => $brand['name'],
                        'category_ids' => explode(",", $brand['category_ids']),
                        'number_product' => $brand['number_product'],
                    );
                } else {
                    $item = array(
                        'brand_id' => $brand['brand_id'],
                        'url_key' => $brand['url_key'],
                        'name' => $brand['name'],
                        'category_ids' => explode(",", $brand['category_ids']),
                    );
                }
                if($is_show_logo){
                    $item['logo_image'] = $img;
                }

                $list[] = $item;
            }
        }
        $information['data'] = $list;
        return $information;
    }

    public function getSearchData()
    {
        $store = Mage::app()->getStore()->getId();
        $information = $this->statusSuccess();
        $brandData = unserialize(Mage::app()->getCacheInstance()->load('brand_search_data_simi_' . $store));
        if ($brandData) {
            $information['data'] = $brandData;
            return $information;
        }

        $shopbybrands = Mage::getSingleton('shopbybrand/brand')->getBrandCollection();
        $array = array();
        foreach ($shopbybrands as $brand) {
            $array[] = array('n' => $brand->getName(),
                'id' => $brand->getId(),
                'k' => $brand->getUrlKey());

        }
        Mage::app()->getCacheInstance()->save(serialize($array), 'brand_search_data_simi_' . $store);
        $information['data'] = $array;
        return $information;
    }

    public function setProductByBrand($brand_id, &$collection)
    {
        $model = Mage::getModel('shopbybrand/brand')->load($brand_id);
        $product_ids = $model->getProductIds();
        $collection->addFieldToFilter('entity_id', array('in' => $product_ids));
    }

    public function getListCharacterBegin()
    {
        $lists = array('0-9', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I',
            'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
            'S', 'T', 'W', 'U', 'V', 'X', 'Y', 'Z'
        );
        return $lists;
    }

    public function getBrandOfHome($data)
    {
        $information = $this->statusSuccess();
        $info = array();

        $store = Mage::app()->getStore()->getId();
        if (Mage::getStoreConfig('shopbybrand/brand_list/display_featured_brand', $store)) {
            $features = $this->getFeaturedBrands($data);
            $info['features'] = $features['data'];
        }

        if (Mage::getStoreConfig('shopbybrand/brand_list/display_brand_category', $store)) {
            $categories = $this->getCategoryBrands();
            $info['categories'] = $categories['data'];
        }

        if (Mage::getStoreConfig('shopbybrand/brand_list/display_brand_character_list', $store)) {
            $character = $this->getListCharacterBegin();
            $info['character'] = $character;
        }

        if (Mage::getStoreConfig('shopbybrand/brand_list/display_brand_search_box', $store)) {
            $search_data = $this->getSearchData();
            $info['search_box'] =$search_data['data'];
        }

        $brands = $this->getBrands($data);
        $info['brands'] = $brands['data'];

        $info['brand_style_by_name'] = Mage::getStoreConfig('shopbybrand/brand_list/display_brand_group_by_name', $store);

        $information['data'] = array($info);
        return $information;
    }

    public function getBrandOnSideBar($data){
        $shopbybrands = Mage::getSingleton('shopbybrand/brand')->getBrandsData();

        $store = Mage::app()->getStore()->getId();
        $display = Mage::getStoreConfig('shopbybrand/sidebar/brand_sidebar', $store);
        $max = Mage::getStoreConfig('shopbybrand/sidebar/maximum_item_sidebar', $store);

        $list = array();
        $logo_width = isset($data->with_brand) && $data->with_brand != '' ? $data->with_brand : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_width', $store);
        $logo_height = isset($data->height_brand) && $data->height_brand != '' ? $data->height_brand : Mage::getStoreConfig('shopbybrand/brand_list/brand_logo_height', $store);
        $is_show_logo = Mage::getStoreConfig('shopbybrand/brand_list/display_brand_image', $store);

        if(!$display) return $list;

        $count = 0;

        $noImg = '';
        if ($shopbybrands[0]) {
            $path = 'brands/thumbnail' . DS . $shopbybrands[0]['brand_id'];
            $noImg = Mage::helper('shopbybrand/image')
                ->resizeThumbnail1('', $path, $logo_width, $logo_height);
        }

        foreach ($shopbybrands as $brand) {
            if($count >= $max){
                break;
            }
            $count++;
            $path = 'brands/thumbnail' . DS . $brand['brand_id'];

            $img = ($brand['thumbnail_image'] == NULL) ? $noImg : Mage::helper('shopbybrand/image')
                ->resizeThumbnail1($brand['thumbnail_image'], $path, $logo_width, $logo_height);

            $item = array(
                'brand_id' => $brand['brand_id'],
                'url_key' => $brand['url_key'],
                'name' => $brand['name'],
                'category_ids' => explode(",", $brand['category_ids']),
                'number_product' => $brand['number_product'],
            );
            if($is_show_logo){
                $item['logo_image'] = $img;
            }

            $list[] = $item;
        }
        return $list;
    }
}