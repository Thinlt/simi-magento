<?php

/**
 * Created by PhpStorm.
 * User: Scott
 * Date: 1/22/2016
 * Time: 11:54 AM
 */
class Simi_Simicustomsize_Model_Observer
{
	
	public function getCustomerProfiles($observer){
		if(Mage::helper('simicustomsize')->checkPluginEnable()){
			$observerObject = $observer->getObject();
            $data = $observer->getObject()->getData();
			$data_return=$data['data'];
			$customer_profiles=array();
		
			$customerId = Mage::helper('simicustomsize')->getCustomerIdByEmail($data_return[0]['user_email']);
			
			$customSizeProfile = Mage::getModel('simicustomsize/simicustomsize')->getProfiles($customerId);
			$fieldset = Mage::getModel('simicustomsize/simicustomsize')->getFieldsets();
			$basicFieldset=Mage::getModel('simicustomsize/simicustomsize')->getFieldsFromFieldset('basic');
			if(sizeOf($customSizeProfile)>0){
				foreach($customSizeProfile as $profile){
					$profile=Mage::getModel('custsize/profile')->load($profile->getId());
					$arr_attr=array();
					$arr_fieldset=array();
					
					$arr_attr['id']=$profile->getId();
					$arr_attr['name']=$profile->getName();
					$arr_attr['is_default']=$profile->getDefault() != null ? (string)$profile->getDefault() : "0" ;
					$arr_attr['description']=$profile->getDescription();
					
					$arr_fields_basic=array();
					if(sizeOf($basicFieldset)>0) {
						$arr_fields_basic=Mage::getModel('simicustomsize/simicustomsize')->getField('basic',$profile);
					}
					
					foreach($fieldset as $item){
						if($item->getTag()=='basic')
							continue;
						
						$arr_fieldset[]=array(
							'id' => $item->getId(),
							'label' => $item->getLabel(),
							'description' => $item->getDescription(),
							'fields' => Mage::getModel('simicustomsize/simicustomsize')->getField($item->getTag(),$profile),
						);
						
					}
					
					$arr_attr['fieldset']=$arr_fieldset;
					$arr_attr['basic_fieldset']=$arr_fields_basic;
					$customer_profiles[]=$arr_attr;
				}
			}
			
			
			$data_return[0]['customsize_profiles']=$customer_profiles;
			$data['data']=$data_return;
			$observerObject->setData($data);
		}
	}
	
	public function changeProductDetailReturn($observer){
			
		if(Mage::helper('simicustomsize')->checkPluginEnable()){
			$object = $observer->getObject();
			$data=$object->getData();
		
			$product=Mage::getModel('catalog/product')->load($data['data'][0]['product_id']);
			
			$data['data'][0]['is_custom_size']=$product->getCustsize()!=null ? $product->getCustsize() :"0";
			$object->setData($data);
			$observer->setObject($object);
		}
	}
	
	public function changeRequestInfo($observer){
		$event=$observer->getEvent();
		$parrams=$event->getObject();
		
		$options=array();
		$data=$event->getData('request_data');
		if(!isset($data->custsize_profile_id) || $data->custsize_profile_id==null)
		{
			return;
		}
		$parrams->setData('custsize_profile_id',$data->custsize_profile_id);
		
	}

	
	public function checkoutCartAddProductComplete($observer)
    {
        // Get objects from the event
        $product = $observer->getEvent()->getProduct();
        $request = $observer->getEvent()->getRequest();
        $params = $request->getParams();
		$data=json_decode($params['data']);
        if(isset($data->custsize_profile_id)) {

            // Get the current quote
            $quote = Mage::getSingleton('checkout/session')->getQuote();

            // Search the quote for the current product
            $items = $quote->getAllItems();
            $quoteProduct = null;
            foreach ($items as $item) {
                if ($item->getProductId() == $product->getId()) {
                    $quoteProduct = $item;
                    break;
                }
            }

            $profileId = $data->custsize_profile_id;
            if($quoteProduct != null && $profileId > 0) {

                // Load the custsize data into an array
                $custsizeData = Mage::helper('custsize')->getProfileValues($profileId);

                // Add extra data within the quote
                $this->addAdditionalData($quoteProduct, 'custsize', $custsizeData);

                // Save the changed information
                $quoteProduct->save();

            }
        }

        return $this;
    }

  
    protected function addAdditionalData(&$item, $key, $value)
    {
        $data = unserialize($item->getAdditionalData());
        if(!is_array($data)) {
            $data = array();
        }

        $data[$key] = $value;

        $item->setAdditionalData(serialize($data));
        return $item;
    }
	
	public function changeConfigReturn($observer){
		$observerObject = $observer->getObject();
		$data = $observer->getObject()->getData();
		$data_return=$data['data'];
		$customsize_conf=array();
		$basicFieldset=Mage::getModel('simicustomsize/simicustomsize')->getFieldsFromFieldset('basic');
		$fieldset = Mage::getModel('simicustomsize/simicustomsize')->getFieldsets();
		
		$arr_fieldset=array();
		$arr_fields_basic=array();		
		if(sizeOf($basicFieldset)>0) {
			$arr_fields_basic=Mage::getModel('simicustomsize/simicustomsize')->getField('basic',$profile);
		}

		foreach($fieldset as $item){
			if($item->getTag()=='basic')
				continue;

			$arr_fieldset[]=array(
				'id' => $item->getId(),
				'label' => $item->getLabel(),
				'description' => $item->getDescription(),
				'fields' => Mage::getModel('simicustomsize/simicustomsize')->getField($item->getTag()),
			);
			
		}
		$is_enable=Mage::helper('simicustomsize')->checkPluginEnable();
		$customsize_conf['is_custom_size_enable']=$is_enable ? "1" : "0";
		$customsize_conf['field_config']=array(
			'basic_fieldset'=>$arr_fields_basic,
			'fieldset'=>$arr_fieldset,
		);
		
		
		$data_return[0]['customsize_config']=$customsize_conf;
		$data['data']=$data_return;
		$observerObject->setData($data);
	}
	
}