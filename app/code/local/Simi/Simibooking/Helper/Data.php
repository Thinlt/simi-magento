<?php

class Simi_Simibooking_Helper_Data extends Mage_Core_Helper_Abstract
{
	const TYPE_SINGLE           = 'single';
    
    const TYPE_RECURRENT_DAY    = 'recurrent_day';
    
    const TYPE_RECURRENT_DATE   = 'recurrent_date';
    
    const TYPE_PERIOD           = 'period';
    
    const TYPE_RECURRENT_PERIOD = 'recurrent_period';
	
    const BOOKABLE_TYPE_CODE = "bookable";
    const RESERVATION_TYPE_CODE = "reservation";
	public function getExcludeddays($product_id,$store_id){
		$excludeddays=array();
		$colls = Mage::getModel('booking/excludeddays')
                ->getCollection()
                ->addEntityIdFilter($product_id)
                ->addStoreIdFilter($store_id)
            ;
		// Zend_debug::dump($coll->getData());die;
		
		foreach($colls as $item){
			switch ($item->getType()) {
				case self::TYPE_SINGLE:
					$excludeddays[]=array(
						'product_id'=>$item->getEntity_id(),
						'period_type'=>$item->getPeriod_type(),
						'period_from'=>$item->getPeriod_from(),
						'period_to'=>"" ,
					);
					break;
				case self::TYPE_RECURRENT_DAY:
					$excludeddays[]=array(
						'product_id'=>$item->getEntity_id(),
						'period_type'=>$item->getPeriod_type(),
						'period_from'=>$item->getPeriod_from(),
						'period_to'=>"" ,
					);
					break;
				case self::TYPE_RECURRENT_DATE:
					$excludeddays[]=array(
						'product_id'=>$item->getEntity_id(),
						'period_type'=>$item->getPeriod_type(),
						'period_from'=>$item->getPeriod_from(),
						'period_to'=>"" ,
					);
					break;
				case self::TYPE_PERIOD:
					$excludeddays[]=array(
						'product_id'=>$item->getEntity_id(),
						'period_type'=>$item->getPeriod_type(),
						'period_from'=>$item->getPeriod_from(),
						'period_to'=>$item->getPeriod_to() ,
					);
					break;
				case self::TYPE_RECURRENT_PERIOD:
					$excludeddays[]=array(
						'product_id'=>$item->getEntity_id(),
						'period_type'=>$item->getPeriod_type(),
						'period_from'=>$item->getPeriod_from(),
						'period_to'=>$item->getPeriod_to() ,
						'period_recurrence_type'=>$item->getPeriod_recurrence_type(),
						
					);
					break;
			}
		}
		
		return $excludeddays;
	}
	
	public function updateBookedForProductAction($product)
    {
		$product_id=$product->getId();
		$data=array();
        if(!$product_id){
            return;
        }
        $product = ITwebexperts_Payperrentals_Helper_Data::initProduct($product_id);//todo might not be necessary

        $qtyArr = ITwebexperts_Payperrentals_Helper_Inventory::getQuantityArrayForProduct(
            $product, 1, null, null, null, null, true
        );
        $paddingDays = array();
        foreach($qtyArr as $iProduct => $iQty) {
            $paddingDaysNew = ITwebexperts_Payperrentals_Helper_Data::getFirstAvailableDateRange($iProduct, null, false, true);
            if(!$paddingDaysNew){
                $paddingDaysNew = array();
            }
            $paddingDays = array_merge($paddingDays, $paddingDaysNew);
        }
        if(count($paddingDays) > 0){
            $data['disabled_dates_padding'] = ITwebexperts_Payperrentals_Helper_Data::toFormattedDateArray($paddingDays, false);
			$data['message']=$this->__('If you require service within 48 hours, please contact us directly');
        }
		return $data;
    }
	
	public function renderDates($options, $item = null, $product = null, $isCart = false)
    {
        $isSingle = true;
        $nonSequential = 0;
        if ($item && $item->getOrder() && !$isCart) {
            $isSingleBool = ITwebexperts_Payperrentals_Helper_Data::isSingleOrder($item->getOrder());
            $isSingle = $isSingleBool['bool'];
        }else if ($item && $item->getQuote() && !$isCart) {
            $isSingle = false;
        }

        $productId = -1;
        $storeId = 1;
        $qty = 0;
        if (!is_null($item) && !is_null($item->getProductId())) {
            $productId = $item->getProductId();
            $storeId = $item->getStoreId();
            $qty = $item->getQty();
            $product = $item->getProduct();
        } else {
            if (!is_null($product) && is_object($product)) {
                $productId = $product->getId();
            } elseif (isset($options['info_buyRequest']['product'])) {
                $productId = $options['info_buyRequest']['product'];
            }
        }
        $showTime = (bool)Mage::getResourceModel('catalog/product')
            ->getAttributeRawValue(
                $productId,
                'payperrentals_use_times',
                $storeId
            );
        $customerGroup = ITwebexperts_Payperrentals_Helper_Data::getCustomerGroup();

        $source = $this->_getOptionsArray($product, $options);
        $options = array();

        $isBuyout = isset($source['buyout'])?$source['buyout']:'false';
        if($isBuyout != "false") {
            $options[] = array('option_title' => $this->__('Product Type'),
                               'option_value' => 'Rental Buyout',
                               'option_type' => 'reservation');
        }else if (isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] ) && $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] != '') {
            $startDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] , !$showTime, false);
            $endDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] , !$showTime, false);
            if (!isset($nonSequential) || $nonSequential == 0) {
                $endDate = ITwebexperts_Payperrentals_Helper_Date::formatDbDate($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION] , !$showTime, false);

                if(isset($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::FIXED_DATE_ID])){
                    $fixedRentalDatesCollection = Mage::getModel('payperrentals/fixedrentaldates')->getCollection()
                        ->addFieldToFilter('id', $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::FIXED_DATE_ID])
                        ->getFirstItem()
                    ;
                    $fixedNameId = $fixedRentalDatesCollection->getNameid();
                    $fixedStartDate = $fixedRentalDatesCollection->getStartDate();
                    $fixedEndDate = $fixedRentalDatesCollection->getEndDate();
                    $difference = strtotime($fixedEndDate) - strtotime($fixedStartDate);
                    $startDate = date('Y-m-d', strtotime($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) . ' '. date('H:i:s', strtotime($fixedStartDate));
                    $endDate = date('Y-m-d H:i:s', (strtotime($source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION]) + $difference));
                    $fixedRentalNamesCollection = Mage::getModel('payperrentals/fixedrentalnames')->getCollection()
                        ->addFieldToFilter('id', $fixedNameId)
                        ->getFirstItem()
                    ;
                    $fixedName = $fixedRentalNamesCollection->getName();
                    $options[] = array('option_title' => $this->__('Start Date'), 'option_value' => ITwebexperts_Payperrentals_Helper_Date::formatDbDate($startDate, !$showTime, false), 'option_type' => 'reservation');
                    $options[] = array('option_title' => $this->__('End Date'), 'option_value' => ITwebexperts_Payperrentals_Helper_Date::formatDbDate($endDate, !$showTime, false), 'option_type' => 'reservation');
                }else{
                    $options[] = array('option_title' => $this->__('Start Date'), 'option_value' => $startDate, 'option_type' => 'reservation');
                    $options[] = array('option_title' => $this->__('End Date'), 'option_value' => $endDate, 'option_type' => 'reservation');
                }
            } else {
                $options[] = array('option_title' => $this->__('Dates:'), 'option_value' => ITwebexperts_Payperrentals_Helper_Date::localiseNonsequentialBuyRequest($startDate, $showTime), 'option_type' => 'reservation');
            }

            if(!$isCart && $isSingle){
                $options = array();
            }


            //$damageWaiver = ITwebexperts_Payperrentals_Helper_Price::getDamageWaiver($productId, 1);
            if (!is_null($item) && $item->getDamageWaiverPrice()) {
                $options[] = array(
                    'option_title' => $this->__('Damage Waiver'),
                    'option_value' => ITwebexperts_Payperrentals_Helper_Price::getDamageWaiverHtml($item, (bool)$item->getBuyRequest()->getDamageWaiver(), $qty, $item->getDamageWaiverPrice()),
                    'option_type' => 'reservation'
                );
            }
        } else {
            return array();
        }

        $resultObject = new Varien_Object();
        $resultObject->setResult($options);
        Mage::dispatchEvent('render_cart', array('options' => $source, 'result' => $resultObject, 'product' => $product, 'item' => $item, 'is_cart' => $isCart));

        return $resultObject->getResult();

    }
	 private function _getOptionsArray($product, $options){
        $source = array();

        if (is_object($product) && !is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)) && is_object($product->getCustomOption('info_buyRequest'))) {
            $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
        }elseif (isset($options['info_buyRequest']) && isset($options['info_buyRequest'][ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION])) {
            $source =  $options['info_buyRequest'];
        }elseif(is_object($product)){
            if ($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)) {
            $start_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION)->getValue();
            $end_date = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION)->getValue();
            if (is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL))) {
                $nonSequential = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL)->getValue();
                $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::NON_SEQUENTIAL] = $nonSequential;
            }
            if (is_object($product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::FIXED_DATE_ID))) {
                $fixedDateId = $product->getCustomOption(ITwebexperts_Payperrentals_Model_Product_Type_Reservation::FIXED_DATE_ID)->getValue();
                $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::FIXED_DATE_ID] = $fixedDateId;
            }
            $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::START_DATE_OPTION] = $start_date;
            $source[ITwebexperts_Payperrentals_Model_Product_Type_Reservation::END_DATE_OPTION] = $end_date;
            }
        }

        return $source;
    }

	public function getBookingAtributes($item){
		$product=$item->getProduct();
		$fromDateValue = $product->getCustomOption('aw_booking_from')->getValue();
        $fromTimeValue = $product->getCustomOption('aw_booking_time_from')->getValue();
        $toDateValue = $product->getCustomOption('aw_booking_to')->getValue();
        $toTimeValue = $product->getCustomOption('aw_booking_time_to')->getValue();
        $data = array(
            new Zend_Date("{$fromDateValue} {$fromTimeValue}", AW_Core_Model_Abstract::DB_DATETIME_FORMAT),
            new Zend_Date("{$toDateValue} {$toTimeValue}", AW_Core_Model_Abstract::DB_DATETIME_FORMAT),
        );
        if (!is_object($product->getCustomOption(AW_Booking_Model_Product_Type_Bookable::FROM_DATE_OPTION_NAME))) {
            $source = unserialize($product->getCustomOption('info_buyRequest')->getValue());
            $from_date = $source['aw_booking_from'];
            $to_date = $source['aw_booking_to'];
            $data = array(
                new Zend_Date(
                    $from_date,
                    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                ),
                new Zend_Date(
                    $to_date,
                    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT)
                ),
            );
        }
        return array(
				array(
					'option_title'=>$this->__("Start date"),
					'option_value'=>$this->formatDate(
                    $data[0], 'short', $product->getAwBookingRangeType() != 'date_fromto'),
				),
				array(
					'option_title'=>$this->__("End date"),
					'option_value'=>$this->formatDate(
                    $data[1], 'short', $product->getAwBookingRangeType() != 'date_fromto'),
                ),
			);
	}
	
	
	public function formatDate($date = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showTime = false)
    {
        return Mage::helper('core')->formatDate($date, $format, $showTime);
    }
	
	public function formatTime($time = null, $format = Mage_Core_Model_Locale::FORMAT_TYPE_SHORT, $showDate = false)
    {
        return  Mage::helper('core')->formatTime($time, $format, $showDate);
    }

}