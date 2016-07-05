<?php

class Simi_Simibooking_ApiController extends Simi_Connector_Controller_Action
{
	public function indexAction(){
		$this->loadLayout();
		$this->renderLayout();
	}
	
	public function get_priceAction() {
        $data = $this->getData();
        $information = Mage::getModel('simibooking/simibooking')->update_price($data);
        $this->_printDataJson($information);
    }
}