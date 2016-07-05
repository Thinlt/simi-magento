<?php

class Simi_Simibooking_Model_Simibooking extends Simi_Connector_Model_Abstract {
	
	public function _construct(){
		parent::_construct();
		$this->_init('simibooking/simibooking');
	}
	
	public function getOptionModel() {
        return Mage::getModel('simibooking/catalog_product_options');
    }
	
	protected function getProduct($id) {
        return Mage::getModel('catalog/product')->load($id);
    }
	
	protected function getHelper() {
        return Mage::helper('connector');
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
	
	
	public function update_price($data)
    {

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
        //Zend_Debug::dump($product->getData());
        if (!$product->getId()) {
            $information = $this->statusError();
            return $information;
        }
        
        $images = $product->getMediaGallery();
        $image_url = array();
        
        foreach ($images['images'] as $image) {     
            // Zend_debug::dump($image['disabled']);
            // if ($image['disabled'] == 0){
                 $image_url[] = $this->getImageProduct($product, $image['file'], $width, $height);
            // }           
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
		$storeId = Mage::app()->getStore()->getStoreId();
		$bookingCongfig=array(
			'booking_quantity'		=>	$product->getAw_booking_quantity(),
			'booking_range_type'	=>	$product->getAw_booking_range_type(),
			'booking_time_from'		=>	str_replace(',',':',$product->getAw_booking_time_from()),
			'booking_time_to'		=>	str_replace(',',':',$product->getAw_booking_time_to()),
			'booking_date_from'		=>	$product->getAw_booking_date_from(),
			'booking_date_to'		=>	$product->getAw_booking_date_to(),
			'booking_excludeddays'	=>	Mage::helper('simibooking')->getExcludeddays($product->getId()),
		);
			
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
		if($product->getTypeId() == Simi_Simibooking_Helper_Data::BOOKABLE_TYPE_CODE){
			$_product['booking_config']=$bookingCongfig;
			$_product['bookable_price_update']=$this->getBookingPrice($data);
			
		}
		
        if ($prices) {
            $_product = array_merge($_product, $prices);
        }
		$information=$this->statusSuccess();
        Mage::helper("connector/tax")->getProductTax($product, $_product, true, false);
        $information['data'] = array($_product);
        return $information;
    }
	/*
	*
	*Get price Reservation product
	*/
	protected function _getReservationPriceUpdate($data){
		 
        $productId =$data->product_id;
        $product = Mage::getModel('catalog/product')->load($productId);
		$data=array();
        $qty = isset($data->qty) ? $data->qty : 1;
		$start_date=$data->date_time_from;
		$end_date=$data->date_time_to;
		$buyout=$data->buyout;
		$requestOptions=$data->options;
		
		$option_type_id=isset($data->options->option_type_id) ? $data->options->option_type_id : "";
		$option_id=isset($data->options->option_id) ? $data->options->option_id : "";
		$options=array(
			$option_type_id => $option_id,
		);
		
		$parrams=array(
			'product'		=>	$productId,
			'start_date'	=>	$startDate,
			'end_date'		=>	$end_date,
			'options' 		=>	$options,
			'qty'			=>	$qty,
			'buyout'		=> 	$buyout,
		);
        list($startDate, $endDate) = ITwebexperts_Payperrentals_Helper_Date::saveDatesForGlobalUse($parrams);
        $attributes = $data->super_attribute ? $data->super_attribute : null;
        $bundleOptions = $data->bundle_option ?  $data->bundle_option : null;
        $bundleOptionsQty1 = $data->bundle_option_qty1 ? $data->bundle_option_qty1  : null;
        $bundleOptionsQty = $data->bundle_option_qty  ? $data->bundle_option_qty : null;


	}
	
	/*
	*
	*Get price bookable product
	*/
	public function getBookingPrice($data){
		$product_id=$data->product_id;
		$date_from=$data->date_from;
		$date_to=$data->date_to;
		$_date=$data->date;
		
		
        if (!$product_id) {
            return $this->statusError(array(Mage::helper("simibooking")->__("Can not find the product")));
        }
        $product = Mage::getModel('catalog/product')->load($product_id);
        if ($from = urldecode($date_from)) {
            $zDateFrom = new Zend_Date($from, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            if ($to = urldecode($date_to)) {
                $zDateTo = new Zend_Date($to, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
                // From and To are now ready. Get price for period
                $price = $product->getPriceModel()->getBookingPrice(
                    $product, $zDateFrom, $zDateTo, null, AW_Core_Model_Abstract::RETURN_ARRAY
                );
            }
        }
        if ($date = urldecode($_date)) {
            $zDate = new Zend_Date($date, AW_Core_Model_Abstract::DB_DATETIME_FORMAT);
            $price = $product->getPriceModel()->getBookingPrice($product, $zDate);
        }
		$dataReturn=is_array($price) ? $price[0] : $price;
		
		return $dataReturn;
		
	}
}