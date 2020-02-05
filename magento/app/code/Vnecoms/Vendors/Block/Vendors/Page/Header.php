<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile

namespace Vnecoms\Vendors\Block\Vendors\Page;

use Magento\Framework\App\Filesystem\DirectoryList;

/**
 * Vendor header block
 */
class Header extends \Vnecoms\Vendors\Block\Vendors\AbstractBlock
{
    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData = null;

    /**
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $_authSession;

    /**
     * @var \Magento\MediaStorage\Helper\File\Storage\Database
     */
    protected $_fileStorageDatabase;
    
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $mediaDirectory;
    
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Backend\Helper\Data $backendData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Vnecoms\Vendors\Model\UrlInterface $url,
        \Magento\MediaStorage\Helper\File\Storage\Database $fileStorageDatabase,
        array $data = []
    ) {
        $this->_fileStorageDatabase = $fileStorageDatabase;
        $this->_mediaDirectory = $context->getFilesystem()->getDirectoryRead(DirectoryList::MEDIA);
        parent::__construct($context,$url, $data);
    }

    /**
     * @return string
     */
    public function getHomeLink()
    {
        return $this->getUrl($this->_urlBuilder->getStartupPageUrl());
    }

    /**
     * @return \Magento\User\Model\User|null
     */
    public function getUser()
    {
        return $this->_authSession->getUser();
    }

    /**
     * @return string
     */
    public function getLogoutLink()
    {
        return $this->getUrl('adminhtml/auth/logout');
    }

    /**
     * Check if noscript notice should be displayed
     *
     * @return boolean
     */
    public function displayNoscriptNotice()
    {
        return $this->_scopeConfig->getValue('web/browser_capabilities/javascript', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
    
    /**
     * Get logo Src
     * 
     * @return string
     */
    public function getLogoSrc(){
        $scopeConfig = $this->_scopeConfig->getValue(
            'vendors/design/logo',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        $path = 'ves_vendors/logo/' . $scopeConfig;
        $logoUrl = $this->_storeManager->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
        
        if ($scopeConfig !== null && $this->checkIsFile($path)) {
            return $logoUrl;
        }
        
        return $this->getViewFileUrl('images/logo.png');
    }
    
    /**
     * Get logo Src
     *
     * @return string
     */
    public function getLogoIconSrc(){
        $scopeConfig = $this->_scopeConfig->getValue(
            'vendors/design/logo_icon',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    
        $path = 'ves_vendors/logo_icon/' . $scopeConfig;
        $logoUrl = $this->_storeManager->getStore()
        ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA) . $path;
    
        if ($scopeConfig !== null && $this->checkIsFile($path)) {
            return $logoUrl;
        }
    
        return $this->getViewFileUrl('images/logo-icon.png');
    }
    
    /**
     * If DB file storage is on - find there, otherwise - just file_exists
     *
     * @param string $filename relative file path
     * @return bool
     */
    protected function checkIsFile($filename)
    {
        if ($this->_fileStorageDatabase->checkDbUsage() && !$this->_mediaDirectory->isFile($filename)) {
            $this->_fileStorageDatabase->saveFileToFilesystem($filename);
        }
        return $this->_mediaDirectory->isFile($filename);
    }
}
