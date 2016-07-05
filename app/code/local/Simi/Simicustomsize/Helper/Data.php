<?php

class Simi_Simicustomsize_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function checkPluginEnable(){
		if(Mage::helper('core')->isModuleEnabled('Yireo_Custsize')){
			return true;
		}
		
		return false;
	}
	
	//get Customer Id by email
	
	public function getCustomerIdByEmail($email){
		$websiteId = Mage::app()->getStore()->getWebsiteId();
		$customerModel=Mage::getModel('customer/customer');
		$customerModel->setWebsiteId($websiteId);
		$customer = $customerModel->getCollection()
					->addFieldToFilter('email', $email)
					->getFirstItem();
					
		return $customer->getId();
	}
}