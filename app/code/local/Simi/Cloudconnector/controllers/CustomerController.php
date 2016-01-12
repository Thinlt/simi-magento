<?php

/**
 * Magestore
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @copyright   Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Config Controller
 * 
 * @category    Simi
 * @package     Simi_Cloudconnector
 * @author      Simi Developer
 */
class Simi_Cloudconnector_CustomerController extends Simi_Cloudconnector_Controller_Action {

    /**
     * call api sign_in from client
     * 
     * @param    
     * @return   
     */
    public function sign_inAction() {
        $request = $this->getRequest();
        $userEmail = $request->getParam('user_email');
        $userPassword = $request->getParam('user_password');
        $information = Mage::getModel('cloudconnector/customer')->login($userEmail, $userPassword);
        $this->_printDataJson($information);
    }

    /**
     * call api update_user from client
     * 
     * @param    
     * @return   
     */
    public function update_userAction() {
        $data = $this->getRequest()->getParams();
        $information = Mage::getModel('cloudconnector/customer')->updateUser($data);
        $this->_printDataJson($information);
    }

}