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
 * Simirewardpoints Policy Block, Render CMS page to content of policy action
 * 
 * @category    Simi
 * @package     Simi_Simirewardpoints
 * @author      Simi Developer
 */
class Simi_Simirewardpoints_Block_Account_Policy extends Simi_Simirewardpoints_Block_Template
{
    protected function _construct()
    {
        parent::_construct();
        $page = $this->getPage();
        if ($pageId = $this->getPageId()) {
            $page->setStoreId(Mage::app()->getStore()->getId())
                ->load($pageId);
        }
    }
    
    /**
     * get Policy CMS Page ID
     * 
     * @return int
     */
    public function getPageId()
    {
        $page = $this->getPage();
        $identifier = Mage::getStoreConfig(Simi_Simirewardpoints_Helper_Policy::XML_PATH_POLICY_PAGE);
        $pageId = $page->checkIdentifier($identifier, Mage::app()->getStore()->getId());
        if (!$pageId) {
            $idArray = explode('|', $identifier);
            if (count($idArray) > 1) {
                return end($idArray);
            }
        }
        return $pageId;
    }
    
    /**
     * get cms page model
     * 
     * @return Mage_Cms_Model_Page
     */
    public function getPage()
    {
        return Mage::getSingleton('cms/page');
    }
    
    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        $helper     = Mage::helper('cms');
        $processor  = $helper->getPageTemplateProcessor();
        
        $html   = $this->getMessagesBlock()->getGroupedHtml();
        if ($pageHeading = $this->getChild('page_content_heading')) {
            $pageHeading->setContentHeading($this->getPage()->getContentHeading());
            $html .= $pageHeading->toHtml();
        }
        $html .= $processor->filter($this->getPage()->getContent());
        return $html;
    }
}
