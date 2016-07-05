<?php

/**
 * Created by PhpStorm.
 * User: Max
 * Date: 4/15/16
 * Time: 11:30 AM
 */
class Simi_Connector_BrandController extends Simi_Connector_Controller_Action
{
    public function get_list_brandsAction(){
        $data = $this->getData();
        $information = Mage::getModel('connector/brand_customize')->getBrandOfHome($data);
        $this->_printDataJson($information);
    }
}