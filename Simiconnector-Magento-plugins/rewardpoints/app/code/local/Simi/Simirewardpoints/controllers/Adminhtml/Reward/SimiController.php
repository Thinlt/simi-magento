<?php
/**
 * Simi
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Simi.com license that is
 * available through the world-wide-web at this URL:
 * http://www.simi.com/license-agreement.html
 * 
 * DISCLAIMER
 * 
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @copyright   Copyright (c) 2012 Simi (http://www.simi.com/)
 * @license     http://www.simi.com/license-agreement.html
 */

/**
 * Simirewardpoints Adminhtml Controller
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Adminhtml_Reward_SimiController extends Mage_Adminhtml_Controller_Action
{

    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('simirewardpoints/simicart');
    }
    
    public function indexAction(){
		$url = "https://www.simicart.com/usermanagement/checkout/buyProfessional/?extension=1&utm_source=simibuyer&utm_medium=backend&utm_campaign=Simi Buyer Backend";

		Mage::app()->getResponse()->setRedirect($url)->sendResponse();
		exit();
	}
}
