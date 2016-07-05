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
 * @category 	Simi
 * @package 	Simi_Cloudconnector
 * @copyright 	Copyright (c) 2015 Magestore (http://www.magestore.com/)
 * @license 	http://www.magestore.com/license-agreement.html
 */

/**
 * Connector Config Controller
 * 
 * @category 	Simi
 * @package 	Simi_Cloudconnector
 * @author  	Simi Developer
 */
class Simi_Cloudconnector_DownloadController extends Simi_Cloudconnector_Controller_Action {

    /**
     * call api download file
     * 
     * @param    
     * @return   
     */
    public function linksAction() {
        $request = $this->getRequest();
        $linkId = $request->getParam('id');
        $userName = $request->getParam('user_name');
        $userPassword = $request->getParam('user_password');
        if(!$this->checkCustomerPurchased($userName, $userPassword)){
            echo 'Connect error!';
            header("HTTP/1.0 401 Unauthorized");
            exit();
        }
        $link = Mage::getModel('downloadable/link')->load($linkId);
        $resource = Mage::helper('downloadable/file')->getFilePath(
                    Mage_Downloadable_Model_Link::getBasePath(), $link->getLinkFile()
                );
        $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
        try {
            $this->_processDownload($resource, $resourceType);
            exit(0);
        }
        catch (Exception $e) {
        }        
    } 

    /**
     * check customer purchased downloadable product
     * 
     * @param    
     * @return   
     */
    public function checkCustomerPurchased($userName, $userPassword) {
        $customer = Mage::getModel('cloudconnector/customer');
        try{
            $customerId = $customer->loginCustomer($userName, $userPassword);
            return true;
        }catch(Exception $e){
            return false;
        }
        
        return false;
    } 

    /**
     * download file
     * 
     * @param   string, string 
     * @return   
     */
    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('downloadable/download');
        /* @var $helper Mage_Downloadable_Helper_Download */
        $helper->setResource($resource, $resourceType);
        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();
        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);
        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }
        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }
        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        session_write_close();
        $helper->output();
    }

}