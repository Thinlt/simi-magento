<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Vnecoms\Vendors\App\Response\Http;

class FileFactory extends \Magento\Backend\App\Response\Http\FileFactory
{
    /**
     * @var \Vnecoms\Vendors\Model\UrlInterface
     */
    protected $_backendUrl;

    /**
     * @var \Vnecoms\Vendors\Helper\Data
     */
    protected $_helper;

    /**
     *
     * @param \Magento\Framework\App\ResponseInterface $response
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Auth $auth
     * @param \Magento\Backend\Model\UrlInterface $backendUrl
     * @param \Magento\Backend\Model\Session $session
     * @param \Magento\Framework\App\ActionFlag $flag
     * @param \Magento\Backend\Helper\Data $helper
     * @param \Vnecoms\Vendors\Model\UrlInterface $vendorBackendUrl
     * @param \Vnecoms\Vendors\Helper\Data $vendorHelper
     */
    public function __construct(
        \Magento\Framework\App\ResponseInterface $response,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Auth $auth,
        \Magento\Backend\Model\UrlInterface $backendUrl,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\App\ActionFlag $flag,
        \Magento\Backend\Helper\Data $helper,
        \Vnecoms\Vendors\Model\UrlInterface $vendorBackendUrl,
        \Vnecoms\Vendors\Helper\Data $vendorHelper
    ) {
        $this->_backendUrl = $vendorBackendUrl;
        $this->_helper = $vendorHelper;
        parent::__construct($response, $filesystem, $auth, $backendUrl, $session, $flag, $helper);
    }
}
