<?php
/**
 *
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    
 * @package     Similayerednavigation
 * @copyright   Copyright (c) 2012 
 * @license     
 */

/**
 * Observer Model
 * 
 * @category    
 * @package     Similayerednavigation
 * @author      Developer
 */
class Simi_Similayerednavigation_Model_Observer extends Simi_Connector_Model_Catalog_Product
{
    /**
     * process controller_action_predispatch event
     *
     * @return Simi_Similayerednavigation_Model_Observer
     */
    public function controllerActionPredispatch($observer)
    {
        $action = $observer->getEvent()->getControllerAction();
        return $this;
    }

    public function getItemsShopBy($block){
        $_children = $block->getChild();
        $refineArray = array();
        foreach ($_children as $index => $_child) {
            if ($index == 'layer_state') {
                // $itemArray = array();
                foreach ($_child->getActiveFilters() as $item) {
                    $itemValues = array();
                    $itemValues = $item->getValue();
                    if(is_array($itemValues)){
                        $itemValues = implode('-', $itemValues);
                    }

                    if($item->getFilter()->getRequestVar() != null){
                        $refineArray['layer_state'][] = array(
                            'attribute' => $item->getFilter()->getRequestVar(),
                            'title' => $item->getName(),
                            'label' => (string) strip_tags($item->getLabel()), //filter request var and correlative name
                            'value' => $itemValues,
                        ); //value of each option
                    }
                }
                // $refineArray[] = $itemArray;
            }else{
                $items = $_child->getItems();
                $itemArray = array();
                foreach ($items as $index => $item) {
                    $filter = array();
                    if ($index == 0) {
                        foreach ($items as $index => $item){
                            $filter[] = array(
                                'value' => $item->getValue(), //value of each option
                                'label' => strip_tags($item->getLabel()),
                            );
                        }

                        if($item->getFilter()->getRequestVar() != null) {
                            $refineArray['layer_filter'][] = array(
                                'attribute' => $item->getFilter()->getRequestVar(),
                                'title' => $item->getName(), //filter request var and correlative name
                                'filter' => $filter,
                            );
                        }
                    }
                }
            }
        }
        return $refineArray;
    }

    public function connectorCatalogGetAllProductsReturn($observer) {  
        $observerObject = $observer->getObject();
        $observerData = $observer->getObject()->getData();
        // get json parameter then set again as normal parameter
        $value = $observerObject->getRequest()->getParam('data');
        $params = $observerObject->getRequest()->getParams();
        $data = json_decode($value);

        foreach ($data as $id => $param) {
            if ($id == 'category_id')
                $id = 'cat';
            if($id != 'filter')
                $params[(string) $id] = (string) $param;
        }
        $observerObject->getRequest()->setParams($params);
        $filter = array();
        $filter = $data->filter;
        $params = json_decode(json_encode($filter), true);
        // if(in_array($filter))
        foreach ($params as $key => $value) {
            $observerObject->getRequest()->setParam($key,$value);
        }

        $table = $observerObject->getLayout()->createBlock('catalog/layer_view');

        $observerData['layerednavigation'] = $this->getItemsShopBy($table);

        if(isset($data->brand) && $data->brand){
            $search_data = Mage::getModel('connector/brand_customize')->getSearchData();
            $observerData['search_box'] = $search_data['data'];
            $shopbybrands = Mage::getModel('connector/brand_customize')->getBrandOnSideBar($data);
            $observerData['brands'] = $shopbybrands;
        }

        $productList = $this->changeProductList($data, $table);
        $observerData['data'] = $productList['data'];
        $observerData['message'] = $productList['message'];

        $observerData['other'][0]['product_id_array'] = $productList['other'];
        // $observerData['other'] = $productList['other'];
        $observerObject->setData($observerData);
    }

    public function connectorCatalogGetCategoryProductsReturn($observer) {        
        $observerObject = $observer->getObject();
        $observerData = $observer->getObject()->getData();
        // get json parameter then set again as normal parameter
        $value = $observerObject->getRequest()->getParam('data');
        $params = $observerObject->getRequest()->getParams();
        $data = json_decode($value);
        $categoryM = Mage::getModel('catalog/category')->load($data->category_id);
        $m = $categoryM->getData();
        if($categoryM->getData('include_in_menu') == 0) return;

        foreach ($data as $id => $param) {
            if ($id == 'category_id')
                $id = 'cat';
            if($id != 'filter')
                $params[(string) $id] = (string) $param;
        }
        $observerObject->getRequest()->setParams($params);
        $filter = array();
        $filter = $data->filter;
        $params = json_decode(json_encode($filter), true);
        // if(in_array($filter))
        foreach ($params as $key => $value) {
            $observerObject->getRequest()->setParam($key,$value);
        }
        $table = $observerObject->getLayout()->createBlock('catalog/layer_view');

        $observerData['layerednavigation'] = $this->getItemsShopBy($table);
        $productList = $this->changeProductList($data, $table);
        $observerData['data'] = $productList['data'];
        $observerData['message'] = $productList['message'];
        $observerData['other'][0]['product_id_array'] = $productList['other'];
        // $observerData['other'] = $productList['other'];
        $observerObject->setData($observerData);
    }

    public function connectorCatalogSearchProductsReturn($observer) {  
        $observerObject = $observer->getObject();
        $observerData = $observer->getObject()->getData();
        // get json parameter then set again as normal parameter
        $value = $observerObject->getRequest()->getParam('data');
        $params = $observerObject->getRequest()->getParams();
        $data = json_decode($value);

        foreach ($data as $id => $param) {
            if ($id == 'category_id')
                $id = 'cat';
            if($id != 'filter')
                $params[(string) $id] = (string) $param;
        }
        $observerObject->getRequest()->setParams($params);
        $filter = array();
        $filter = $data->filter;
        $params = json_decode(json_encode($filter), true);
        // if(in_array($filter))
        foreach ($params as $key => $value) {
            $observerObject->getRequest()->setParam($key,$value);
        }

        $table = $observerObject->getLayout()->createBlock('catalogsearch/layer');
        $observerData['layerednavigation'] = $this->getItemsShopBy($table);
        $productList = $this->changeProductList($data, $table);
        $observerData['data'] = $productList['data'];
        $observerData['message'] = $productList['message'];
        // $observerData['other'] = $productList['other'];
        $observerObject->setData($observerData);
    }

    public function changeProductList($data, $table){
        $categoryId = $data->category_id;
        if($categoryId < 0 || $categoryId == NULL){
            $categoryId = Mage::app()->getWebsite()->getDefaultStore()->getRootCategoryId();
        }
       
        $sort_option = $data->sort_option;
        $offset = $data->offset;
        $limit = $data->limit;
        $width = $data->width;
        $height = $data->height;
        $storeId = Mage::app()->getStore()->getId();
        $layerView = $table;
        // $layerView->getLayer()->apply(Mage::app()->getRequest());
        $layer = $layerView->getLayer();

        $productCollection = $layer->getProductCollection();
        $productCollection = $productCollection
                ->setStoreId($storeId)
                ->addFinalPrice();
        $sort = $this->_helperCatalog()->getSortOption($sort_option);

        if ($sort) {
            $productCollection->setOrder($sort[0], $sort[1]);
        }        

        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($productCollection);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInSearchFilterToCollection($productCollection);
        $productCollection->addUrlRewrite(0);

        $auction = null;

        if(isset($data->auction) && $data->auction){
            $auction = 1;
        }

        $brand = null;
        if(isset($data->brand) && $data->brand){
            $brand = $data->brand;
        }
        return $this->getProductList($productCollection, $offset, $limit, $width, $height, $auction, $brand);
    }

    /*
     *  change list product to array
     */

    public function getProductList($collection, $offset, $limit, $width, $height, $auction=null, $brand=null) {
        if($auction != null){
            Mage::getModel('connector/auction_customize')->setListAuction($collection);
        }

        if($brand != null){
            Mage::getModel('connector/brand_customize')->setProductByBrand($brand, $collection);
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
                $info_product['auction_current_price'] = $currentPrice;
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

        $producIdArray = array();
        $information['other'][0] = array(
            'product_id_array' => array()
        );

        foreach ($productList as $_product) {
            $producIdArray[] = $_product['product_id'];
        }

        $information['other'] = $producIdArray;

        return $information;
    }

    /*
     *  change searching list product to array
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

    public function getlink() {
        $link = Mage::app()->getRequest()->getRouteName() .
            Mage::app()->getRequest()->getControllerName() .
            Mage::app()->getRequest()->getActionName() .
            Mage::app()->getRequest()->getModuleName();
        return $link;
    }

    public function catalog_product_collection_apply_limitations_after($observer) {
        if ($this->getlink() != 'connectorcatalogget_all_productsconnector')
            return $this;
        $productCollection = $observer['collection'];
        $pararm = Mage::app()->getRequest()->getParam('data');
        $ob = json_decode($pararm);
        if(is_object($ob) && isset($ob->auction)
            && $ob->auction
            && Mage::helper('core/data')->isModuleEnabled("Magestore_Auction")){
            $productCollection->addFieldToFilter('entity_id', array('in' => $Ids = Mage::helper('auction')->getProductAuctionIds(Mage::app()->getStore()->getId())));
        }
        return $this;

    }

}
