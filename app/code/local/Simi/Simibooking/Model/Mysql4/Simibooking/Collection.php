<?php

class Simi_Simibooking_Model_Mysql4_Simibooking_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct(){
		parent::_construct();
		$this->_init('simibooking/simibooking');
	}
}