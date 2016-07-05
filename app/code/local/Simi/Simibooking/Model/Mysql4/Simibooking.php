<?php

class Simi_Simibooking_Model_Mysql4_Simibooking extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct(){
		$this->_init('simibooking/simibooking', 'simibooking_id');
	}
}