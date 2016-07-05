<?php

class Simi_Simicustomsize_ApiController extends Simi_Connector_Controller_Action {
	
	public function update_profileAction(){
		$data = $this->getData();
		$information = Mage::getModel('simicustomsize/simicustomsize')->saveProfile($data);
        $this->_printDataJson($information);
	}
	
	public function delete_profileAction(){
		$data = $this->getData();
		$information = Mage::getModel('simicustomsize/simicustomsize')->deleteProfile($data);
        $this->_printDataJson($information);
	}
}