<?php

/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 1/22/2016
 * Time: 11:54 AM
 */
class Simi_Simibooking_Model_Observer
{
   
	public function getOptionModel()
    {
        return Mage::getModel("simibooking/catalog_product_options");
    }
	
    public function changeProductOptions($observer)
    {
		
        $object = $observer->getObject();
        $product = $observer->getProduct();
		$this->addProductAttributes($product,$object);
        $observer->setObject($object);
    }

    public function addProductAttributes($product,&$object){
		$arrayConfig=array();
		$store_id=Mage::app()->getStore()->getStoreId();
        if($product->getTypeId()==Simi_Simibooking_Helper_Data::BOOKABLE_TYPE_CODE){
			$options = $this->getOptionModel()->getOptions($product);
			$excludeddays=Mage::helper('simibooking')->getExcludeddays($product->getId(),$store_id);
			
			$arrayConfig['booking_quantity']=$product->getAw_booking_quantity();
			$arrayConfig['booking_range_type']=$product->getAw_booking_range_type();
			$arrayConfig['booking_time_from']=str_replace(',',':',$product->getAw_booking_time_from());
			$arrayConfig['booking_time_to']=str_replace(',',':',$product->getAw_booking_time_to());
			$arrayConfig['booking_date_from']=$product->getAw_booking_date_from();
			$arrayConfig['booking_date_to']=$product->getAw_booking_date_to();
			$arrayConfig['booking_excludeddays']=$excludeddays;
			$object->options=$options;
			
			$object->setData('booking_config',$arrayConfig);
        }else if($product->getTypeId()==Simi_Simibooking_Helper_Data::RESERVATION_TYPE_CODE){
			$options = $this->getOptionModel()->getOptions($product);
			$object->options=$options;
			$reservation_excludeddays=Mage::helper('simibooking')->updateBookedForProductAction($product);
			if(sizeOf($reservation_excludeddays)>0){
				$object->setData('reservation_excludeddays',$reservation_excludeddays);
			}
			
        }
    }
	
	public function addParramsAddToCart($observer){
		$event=$observer->getEvent();
		$parrams=$event->getObject();
		$options=array();
		$data=$event->getData('request_data');
		if(!isset($data->product_type) || $data->product_type==null)
		{
			return;
		}
		
		if($data->product_type=='bookable'){
			if(isset($data->date_from)){
			$parrams->setData('aw_booking_from',$data->date_from);
			}
			if(isset($data->date_to)){
				$parrams->setData('aw_booking_to',$data->date_to);
			}
			if(isset($data->time_from)){
				$parrams->setData('aw_booking_time_from',array(
					'hours'=>$data->time_from->hours,
					'minutes'=>$data->time_from->minutes,
					'daypart'=>$data->time_from->daypart,
				));
			}
			if(isset($data->time_to)){
				$parrams->setData('aw_booking_time_to',array(
					'hours'=>$data->time_to->hours,
					'minutes'=>$data->time_to->minutes,
					'daypart'=>$data->time_to->daypart,
				));
			}
			$data_options=isset($data->options)? $data->options : null;
			if(null != $data){
				foreach($data_options as $option){
					$options[$option->option_type_id] = $option->option_id;
				}
			}
			$parrams->setData('options',$options);
		}
		//
		if($data->product_type=='reservation'){
			$qty = isset($data->qty) ? $data->qty : 1;
			$start_date=$data->date_from;
			$end_date=$data->date_to;
			$buyout=$data->buyout;

			$data_options=isset($data->options)? $data->options : null;
			if(null != $data){
				foreach($data_options as $option){
					$options[$option->option_type_id] = $option->option_id;
				}
			}
			
			$parrams->setData(array(
				'start_date'	=>	$start_date,
				'end_date'		=>	$end_date,
				'options' 		=>	$options,
				'qty'			=>	$qty,
				'buyout'		=> 	$buyout,
				)
			);
		}
	}
	
	
}