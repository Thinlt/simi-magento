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
class Simi_Connector_Model_Catalog_Product extends Simi_Connector_Model_Catalog {

    protected $_data;

    protected function getHelper() {
        return Mage::helper('connector');
    }
    
    protected function _getSession() {
        return Mage::getSingleton('customer/session');
    }

    protected function getProduct($id) {
        return $this->getModel('catalog/product')->load($id);
    }

    public function getImageProduct($product, $file = null, $width, $height) {
        if (!is_null($width) && !is_null($height)) {
            if ($file) {
                return Mage::helper('catalog/image')->init($product, 'thumbnail', $file)->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize($width, $height)->__toString();
            }
            return Mage::helper('catalog/image')->init($product, 'small_image')->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize($width, $height)->__toString();
        }
        if ($file) {
            return Mage::helper('catalog/image')->init($product, 'thumbnail', $file)->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(600, 600)->__toString();
        }
        return Mage::helper('catalog/image')->init($product, 'small_image')->constrainOnly(TRUE)->keepAspectRatio(TRUE)->keepFrame(FALSE)->resize(600, 600)->__toString();
    }

    public function changeData($data_change, $event_name, $event_value) {
        $this->_data = $data_change;
        // dispatchEvent to change data
        $this->eventChangeData($event_name, $event_value);
        return $this->getCacheData();
    }

    public function setCacheData($data, $module_name = '') {
        if ($module_name == "simi_connector") {
            $this->_data = $data;
            return;
        }
        if ($module_name == '' || is_null(Mage::getModel('connector/plugin')->checkPlugin($module_name)))
            return;
        $this->_data = $data;
    }

    public function getCacheData() {
        return $this->_data;
    }

    public function getReviewModel() {
        return Mage::getModel('connector/review');
    }

    public function getOptionModel() {
        return Mage::getModel('connector/catalog_product_options');
    }

    /*
     * get_all_produts
     */

    public function getAllProducts($data) {
        $limit = $data->limit;
        $offset = $data->offset;
        $width = null;
        $height = null;
        if(isset($data->width)){
            $width = $data->width;
        }
        if(isset($data->height)){
            $height = $data->height;
        }
        $sort_option = 0;
        if(isset($data->sort_option)){
            $sort_option = $data->sort_option;
        }
        
        $collection = $this->getProductCollection();
        $sort = $this->_helperCatalog()->getSortOption($sort_option);
        if ($sort) {
            $collection->setOrder($sort[0], $sort[1]);
        }

        $auction = null;
        if(isset($data->auction) && $data->auction){
            $auction = 1;
        }
        $information = $this->getListProduct($collection, $offset, $limit, $width, $height, $auction);
        return $information;
    }

    /*
     *  get productcollection all
     */

    public function getProductCollection() {
        $collection = $this->getResourceModel('catalog/product_collection');
        $collection->addAttributeToSelect($this->getProductAttributes());
        $collection->addFinalPrice();
        $this->setAvailableProduct($collection);
        return $collection;
    }

    /*
     *  change list product to array
     */

    public function getListProduct($collection, $offset, $limit, $width, $height, $auction=null) {
        if($auction != null){
            Mage::getModel('connector/auction_customize')->setListAuction($collection);
        }
        $productList = array();
        $collection->setPageSize($offset + $limit);
        $product_total = $collection->getSize();
  
        if ($offset > $product_total)
            return $this->statusError(array('No information'));
        $check_limit = 0;
        $check_offset = 0;
        foreach ($collection as $product) {
            if (++$check_offset <= $offset) {
                continue;
            }
            if (++$check_limit > $limit)
                break;
            $ratings = Mage::getModel('connector/review')->getRatingStar($product->getId());
            $total_rating = $this->getHelper()->getTotalRate($ratings);
            $avg = $this->getHelper()->getAvgRate($ratings, $total_rating);
            $prices = $this->getOptionModel()->getPriceModel($product);
            $manufacturer_name = "";
            try{
                // $manufacturer_name = $product->getAttributeText('manufacturer') == false ? '' : $product->getAttributeText('manufacturer');
            }catch(Exception $e){
                
            }



            $info_product = array(
                'product_id' => $product->getId(),
                'product_name' => $product->getName(),
                'product_type' => $product->getTypeId(),
                'product_regular_price' => Mage::app()->getStore()->convertPrice($product->getPrice(), false),
                'product_price' => Mage::app()->getStore()->convertPrice($product->getFinalPrice(), false),
                'product_rate' => $avg,
                'stock_status' => $product->isSaleable(),
                'product_review_number' => $ratings[5],
                'product_image' => $this->getImageProduct($product, null, $width, $height),
                'manufacturer_name' => $manufacturer_name,
                'is_show_price' => true,
            );

            if($auction != null){
                $modelAuction = Mage::getModel('auction/productauction');
                $showprice = Mage::getStoreConfig('auction/general/show_price');
                $delay = Mage::getStoreConfig('auction/general/delay_time');

                $now_time = Mage::getModel('core/date')->timestamp(time());
                $auction = $modelAuction->loadAuctionByProductId($product->getId());
                $lastBid = $auction->getLastBid();
                $currentPrice = $lastBid->getPrice() ? $lastBid->getPrice() : $auction->getInitPrice();
                $end_time = strtotime($auction->getEndTime() . ' ' . $auction->getEndDate());
                $bidder_name = $lastBid ? $lastBid->getBidderName() : Mage::helper('auction')->__('None');

                $info_product['auction_is_show_price'] = $showprice;
                $info_product['auction_is_delay'] = $delay;
                $info_product['auction_now_time'] = $now_time;
                $info_product['auction_end_time'] = $end_time;
                $info_product['auction_$currentPrice'] = $currentPrice;
                $info_product['auction_bidder_name'] = $bidder_name;
            }

            if ($prices) {
                $info_product = array_merge($info_product, $prices);
            }
            Mage::helper("connector/tax")->getProductTax($product, $info_product);

            $event_name = $this->getControllerName() . '_product_detail';
            $event_value = array(
                'object' => $this,
                'product' => $product
            );
            $data_change = $this->changeData($info_product, $event_name, $event_value);
            $productList[] = $data_change;
        }
        $information = '';
        if (count($productList)) {
            $information = $this->statusSuccess();
            $information['message'] = array($product_total);
            $information['data'] = $productList;
            $_taxHelper = Mage::helper('tax');
            if ($_taxHelper->displayBothPrices()){
                $information['other'] = array(
                    array(
                        'is_show_both_tax' => '1',
                    )
                );
            }else{
                $information['other'] = array(
                    array(
                        'is_show_both_tax' => '0',
                    )
                );
            }           
        } else {
            $information = $this->statusSuccess();
            $information['message'] = array($product_total);
            $information['data'] = $productList;
        }

        //$observerArr = array();
        //$observerObject =  json_encode($observerArr);
        $observerObject = new stdClass();
        $observerObject->information = $information;
        $observerObject->collection = $collection;
        Mage::dispatchEvent('simi_connector_get_product_list_after', array('object' => $observerObject));
        $information = $observerObject->information;

        return $information;
    }

    public function getDetail($data) {
        $id = $data->product_id;
        $width = null;
        $height = null;
        if(isset($data->width)){
            $width = $data->width;
        }
        if(isset($data->height)){
            $height = $data->height;
        }
        
        $product = $this->getProduct($id);
        if (!$product->getId()) {
            $information = $this->statusError();
            return $information;
        }
        
        $images = $product->getMediaGallery();
        $image_url = array();
        
        foreach ($images['images'] as $image) {     
            // Zend_debug::dump($image['disabled']);
            if ($image['disabled'] == 0){
                 $image_url[] = $this->getImageProduct($product, $image['file'], $width, $height);
            }           
        }       
        if (count($image_url) == 0) {
            $image_url[] = $this->getImageProduct($product, null, $width, $height);
        }
        $ratings = Mage::getModel('connector/review')->getRatingStar($product->getId());
        $total_rating = $this->getHelper()->getTotalRate($ratings);
        $avg = $this->getHelper()->getAvgRate($ratings, $total_rating);
        $option = $this->getOptionModel()->getOptions($product);
        $prices = $this->getOptionModel()->getPriceModel($product);
        $manufacturer_name = "";
        $product_short_description = "";
        try{
            $product_short_description = $product->getShortDescription();
            // $manufacturer_name = $product->getAttributeText('manufacturer') == false ? '' : $product->getAttributeText('manufacturer');
        }catch(Exception $e){
                
        }
        
        if($product_short_description == null){
            $product_short_description = "";
        }
        
        $_product = array(
            'product_id' => $id,
            'product_name' => $product->getName(),
            'product_type' => $product->getTypeId(),
            'product_regular_price' => Mage::app()->getStore()->convertPrice($product->getPrice(), false),
            'product_price' => Mage::app()->getStore()->convertPrice($product->getFinalPrice(), false),
            'product_description' => $product->getDescription(),
            'product_short_description' => $product_short_description,
            'max_qty' => (int) Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getQty(),
            'product_rate' => $avg,
            'product_images' => $image_url,
            'manufacturer_name' => $manufacturer_name,
            'product_review_number' => $ratings[5],
            '5_star_number' => $ratings[4],
            '4_star_number' => $ratings[3],
            '3_star_number' => $ratings[2],
            '2_star_number' => $ratings[1],
            '1_star_number' => $ratings[0],
            'stock_status' => $product->isSaleable(),
            'options' => $option,
            'product_attributes' => $this->getAttributes($product),
            'is_show_price' => true,
        );

        if ($prices) {
            $_product = array_merge($_product, $prices);
        }       
        //Max add to auction
        Mage::getModel("connector/auction_customize")->setActionToProduct($product, $_product);

        Mage::helper("connector/tax")->getProductTax($product, $_product, true, false);
        $event_name = $this->getControllerName() . '_product_detail';

        $event_value = array(
            'object' => $this,
            'product' => $product
        );
        $data_change = $this->changeData($_product, $event_name, $event_value);
        $information = '';
        if (count($data_change)) {
            $information = $this->statusSuccess();
            $information['data'] = array($data_change);
        } else {
            $information = $this->statusError();
        }
        return $information;
    }

    /*
     * get related product \
     * input id current product
     * output array list products related
     */

    public function getRelatedProducts($data) {
        $product = $this->getProduct($data->product_id);
        $limit = $data->limit;
        $width = null;
        $height = null;
        if(isset($data->width)){
            $width = $data->width;
        }
        if(isset($data->height)){
            $height = $data->height;
        }
        $collection = $product->getRelatedProductCollection()
                ->addAttributeToSelect($this->getProductAttributes())
                ->addAttributeToSelect('required_options')
                ->setPositionOrder()
                ->addStoreFilter();
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
        $collection->load();
        $information = $this->getListProduct($collection, 0, $limit, $width, $height);
        return $information;
    }

    /*
     * search product by key word. 
     */

    public function getSearchProducts($data) {
        $keyword = $data->key_word;
        $category_id = null;
        if(isset($data->category_id)){
            $category_id = $data->category_id;
        }
        $sort_option = 0;
        if(isset($data->sort_option)){
            $sort_option = $data->sort_option;
        }               
        $offset = $data->offset;
        $limit = $data->limit;
        $width = $data->width;
        $height = $data->height;
        $width = null;
        $height = null;
        if(isset($data->width)){
            $width = $data->width;
        }        
        if(isset($data->height)){
            $height = $data->height;
        }  
        $_helper = Mage::helper('catalogsearch');
        $queryParam = str_replace('%20', ' ', $keyword);
        Mage::app()->getRequest()->setParam($_helper->getQueryParamName(), $queryParam);
        /** @var $query Mage_CatalogSearch_Model_Query */
        $query = $_helper->getQuery();
        $query->setStoreId(Mage::app()->getStore()->getId());

        if ($query->getQueryText() != '') {
            $check = false;
            if (Mage::helper('catalogsearch')->isMinQueryLength()) {
                $query->setId(0)
                        ->setIsActive(1)
                        ->setIsProcessed(1);
            } else {
                if ($query->getId()) {
                    $query->setPopularity($query->getPopularity() + 1);
                } else {
                    $query->setPopularity(1);
                }

                if ($query->getRedirect()) {
                    $query->save();
                    //break
                    $check = true;
                } else {
                    $query->prepare();
                }
            }
            if ($check == FALSE) {
                Mage::helper('catalogsearch')->checkNotes();
                if (!Mage::helper('catalogsearch')->isMinQueryLength()) {
                    $query->save();
                }
            }
        } else {
            return $this->statusError();
        }
        if (method_exists($_helper, 'getEngine')) {
            $engine = Mage::helper('catalogsearch')->getEngine();
            if ($engine instanceof Varien_Object) {
                $isLayeredNavigationAllowed = $engine->isLeyeredNavigationAllowed();
            } else {
                $isLayeredNavigationAllowed = true;
            }
        } else {
            $isLayeredNavigationAllowed = true;
        }
        $layer = Mage::getSingleton('catalogsearch/layer');
        $category = null;
        if ($category_id) {
            $category = Mage::getModel('catalog/category')->load($category_id);
            $layer->setCurrentCategory($category);
        }
        $collection = $layer->getProductCollection();
        $productCollection = $collection->addAttributeToSelect($this->getProductAttributes());
        if ($category_id) {
            $productCollection->addCategoryFilter($category);
        }
        $sort = $this->_helperCatalog()->getSortOption($sort_option);

        if ($sort) {
            $productCollection->setOrder($sort[0], $sort[1]);
        }
        $information = $this->getListProduct($productCollection, $offset, $limit, $width, $height);
        return $information;
    }

    /*
     * get Product with category id     
     */

    public function getCategoryProduct($data) {
        // Zend_debug::dump($data);die();

        $categoryId = $data->category_id;
        $sort_option = $data->sort_option;
        $offset = $data->offset;
        $limit = $data->limit;
        $width = $data->width;
        $height = $data->height;
        $category = Mage::getModel('catalog/category')->load($categoryId);
        $storeId = Mage::app()->getStore()->getId();
        $productCollection = $category->getProductCollection()
                ->addAttributeToSelect($this->getProductAttributes())
                ->setStoreId($storeId)
               ->addFinalPrice();
        $sort = $this->_helperCatalog()->getSortOption($sort_option);

        if ($sort) {
            $productCollection->setOrder($sort[0], $sort[1]);
        }
        // Zend_debug::dump($sort);die();
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($productCollection);
        $productCollection->addUrlRewrite(0);
        return $this->getListProduct($productCollection, $offset, $limit, $width, $height);
    }

    public function getProductReview($data) {
        $information = Mage::getModel('connector/review')->getProductReview($data);
        return $information;
    }

    // add Attributes for product 
    public function getAttributes($product) {
        $result = array();
        $attributes = $product->getAttributes();
        foreach ($attributes as $attribute) {
            if ($attribute->getIsVisibleOnFront()) {
                // Zend_debug::dump(get_class_methods($attribute));die();
                $result[] = array(
                    'title' => $attribute->getFrontendLabel(),
                    'value' => $attribute->getFrontend()->getValue($product),
                );
            }
        }
        return $result;
    }
        public function getWishlistProducts($data) {
        $limit = $data->limit;
        $offset = $data->offset;
        $width = $data->width;
        $height = $data->height;
        $sort_option = $data->sort_option;      
        $customerId = $this->_getSession()->getCustomer()->getId();
        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if($customerId && ($customerId!='')){   
        $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
        $wishListItemCollection = $wishlist->getItemCollection();
        $itemList = array();
        foreach ($wishListItemCollection as $item)
        {
            $itemList[] = $item->getProductId();           
        }       
        $collection = $this->getResourceModel('catalog/product_collection')
        ->addAttributeToFilter('entity_id', $itemList);
        $collection->addAttributeToSelect($this->getProductAttributes());
        $collection->addFinalPrice();       
        $this->setAvailableProduct($collection);
            
        
        }
        $sort = $this->_helperCatalog()->getSortOption($sort_option);
        if ($sort) {
            $collection->setOrder($sort[0], $sort[1]);
        }
        $information = $this->getListProduct($collection, $offset, $limit, $width, $height);
        return $information;
    }

    public function getDeepLink($data){
        $link = $data->link;
        $information = $this->statusError();
        if($link){
            $product = $this->getProductByLink($link);
            if($product->getId()){
                $information = $this->statusSuccess();
                $productInfo['id'] = $product->getId();
                $productInfo['type'] = 1;
                $information['data'] = $productInfo;
            }else{
                $category = $this->getCategoryByLink($link);
                if($category->getId()){
                    $categoryChildrenCount = $category->getChildrenCount();
                    if($categoryChildrenCount > 0){
                        $hasChild = 1;
                    }else{
                        $hasChild = 0;
                    }                        
                    $information = $this->statusSuccess();
                    $categoryInfo['id'] = $category->getId();
                    $categoryInfo['type'] = 2;
                    $categoryInfo['has_child'] = $hasChild;
                    $information['data'] = $categoryInfo;
                }
            }
        }
        return $information;
    }

    public function getProductByLink($link){
        $product = Mage::getModel('catalog/product')->getCollection()
                ->addAttributeToSelect('url_key')
                ->addFieldToFilter('url_key', $link)
                ;
        return $product->getFirstItem();
    }

    public function getCategoryByLink($link){
        $category = Mage::getModel('catalog/category')->getCollection()
                ->addAttributeToSelect('url_key')
                ->addFieldToFilter('url_key', $link);
        return $category->getFirstItem();
    }

}

