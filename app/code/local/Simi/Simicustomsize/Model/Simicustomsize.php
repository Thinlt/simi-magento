<?php

class Simi_Simicustomsize_Model_Simicustomsize extends Simi_Connector_Model_Abstract 
{
	
	//get All profiles customer
	public function getProfiles($customerId)
    {
        static $profiles = null;
        if(empty($profiles)) {   
            $profiles = Mage::getModel('custsize/profile')->getCollection()->addFieldToFilter('customer_id', $customerId);;
        }
        return $profiles;
    }
	//get All profiles with default
	public function getDefaultProfile($customerId){
		
		$profiles = $this->getProfiles($customerId);
        if(!empty($profiles)) {
            foreach($profiles as $profile) {
                if($profile->getData('default') == 1) {
                    $profile->load($profile->getId());
                    return $profile;
                }
            }
        }
        return null;
	}
	//get All profiles without default
	public function getOtherProfiles($customerId)
    {
        $profiles = $this->getProfiles();
        $rs = array();
        if(!empty($profiles)) {
            foreach($profiles as $profile) {
                if($profile->getData('default') == 0) {
                    $rs[] = $profile;
                }
            }
        }
        return $rs;
    }
	//get Dashboard fields
	public function getDashboardFields()
    {
        $fields = Mage::getModel('custsize/profile_field')->getCollection()
            ->addFieldToFilter('enabled', 1)
            ->addFieldToFilter('dashboard', 1)
        ;
        return $fields;
    }
	
	public function getFieldsFromFieldset($fieldset = 'basic')
    {
        $fields = Mage::getModel('custsize/profile_field')->getCollection()
            ->addFieldToFilter('fieldset', $fieldset)
            ->addFieldToFilter('enabled', 1)
            ->setOrder('ordering', 'ASC')
            ->load()
        ;

        return $fields;
    }

    /**
     * Fetch all enabled fieldsets
     */
    public function getFieldsets()
    {
        $collection = Mage::getModel('custsize/profile_fieldset')->getCollection()
            ->addFieldToFilter('enabled', 1)
            ->setOrder('ordering', 'ASC')
            ->load()
        ;
        return $collection;
    }
	
	public function saveProfile($data){
		$profile_parram = $data->profile;
		$fieldset_parram = $data->fields;
		
		$information=$this->statusSuccess();
		$customer_id=Mage::helper('simicustomsize')->getCustomerIdByEmail($data->user_email);
		$profile_id=isset($profile_parram->id) && $profile_parram->id != ""? $profile_parram->id : 0;
		$profileModel=Mage::getModel('custsize/profile')->load($profile_id);
		
		$profileModel->setData('name', $profile_parram->name);
		$profileModel->setData('description', $profile_parram->description);
		$profileModel->setData('default', (isset($profile_parram->is_default)) ? $profile_parram->is_default : 0);
		$profileModel->setData('customer_id', $customer_id);
		
		if($profileModel->getData('default')) {
			$resource = Mage::getSingleton('core/resource');
			$writeConnection = $resource->getConnection('core_write');
			$tableName = $resource->getTableName('custsize_profile');
			$query = "UPDATE {$tableName} SET `default` = 0 WHERE `customer_id` = " . $customer_id;
			$writeConnection->query($query);
        }
		
		try {
			$profileModel->save();
		} catch (Exception $e) {
			 $information = $this->statusError(array($e->getMessage()));
		}
		$fieldData=array();
		foreach($fieldset_parram as $item){
			$fieldData[$item->id]=$item->value;
		}
		
		$result = $profileModel->saveFields($fieldData, $profileModel->getProfileId());
		
		if($result){
			$information['message'] = array("SUCCESS"); 
			$information['data'] = $this->getLisProfiles($customer_id); 
		}else{
			$information = $this->statusError(array("NO DATA"));
		}
		
		return $information;
	}
	
	public function getLisProfiles($customerId){
		$customer_profiles=array();
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
						$arr_fields_basic=$this->getField('basic',$profile);
					}
					
					foreach($fieldset as $item){
						if($item->getTag()=='basic')
							continue;
						
						$arr_fieldset[]=array(
							'id' => $item->getId(),
							'label' => $item->getLabel(),
							'description' => $item->getDescription(),
							'fields' => $this->getField($item->getTag(),$profile),
						);
						
					}
					
					$arr_attr['fieldset']=$arr_fieldset;
					$arr_attr['basic_fieldset']=$arr_fields_basic;
					$customer_profiles[]=$arr_attr;
				}
			}
			return $customer_profiles;
	}
	public function getField($tag,$profile=null){
		$arr_fields=array();
		$formFieldset=Mage::getModel('simicustomsize/simicustomsize')->getFieldsFromFieldset($tag);
		foreach($formFieldset as $form){
			$arr_fields[]=array(
				'id'=>$form->getId(),
				'label'=>$form->getLabel(),
				'value'=> $profile != null && $profile->getData('field'.$form->getId()) != null ? $profile->getData('field'.$form->getId()) : "",
				'is_required'=>$form->getRequired() !=null ? (string)$form->getRequired() :"0",
				'description'=>$form->getDescription(),
				'is_dashboard'=>$form->getDashboard()!= null ? (string)$form->getDashboard() : "0",
				'unit'=>$form->getUnit(),
				'marker'=>$form->getMarker(),
			);
			
		}
		
		return $arr_fields;				
	}
	
	public function deleteProfile($data){
		$profile_id = $data->id;
		$information=$this->statusSuccess();
		$customerId=Mage::helper('simicustomsize')->getCustomerIdByEmail($data->user_email);
		$profileModel=Mage::getModel('custsize/profile')->load($profile_id);
		if($profileModel->getId()){
			try{
				$profileModel->delete();
				$information['message']=array("SUCCESS");
				$information['data']=$this->getLisProfiles($customerId);
			}catch(Exception $e){
				$information = $this->statusError(array($e->getMessage()));
			}
			
		}else{
			$information = $this->statusError(array(Mage::helper()->__('This profile does not exist')));
		}
		return $information;
	}
}